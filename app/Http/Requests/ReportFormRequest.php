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
 * @file   ReportFormRequest.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\Requests;

use App\Http\Requests\FormRequestInterface;
use App\Traits\Request;
use Illuminate\Foundation\Http\FormRequest;


class ReportFormRequest extends FormRequest implements FormRequestInterface {
	use Request;


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
		return [];
	}
}
