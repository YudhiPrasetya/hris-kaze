<?php

namespace App\Managers\Form\Fields;

class RepeatedType extends ParentType {
	/**
	 * @inheritdoc
	 */
	public function getAllAttributes(): array {
		// Collect all children's attributes.
		return $this->parent->getFormHelper()->mergeAttributes($this->children);
	}

	/**
	 * Get the template, can be config variable or view path.
	 *
	 * @return string
	 */
	protected function getTemplate() {
		return 'repeated';
	}

	/**
	 * @inheritdoc
	 */
	protected function getDefaults(): array {
		return [
			'type'           => 'password',
			'second_name'    => null,
			'first_options'  => ['label' => 'Password'],
			'second_options' => ['label' => 'Password confirmation']
		];
	}

	/**
	 * @return mixed|void
	 * @throws \App\Managers\Form\Filters\Exception\InvalidInstanceException
	 * @throws \App\Managers\Form\Filters\Exception\UnableToResolveFilterException
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	protected function createChildren() {
		$firstName = $this->getRealName();
		$secondName = $this->getOption('second_name');

		if (is_null($secondName)) {
			$secondName = $firstName . '_confirmation';
		}

		$form = $this->parent->getFormBuilder()->plain(
			[
				'name'  => $this->parent->getName(),
				'model' => $this->parent->getModel()
			]
		)
		                     ->add($firstName, $this->getOption('type'), $this->getOption('first_options'))
		                     ->add($secondName, $this->getOption('type'), $this->getOption('second_options'));

		$this->children['first'] = $form->getField($firstName);
		$this->children['second'] = $form->getField($secondName);
	}
}
