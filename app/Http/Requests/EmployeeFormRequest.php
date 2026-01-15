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

use App\Models\Employee;
use App\Traits\Request as RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;


class EmployeeFormRequest extends FormRequest implements FormRequestInterface {
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
			$employee = Employee::where(['nik' => Request::input('nik')])->first();

			return [
				'nik'               => $employee ? "required|unique:employees,id,{$employee->id}|max:255" : "required|unique:employees,nik|max:255",
				'user_id'           => $employee ? "nullable|unique:employees,user_id,{$employee->user_id}" : "nullable|unique:employees,user_id",
				'name'              => 'required',
                'personal_email'    => 'nullable',
				'address'           => 'nullable',
                'birth_date'        => 'nullable',
                'effective_since'   => 'required',
                'leave_allowance'   => 'nullable',
                'gender_id'         => 'required',
                'position_id'       => 'nullable',
                'working_shift_id'  => 'required',
                'job_title_id'      => 'required',
                'country_id'        => 'nullable',
                'state_id'          => 'nullable',
                'city_id'           => 'nullable',
                'district_id'       => 'nullable',
                'village_id'        => 'nullable',
                'postal_code'       => 'nullable',
                'street'            => 'nullable',
                'currency_code'     => 'nullable',
                'basic_salary'      => 'required',
                'meal_allowance'    => 'nullable',
                'other_allowance'   => 'nullable',
                'attendance_premium' => 'nullable',
                'overtime'          => 'nullable',
                'marital_status'    => 'nullable',
                'has_npwp'          => 'nullable',
                'num_of_dependents_family' => 'nullable',
                'permanent_status'  => 'nullable',
                // 'employee_guarantee' => 'nullable',
				'profile_photo_url' => 'nullable',
			];
		}

		return [];
	}
}
