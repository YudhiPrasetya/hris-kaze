<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Employee;
use App\Models\ReasonForLeave;

class LeaveForm extends Form{
    public function buildForm()
    {
        $this
            ->add('leave_date', Field::DATE, ['label' => 'Submission Date (Tgl Pengajuan)', 'attr' => ['class_append' => 'col-5']])
            ->add('id_employee', Field::ENTITY, [
                'class' => Employee::class,
                'property' => 'name',
                'label' => 'Employee',
                'query_builder' => function(Employee $employee){
                    return $employee->select(['id', 'name'])->selectRaw('CONCAT("{\"key\":\"", id, "\",\"labelWithKey\": false, \"value\":\"", name, "\"}") as name');
                }
            ])
            ->add('id_reason_for_leave', Field::ENTITY, [
                'class' => ReasonForLeave::class,
                'property' => 'name',
                'label' => "Reason for leave (Alasan cuti)",
                'query_builder' => function(ReasonForLeave $reasonForLeave){
                    return $reasonForLeave->select(['id', 'name'])->selectRaw('CONCAT("{\"key\":\"", id, "\",\"labelWithKey\": false, \"value\":\"", name, "\"}") as name');
                }
            ])
            ->add('start', Field::DATE, [
                'label' => 'From',
                'attr' => ['class_append' => 'col-5']
            ])
            ->add('end', Field::DATE, [
                'label' => 'To',
                'attr' => ['class_append' => 'col-5']
            ])
            ->add('note', Field::TEXTAREA, [
                'label' => 'Note (Catatan)',
                'attr' => ['rows' => '3']
            ])
            ->add('attachment_path', 'browse', [
					'btnLabel' => 'Pick Document Image',
					'attr'     => [
						'accept'      => "image/*",
						'data-target' => 'targetPreview',
						'onchange'    => 'javascript: previewImage(event);',
					],
            ])
            ->add('cut_att_allowance', Field::SWITCH, ['label' => 'Potong Premi'])
            ->add('submit', Field::BUTTON_SUBMIT, [
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					'attr'  => ['class' => 'btn-falcon-success', 'id' => 'leave-permit'],
            ]);
    }
}
