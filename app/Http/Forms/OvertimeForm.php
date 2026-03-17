<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Employee;
// use App\Models\Overtime;
// use App\Model\App\Models\WorkingShift;
// use App\Models\WorkingShift as ModelsWorkingShift;

class OvertimeForm extends Form{
    
    public function buildForm(){
        // $endWorkingHour = ModelsWorkingShift::get("*")->first()->end;

        $this->add('id_attendance', Field::HIDDEN)
        ->add('overtime_date', Field::TEXT,
            ['label' => 'Overtime Date (Tanggal lembur)', 'attr' => [
                'class_append' => 'col-6'
            ]]
        )
        // ->add('id_employee', Field::ENTITY, [
        //     'class' => Employee::class,
        //     'property' => 'name',
        //     'label' => 'Employee',
        //     'query_builder' => function(Employee $employee){
        //         return $employee->select(['id', 'name'])->selectRaw('CONCAT("{\"key\":\"", id, "\",\"labelWithKey\": false, \"value\":\"", name, "\"}") as name');
        //     }
        // ])
        ->add('id_employee', Field::HIDDEN)
        ->add('employee', Field::TEXT, [
            'label' => 'Employee',
            'attr' => ['class_append' => 'col-6']
        ])
        ->add('start', Field::TEXT, [
            'label' => 'Start',
            'attr' => ['class_append' => 'col-6'],
            // 'value' => $endWorkingHour
        ])
        ->add('end', Field::TEXT, [
            'label' => 'End',
            'attr' => ['class_append' => 'col-6']
        ])
        ->add('overtime', Field::TEXT, [
            'label' => 'Overtime',
            'attr' => ['class_append' => 'col-6']
        ])
        ->add('overtime_duration', Field::TEXT, [
            'label' => 'Overtime Duration (Hour/s)',
            'attr' => ['class_append' => 'col-2']
        ])
        ->add('necessity', Field::TEXTAREA, [
            'label' => 'Necessity (Keperluan)',
            'attr' => ['rows' => '3']
        ])
        // ->add('status', Field::SWITCH, ['label' => 'Approved'])
        ->add('submit', Field::BUTTON_SUBMIT, [
                'label' => '<i class="fad fa-check-circle mr-1"></i> Confirm Overtime',
                'attr'  => ['class' => 'btn-falcon-success', 'id' => 'submit-overtime'],
        ]);
    }
}
