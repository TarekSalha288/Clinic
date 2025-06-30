<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $dateToRemindAbout;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($dateToRemindAbout)
    {
        $this->dateToRemindAbout = $dateToRemindAbout;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('تذكير هام!')
                    ->line('لديك موعد قادم بتاريخ: ' . $this->dateToRemindAbout->format('Y-m-d H:i'))
                    ->action('عرض التفاصيل', url('/your-event-details-url'))
                    ->line('شكرا لاستخدامك خدمتنا!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'You have an appointment at '  . $this->dateToRemindAbout->format('Y-m-d H:i'),
            'date' => $this->dateToRemindAbout->toDateTimeString(),
        ];
    }
}
