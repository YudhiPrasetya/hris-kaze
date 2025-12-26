<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\AnnualLeave;
use App\Models\AttendanceReason;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class AttendanceForm extends Form {
	public function buildForm() {
		$this
			->add('employee_id', Field::SELECT, ['label' => 'Employee', 'attr' => ['data-placeholder' => "Select a employee"]])
			->add('attendance_reason_id',
				Field::ENTITY,
				[
					'class'    => AttendanceReason::class,
					'property' => 'name',
					'label'    => 'Presence Status',
					'attr'     => ['data-placeholder' => "Select a reason"],
				])
			->add('annual_leave_id', Field::SELECT, ['label' => 'Annual Leave Voucher', 'attr' => ['data-placeholder' => "Select a voucher"]])
			->add('at', Field::DATE, ['label' => 'Date'])
			->add('start', Field::TIME, ['label' => 'Check-In'])
			->add('end', Field::TIME, ['label' => 'Check-Out'])
			->add('overtime', Field::TIME, ['label' => 'Overtime'])
			->add('detail', Field::TEXTAREA)
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					'attr'  => ['class' => 'btn-falcon-danger'],
				]);
	}
}
