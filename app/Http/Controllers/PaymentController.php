<?php
// [file name]: PaymentController.php
namespace App\Http\Controllers;

use App\Models\Payment;
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
        $query = Payment::with(['appointment.service', 'appointment.staff', 'customer']);
        
        if (auth()->user()->isStaff() && auth()->user()->staff) {
            $query->whereHas('appointment', function($q) {
                $q->where('staff_id', auth()->user()->staff->id);
            });
        }
        
        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('dashboard.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['appointment.service', 'appointment.staff.user', 'customer']);
        return view('dashboard.payments.show', compact('payment'));
    }

    public function createForAppointment(Appointment $appointment)
    {
        // Payment can only be created after service is completed
        if ($appointment->status !== 'completed') {
            return redirect()->route('dashboard.appointments.index')
                ->with('error', 'Payment can only be processed after the service is completed.');
        }

        return view('dashboard.payments.create', compact('appointment'));
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

    $appointment = Appointment::findOrFail($request->appointment_id);

    // Payment can only be created after service is completed
    if ($appointment->status !== 'completed') {
        return back()->withErrors([
            'appointment_id' => 'Payment can only be processed after the service is completed. Please mark the appointment as completed first.'
        ]);
    }

    // Check if payment already exists
    $existingPayment = Payment::where('appointment_id', $appointment->id)->first();
    
    DB::transaction(function () use ($request, $appointment, $existingPayment) {
        $paymentData = [
            'amount' => $request->amount,
            'method' => $request->method(),
            'reference_number' => $request->reference_number,
            'payment_details' => $request->payment_details,
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        // Set paid_at when status is paid
        if ($request->status === 'paid') {
            $paymentData['paid_at'] = now();
        }

        if ($existingPayment) {
            // Update existing payment
            $existingPayment->update($paymentData);
            $payment = $existingPayment;
        } else {
            // Create new payment
            $paymentData['appointment_id'] = $appointment->id;
            $paymentData['customer_id'] = $appointment->customer_id;
            $payment = Payment::create($paymentData);
        }

        // Update appointment payment method
        $appointment->update([
            'payment_method' => $request->method()
        ]);
        
        // If payment is marked as paid, ensure appointment stays completed and create commission
        if ($request->status === 'paid') {
            if ($appointment->status !== 'completed') {
                $appointment->update(['status' => 'completed']);
            }
            
            // Create commission for staff when payment is paid
            $this->createCommission($appointment, $payment);
        }
    });

    return redirect()->route('dashboard.payments.index')
        ->with('success', 'Payment recorded successfully!');
}

    public function updateStatus(Request $request, Payment $payment)
{
    $request->validate([
        'status' => 'required|in:pending,paid,failed,refunded',
        'reference_number' => 'nullable|string|max:100',
        'cancellation_reason' => 'required_if:status,failed,refunded|nullable|string|max:255'
    ]);

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
            'method' => 'required|in:cash,gcash,bank_transfer',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string'
        ]);

        $oldStatus = $payment->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($request, $payment, $oldStatus, $newStatus) {
            $payment->update([
                'method' => $request->method(),
                'amount' => $request->amount,
                'reference_number' => $request->reference_number,
                'status' => $newStatus,
                'paid_at' => $newStatus === 'paid' ? now() : null,
                'notes' => $request->notes
            ]);

            // Create commission when payment is marked as paid
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                $appointment = $payment->appointment;
                if ($appointment && $appointment->status !== 'completed') {
                    $appointment->update(['status' => 'completed']);
                }
                $this->createCommission($appointment, $payment);
            }
        });

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
        if (!$salonSettings || !$salonSettings->commission_rate) {
            return; // No commission rate set
        }

        $commissionAmount = ($salonSettings->commission_rate / 100) * $payment->amount;

        Commission::create([
            'appointment_id' => $appointment->id,
            'staff_id' => $appointment->staff_id,
            'amount' => $commissionAmount,
            'status' => 'unpaid'
        ]);
    }
}