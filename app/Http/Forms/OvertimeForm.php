<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Employee;
use App\Models\Overtime;
use App\Model\App\Models\WorkingShift;
use App\Models\WorkingShift as ModelsWorkingShift;

class OvertimeForm extends Form{
    
    public function buildForm(){
        $endWorkingHour = ModelsWorkingShift::get("*")->first()->end;

        $this->add('overtime_date', Field::DATE,
            ['label' => 'Overtime Date (Tanggal lembur)', 'attr' => [
                'class_append' => 'col-5'
            ]]
        )
        ->add('id_employee', Field::ENTITY, [
            'class' => Employee::class,
            'property' => 'name',
            'label' => 'Employee',
            'query_builder' => function(Employee $employee){
                return $employee->select(['id', 'name'])->selectRaw('CONCAT("{\"key\":\"", id, "\",\"labelWithKey\": false, \"value\":\"", name, "\"}") as name');
            }
        ])
        ->add('end', Field::TIME, [
            'label' => 'End',
            'attr' => ['class_append' => 'col-5', 'readonly'],
            'value' => $endWorkingHour
        ])
        ->add('overtime', Field::TIME, [
            'label' => 'Overtime',
            'attr' => ['class_append' => 'col-5']
        ])
        ->add('necessity', Field::TEXTAREA, [
            'label' => 'Needs (Keperluan)',
            'attr' => ['rows' => '3']
        ])
        // ->add('status', Field::SWITCH, ['label' => 'Approved'])
        ->add('submit', Field::BUTTON_SUBMIT, [
                'label' => '<i class="fad fa-save mr-1"></i> Submit',
                'attr'  => ['class' => 'btn-falcon-success', 'id' => 'submit-overtime'],
        ]);
    }
}
