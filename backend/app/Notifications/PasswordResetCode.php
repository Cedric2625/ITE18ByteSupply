<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetCode extends Notification
{
	use Queueable;

	public function __construct(private string $code)
	{
	}

	public function via(object $notifiable): array
	{
		return ['mail'];
	}

	public function toMail(object $notifiable): MailMessage
	{
		return (new MailMessage)
			->subject('Your ByteSupply password reset code')
			->greeting('Password reset request')
			->line('Use the 6-digit code below to continue your password reset.')
			->line('Code: ' . $this->code)
			->line('This code will expire in 10 minutes. If you did not request this, you can ignore this email.');
	}
}
