<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Commission; // Import the Commission model
use App\Models\SalonSetting;

class AppointmentObserver
{
    public function creating(Appointment $appointment)
    {
        if (!$appointment->end_datetime) {
            $duration = Service::find($appointment->service_id)->duration_minutes ?? 60;
            $appointment->end_datetime = $appointment->start_datetime->copy()->addMinutes($duration);
        }
    }

    public function created(Appointment $appointment)
    {
        // Payment should be created after service completion, not automatically
        // Removed automatic payment creation per defense feedback
    }
    private function createCommission(Appointment $appointment)
    {
        // Check if commission already exists
        $existingCommission = Commission::where('appointment_id', $appointment->id)->first();
        if ($existingCommission) {
            return $existingCommission;
        }

        // Get salon settings for default commission rate
        $salonSettings = SalonSetting::getSettings();
        $commissionRate = $salonSettings->default_commission_rate;

        // Calculate commission amount
        $commissionAmount = ($appointment->total_amount * $commissionRate) / 100;

        // Create commission record
        return Commission::create([
            'staff_id' => $appointment->staff_id,
            'appointment_id' => $appointment->id,
            'service_amount' => $appointment->total_amount,
            'commission_rate' => $commissionRate,
            'amount' => $commissionAmount,
            'status' => 'pending',
            'notes' => 'Commission for completed appointment'
        ]);
    }

    public function updated(Appointment $appointment)
    {
        // When appointment is marked as completed
        if ($appointment->isDirty('status') && $appointment->status === 'completed') {
            $customer = Customer::find($appointment->customer_id);
            if ($customer) {
                $customer->increment('total_visits');
                $customer->increment('total_spent', $appointment->total_amount);
                $customer->update(['last_visit' => $appointment->start_datetime]);
            }

            // Commission will be created when payment is marked as paid (in PaymentController)
            // Payment should be done after service completion, not automatically
        }

        // When appointment is cancelled or failed
        if ($appointment->isDirty('status') && in_array($appointment->status, ['cancelled', 'failed'])) {
            // Update payment status if exists
            $payment = Payment::where('appointment_id', $appointment->id)->first();
            if ($payment) {
                if ($appointment->status === 'cancelled') {
                    $payment->update(['status' => 'cancelled']);
                } elseif ($appointment->status === 'failed') {
                    $payment->update(['status' => 'failed']);
                }
            }

            // Cancel any pending commission
            Commission::where('appointment_id', $appointment->id)
                ->pending()
                ->update(['status' => 'cancelled']);

            // Log the cancellation
            activity()
                ->performedOn($appointment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'reason' => $appointment->cancellation_reason,
                    'status' => $appointment->status
                ])
                ->log("Appointment {$appointment->status}");
        }
    }
}