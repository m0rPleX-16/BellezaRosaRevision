<?php
// [file name]: PaymentController.php
namespace App\Http\Controllers;

use App\Models\Payment;
use App\Notifications\PaymentStatusUpdated;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Commission;
use App\Models\SalonSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        // Staff can only view payments for their own appointments
        $query = Payment::with(['appointment.service', 'appointment.staff', 'customer'])
            ->whereHas('appointment', function ($q) {
                $q->whereHas('customer')->whereHas('service');
            });

        if (auth()->user()->isStaff() && auth()->user()->staff) {
            $query->whereHas('appointment', function ($q) {
                $q->where('staff_id', auth()->user()->staff->id);
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate payment statistics
        $totalPayments = Payment::count();
        $paidPayments = Payment::where('status', 'paid')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $failedPayments = Payment::where('status', 'failed')->count();

        return view('dashboard.payments.index', compact('payments', 'totalPayments', 'paidPayments', 'pendingPayments', 'failedPayments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['appointment.service', 'appointment.staff.user', 'customer']);
        return view('dashboard.payments.show', compact('payment'));
    }

    public function createForAppointment($appointment)
    {
        // If it's not an instance of Appointment, try to find it
        if (!$appointment instanceof Appointment) {
            $appointment = Appointment::findOrFail($appointment);
        }

        // Payment can only be created after service is completed
        if ($appointment->status !== 'completed') {
            return redirect()->route('dashboard.appointments.index')
                ->with('error', 'Payment can only be processed after the service is completed.');
        }

        // Check if payment already exists
        if ($appointment->payment) {
            return redirect()->route('dashboard.payments.show', $appointment->payment)
                ->with('info', 'A payment already exists for this appointment.');
        }

        return view('dashboard.payments.create', compact('appointment'));
    }

    /**
     * Validate payment data alignment
     */
    protected function validatePaymentData($appointment, $requestData)
    {
        $currentDateTime = now();
        $appointmentDate = $appointment->start_datetime;

        // Check if trying to mark as paid before appointment date
        if (isset($requestData['status']) && $requestData['status'] === 'paid' && $currentDateTime->lt($appointmentDate)) {
            $formattedDate = $appointmentDate->format('M j, Y g:i A');
            return [
                'success' => false,
                'message' => "Cannot mark payment as paid before the appointment date ($formattedDate)."
            ];
        }

        // Check if payment amount matches appointment total
        if (isset($requestData['amount']) && $appointment->total_amount != $requestData['amount']) {
            return [
                'success' => false,
                'message' => 'Payment amount does not match the appointment total.'
            ];
        }

        // Check if appointment is assigned to a staff member
        if (!$appointment->staff_id) {
            return [
                'success' => false,
                'message' => 'Appointment must be assigned to a staff member before payment.'
            ];
        }

        // Check if appointment has services
        if (!$appointment->service_id) {
            return [
                'success' => false,
                'message' => 'Appointment must have a service assigned before payment.'
            ];
        }

        return ['success' => true];
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'method' => 'required|in:cash,gcash,bank_transfer,online',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'payment_details' => 'nullable|array',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,paid'
        ]);

        $appointment = Appointment::with(['payment', 'service', 'staff'])->findOrFail($request->appointment_id);

        // Payment can only be created after service is completed
        if ($appointment->status !== 'completed') {
            return back()->with('error', 'Payment can only be processed after the service is completed.');
        }

        // Check if payment already exists and is already paid
        if ($appointment->payment && $appointment->payment->isPaid()) {
            return back()->with('error', 'This appointment already has a paid payment.');
        }

        // Validate payment data alignment
        $validation = $this->validatePaymentData($appointment, $request->all());
        if (!$validation['success']) {
            return back()->with('error', $validation['message']);
        }

        try {
            DB::beginTransaction();

            $paymentData = [
                'appointment_id' => $appointment->id,
                'customer_id' => $appointment->customer_id,
                'amount' => $request->amount,
                'method' => $request->input('method'),
                'reference_number' => $request->reference_number,
                'payment_details' => $request->payment_details ?? [],
                'status' => $request->status,
                'notes' => $request->notes,
                'paid_at' => $request->status === 'paid' ? now() : null
            ];

            if ($appointment->payment) {
                // Update existing payment
                $payment = $appointment->payment;
                $payment->update($paymentData);
            } else {
                // Create new payment
                $payment = Payment::create($paymentData);
            }

            // Update appointment payment method
            $appointment->update([
                'payment_method' => $request->input('method')
            ]);

            // If payment is marked as paid, create commission
            if ($request->status === 'paid') {
                $this->createCommission($appointment, $payment);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!',
                'payment_id' => $payment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment recording failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment. Please try again.'
            ], 500);
        }
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,failed,refunded',
            'reference_number' => 'nullable|string|max:100',
            'cancellation_reason' => 'required_if:status,failed,refunded|nullable|string|max:255'
        ]);

        $appointment = $payment->appointment->load('service', 'staff');

        // If payment is being marked as paid, ensure it's not before the appointment date
        if ($request->status === 'paid' && now()->lt($appointment->start_datetime)) {
            $formattedDate = $appointment->start_datetime->format('M j, Y g:i A');
            return back()->with('error', "Cannot mark payment as paid before the appointment date ($formattedDate).");
        }

        // Only allow updating payment status to paid if the appointment is completed
        if ($request->status === 'paid' && $appointment->status !== 'completed') {
            return back()->with('error', 'Cannot mark payment as paid because the appointment is not yet completed.');
        }

        DB::transaction(function () use ($request, $payment) {
            $oldStatus = $payment->status;
            $newStatus = $request->status;

            // Update payment status properly
            $updateData = [
                'status' => $newStatus,
                'reference_number' => $request->reference_number ?? $payment->reference_number,
            ];

            // Set paid_at when status changes to paid
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                $updateData['paid_at'] = now();
            } elseif ($newStatus !== 'paid') {
                // Clear paid_at if status changes from paid to something else
                $updateData['paid_at'] = null;
            }

            // Update notes if provided
            if ($request->notes) {
                $updateData['notes'] = $request->notes;
            }

            $payment->update($updateData);
            
            // If status changed, notify the customer
            if ($oldStatus !== $newStatus) {
                $message = "Your payment status has been updated to: " . ucfirst($newStatus);
                $payment->customer->notify(new PaymentStatusUpdated($payment, $message));
            }

            // Update appointment status based on payment status
            $appointment = $payment->appointment;
            if ($newStatus === 'paid') {
                // Payment is paid - mark appointment as completed if service was done
                if (in_array($appointment->status, ['in_progress', 'completed'])) {
                    $appointment->update(['status' => 'completed']);
                }

                // Create commission when payment is marked as paid
                $this->createCommission($appointment, $payment);
            } elseif ($newStatus === 'failed') {
                // Payment failed - add reason to notes
                if ($request->cancellation_reason) {
                    $currentNotes = $appointment->notes ? $appointment->notes . "\n" : '';
                    $appointment->update([
                        'notes' => $currentNotes . "Payment failed: " . $request->cancellation_reason
                    ]);
                }
            } elseif ($newStatus === 'refunded') {
                // Payment refunded - requires reason
                if ($request->cancellation_reason) {
                    $currentNotes = $appointment->notes ? $appointment->notes . "\n" : '';
                    $appointment->update([
                        'notes' => $currentNotes . "Payment refunded: " . $request->cancellation_reason
                    ]);
                }
            }
        });

        return back()->with('success', 'Payment status updated successfully!');
    }

    public function edit(Payment $payment)
    {
        $payment->load(['appointment', 'customer']);
        return view('dashboard.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'method' => 'required|in:cash,gcash,bank_transfer,online',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'payment_details' => 'nullable|array',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string'
        ]);

        $appointment = $payment->appointment->load('service', 'staff');

        // Validate payment data alignment
        $validation = $this->validatePaymentData($appointment, $request->all());
        if (!$validation['success']) {
            return back()->with('error', $validation['message']);
        }

        // If payment is being marked as paid, ensure it's not before the appointment date
        if ($request->status === 'paid' && now()->lt($appointment->start_datetime)) {
            $formattedDate = $appointment->start_datetime->format('M j, Y g:i A');
            return back()->with('error', "Cannot mark payment as paid before the appointment date ($formattedDate).");
        }

        // Only allow updating payment status to paid if the appointment is completed
        if ($request->status === 'paid' && $appointment->status !== 'completed') {
            return back()->with('error', 'Cannot mark payment as paid because the appointment is not yet completed.');
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            $payment->update([
                'method' => $request->input('method'),
                'amount' => $request->amount,
                'reference_number' => $request->reference_number,
                'payment_details' => $request->payment_details ?? [],
                'status' => $request->status,
                'notes' => $request->notes,
                'paid_at' => $request->status === 'paid' ? now() : null
            ]);

            // If payment is marked as paid, ensure commission is created
            if ($request->status === 'paid') {
                if ($appointment->status === 'completed') {
                    $appointment->update(['status' => 'completed']);
                    $this->createCommission($appointment, $payment);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update payment: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.payments.show', $payment)
            ->with('success', 'Payment updated successfully!');
    }

    public function createCommission(Appointment $appointment, Payment $payment)
    {
        // Check if commission already exists for this appointment
        $existingCommission = Commission::where('appointment_id', $appointment->id)->first();
        if ($existingCommission) {
            return; // Commission already created
        }

        $salonSettings = SalonSetting::first();
        // Check both commission_rate and default_commission_rate for backward compatibility
        $commissionRate = $salonSettings->commission_rate ?? $salonSettings->default_commission_rate ?? null;
        
        if (!$salonSettings || !$commissionRate) {
            return; // No commission rate set
        }

        $commissionAmount = ($commissionRate / 100) * $payment->amount;

        Commission::create([
            'appointment_id' => $appointment->id,
            'staff_id' => $appointment->staff_id,
            'service_amount' => $payment->amount,
            'commission_rate' => $commissionRate,
            'amount' => $commissionAmount,
            'status' => 'pending'
        ]);
    }
}