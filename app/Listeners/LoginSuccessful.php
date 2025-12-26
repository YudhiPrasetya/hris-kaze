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

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;
use App\Notifications\LoginSuccessful as LoginSuccessfulNotification;


class LoginSuccessful {
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param Login $event
	 *
	 * @return void
	 */
	public function handle(Login $event) {
		/**
		 * @var \App\Models\User
		 */
		$user = $event->user;
		$user->notify(new LoginSuccessfulNotification());

		Session::flash('alert', 'info');
		Session::flash('message', 'Hello ' . $user->name . ', welcome back!');
		// Update last login
		$event->user->last_login = new \DateTime();
	}
}
