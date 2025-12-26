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
 * @file   InputGroupType.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Form\Fields;

class InputGroupType extends InputType {
	public function render(array $options = [], $showLabel = true, $showField = true, $showError = true): string {
		$options['append'] = $this->getOption('append', false);
		$options['prepend'] = $this->getOption('prepend', false);

		$this->type = $this->getOption('attr.type') ?: 'text';

		return parent::render($options, $showLabel, $showField, $showError);
	}

	/**
	 * @inheritdoc
	 */
	protected function getTemplate(): string {
		return 'input_group';
	}

	protected function getRenderData() {
		return [
			'showAppend'  => $this->hasOption('append'),
			'showPrepend' => $this->hasOption('prepend'),
		];
	}

}
