<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, string $message)
    {
        $this->payment = $payment;
        $this->message = $message;
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
        $appointment = $this->payment->appointment->load('service', 'staff');
        
        $subject = match($this->payment->status) {
            'paid' => 'Payment Confirmed - ' . config('app.name'),
            'failed' => 'Payment Failed - ' . config('app.name'),
            'refunded' => 'Payment Refunded - ' . config('app.name'),
            default => 'Payment Status Updated - ' . config('app.name')
        };
        
        return (new MailMessage)
            ->subject($subject)
            ->view('emails.payment-status-updated', [
                'payment' => $this->payment,
                'appointment' => $appointment,
                'notifiable' => $notifiable,
                'status' => $this->payment->status,
                'message' => $this->message,
                'actionUrl' => route('dashboard.payments.show', $this->payment->id)
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $appointment = $this->payment->appointment;
        $service = $appointment->service;
        
        return [
            'payment_id' => $this->payment->id,
            'appointment_id' => $appointment->id,
            'message' => $this->message,
            'details' => [
                'service' => $service->name,
                'staff' => $appointment->staff->name ?? 'No staff assigned',
                'date' => $appointment->start_datetime->format('M j, Y h:i A'),
                'duration' => $service->duration . ' minutes',
                'amount' => '₱' . number_format($this->payment->amount, 2),
                'status' => ucfirst($this->payment->status),
                'reference_number' => $this->payment->reference_number
            ],
            'url' => route('dashboard.appointments.show', $appointment),
            'payment_url' => route('dashboard.payments.show', $this->payment)
        ];
    }
}
