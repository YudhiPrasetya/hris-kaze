<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class FingerPrintDeviceFormRequest extends FormRequest implements FormRequestInterface {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return Request::user()->canAny(['fingerprintdevice.edit', 'fingerprintdevice.update', 'fingerprintdevice.store', 'fingerprintdevice.create']);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			//
		];
	}
}
