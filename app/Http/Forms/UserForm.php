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
 * @file   UserForm.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Role;


class UserForm extends Form {
	public function buildForm() {
		$switchAttr = ['wrapper' => ['style' => 'min-height:2.25rem', 'class' => 'd-flex align-items-center mb-3']];

		$this->add('name', Field::TEXT)
		     ->add('username', Field::TEXT)
		     ->add('email', Field::TEXT)
		     ->add('profile_photo_path',
			     Field::FILE,
			     [
				     'attr'  => [
					     'accept'      => "image/*",
					     'data-target' => 'targetPreview',
					     'onchange'    => 'javascript: previewImage(event);',
				     ],
			     ])
		     ->add('password',
			     Field::REPEATED,
			     [
				     'rules'          => 'nullable|required_with:password_confirmation|string|confirmed',
				     'type'           => 'password',
				     'second_name'    => 'password_confirmation',
				     'first_options'  => ['attr' => ['autocomplete' => "off"], 'help_block' => ['text' => 'Leave it empty if you not change it']],
				     'second_options' => ['attr' => ['autocomplete' => "off"]],
			     ])
		     ->add('role',
			     Field::ENTITY,
			     [
				     'class'    => Role::class,
				     'property' => 'alias',
			     ])
		     ->add('enabled', Field::SWITCH, $switchAttr)
		     ->add('submit',
			     Field::BUTTON_SUBMIT,
			     [
				     'label' => '<i class="fad fa-save mr-1"></i> Submit',
				     'attr'  => ['class' => 'btn-falcon-danger'],
			     ]);
	}
}