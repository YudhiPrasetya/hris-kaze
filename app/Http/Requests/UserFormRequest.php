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
 * @file   UserFormRequest.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\Requests;

use App\Http\Requests\FormRequestInterface;
use App\Models\User;
use App\Traits\Request as RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;


class UserFormRequest extends FormRequest implements FormRequestInterface {
	use RequestTrait;


	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		if ($this->isSubmitted()) {
			$user = User::where(['username' => Request::input('username')])->first();

			return [
				'username'          => $user ? "required|unique:users,username,{$user->id}|max:255" : "required|unique:users,username|max:255",
				'email'             => $user ? "required|unique:users,email,{$user->id}|email" : "required|unique:users,email|email",
				'name'              => 'required|min:3',
				'profile_photo_url' => 'nullable',
				'password'          => 'nullable|required_with:password_confirmation|string|confirmed',
				'password_confirm'  => 'nullable|password|same:password',
			];
		}

		return [];
	}
}
