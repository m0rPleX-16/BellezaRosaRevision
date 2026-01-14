<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;
    public $message;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, string $status, string $message = '')
    {
        $this->appointment = $appointment;
        $this->status = $status;
        $this->message = $message ?: $this->getDefaultMessage($status);
    }

    /**
     * Get default message based on status
     */
    protected function getDefaultMessage(string $status): string
    {
        $messages = [
            'scheduled' => 'Your appointment has been scheduled successfully.',
            'confirmed' => 'Your appointment has been confirmed.',
            'completed' => 'Your appointment has been marked as completed.',
            'cancelled' => 'Your appointment has been cancelled.',
            'rescheduled' => 'Your appointment has been rescheduled.',
            'no_show' => 'You were marked as a no-show for your appointment.',
        ];

        return $messages[strtolower($status)] ?? 'Your appointment status has been updated.';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment->load('service', 'staff', 'customer');
        
        $subject = match($this->status) {
            'scheduled' => 'Appointment Scheduled: ' . $appointment->service->name,
            'confirmed' => 'Appointment Confirmed: ' . $appointment->service->name,
            'completed' => 'Appointment Completed: ' . $appointment->service->name,
            'cancelled' => 'Appointment Cancelled: ' . $appointment->service->name,
            'rescheduled' => 'Appointment Rescheduled: ' . $appointment->service->name,
            'no_show' => 'Missed Appointment: ' . $appointment->service->name,
            default => 'Appointment Update: ' . $appointment->service->name
        };
        
        return (new MailMessage)
            ->subject($subject)
            ->view('emails.appointment-status-updated', [
                'appointment' => $appointment,
                'status' => $this->status,
                'message' => $this->message,
                'notifiable' => $notifiable,
                'actionUrl' => $appointment->customer && $appointment->customer->user 
                    ? route('customer.appointments.show', $appointment->id)
                    : route('dashboard.appointments.show', $appointment->id)
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'service_name' => $this->appointment->service->name,
            'status' => $this->status,
            'message' => $this->message,
            'appointment_date' => $this->appointment->start_datetime->format('M j, Y g:i A'),
            'staff_name' => $this->appointment->staff->name ?? 'Not Assigned',
            'amount' => $this->appointment->total_amount,
            'type' => 'appointment_status_update',
            'action_url' => $this->appointment->customer && $this->appointment->customer->user 
                ? route('customer.appointments.show', $this->appointment->id)
                : route('dashboard.appointments.show', $this->appointment->id)
        ];
    }
}
