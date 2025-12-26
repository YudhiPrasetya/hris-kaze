<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Assignment;


class AssignmentPartForm extends Form {
    public function buildForm() {
        $this
            ->add('part_name', Field::TEXT)
            ->add('part_type', Field::TEXT)
            ->add('qty', Field::TEXT)
            ->add('unit', Field::TEXT);
    }
}
