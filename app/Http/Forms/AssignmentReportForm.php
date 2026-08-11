<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;

class AssignmentReportForm extends Form
{
    public function buildForm()
    {
        // $assignmentType = [
        //     'All' => 'All',
        //     'Domestic' => 'Domestic',
        //     'Overseas' => 'Overseas',
        // ];
        $this
            // ->add(
            //     'assignment_type',
            //     Field::SELECT,
            //     [
            //         'choices' => $assignmentType,
            //         'selected' => 'All',
            //         'attr' => [
            //             'data-value' => 'All',
            //             'style' => "width: 100%",
            //         ]
            //     ]
            // )
            ->add(
                'start_date',
                Field::DATE,
                [
                    'label' => 'Start Date',
                ]
            )
            ->add(
                'end_date',
                Field::DATE,
                [
                    'label' => 'End Date',
                ]
            )
            ->add(
                'submit',
                Field::BUTTON_SUBMIT,
                [
                    'label' => 'Generate Report',
                    'attr' => [
                        'class' => 'btn-falcon-primary',
                    ]
                ]
            );
    }
}
