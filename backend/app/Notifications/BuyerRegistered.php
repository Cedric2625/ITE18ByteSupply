<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BuyerRegistered extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database']; // add 'mail' if mail is configured
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ByteSupply')
            ->greeting('Hello '.$notifiable->buyer_name.'!')
            ->line('Your customer account has been created successfully.')
            ->action('Start Shopping', url('/shop'))
            ->line('Thank you for choosing ByteSupply.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your customer account has been created successfully.',
        ];
    }
}


