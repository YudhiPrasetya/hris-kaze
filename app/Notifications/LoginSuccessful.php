<?php
/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   LoginSuccessful.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;


class LoginSuccessful extends Notification {
	use Queueable;


	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 *
	 * @return array
	 */
	public function via($notifiable) {
		return [/*'slack', 'mail'*/];
		//return [];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param mixed $notifiable
	 *
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		return (new MailMessage)
			->from('admin@omnity.com')
			->line('The introduction to the notification.')
			->action('Notification Action', url('/'))
			->line('Thank you for using our application!');
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param mixed $notifiable
	 *
	 * @return array
	 */
	public function toArray($notifiable) {
		return [
			//
		];
	}

	public function toSlack($notifiable) {
		return (new SlackMessage())
			->success()
			->content('User ' . $notifiable->slackLink() . ', login to <' . env('APP_URL') . '|' . env('APP_URL') . '>');
	}
}
