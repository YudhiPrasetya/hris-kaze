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
 * @file   DateType.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Form\Fields;

class DateType extends FormField {
	public function render(array $options = [], $showLabel = true, $showField = true, $showError = true): string {
		if ($this->hasOption('attr')) {
//			$this->type = $this->type ?? ($this->getOption('attr.type') ?: 'text');
			$this->type = $this->getOption('attr.type') ?: 'text';
		}

		$options['showAppend'] = !$this->getOption('showPrepend');

		return parent::render($options, $showLabel, $showField, $showError);
	}

	public function getType() {
		return 'text';
	}

	/**
	 * @inheritdoc
	 */
	protected function getTemplate(): string {
		return 'date';
	}

	protected function getRenderData() {
		return [
			'showAppend'  => $this->hasOption('showAppend'),
			'showPrepend' => $this->hasOption('showPrepend'),
			'append'      => '<i class="fad fa-'.($this->type == 'date' ? 'calendar' : 'clock').'"></i>',
			'prepend'     => '<i class="fad fa-'.($this->type == 'date' ? 'calendar' : 'clock').'"></i>',
		];
	}

	protected function getDefaults() {
		return [
			'showAppend'  => true,
			'showPrepend' => false,
			'append'      => '<i class="fad fa-'.($this->type == 'date' ? 'calendar' : 'clock').'"></i>',
			'prepend'     => '<i class="fad fa-'.($this->type == 'date' ? 'calendar' : 'clock').'"></i>',
		];
	}
}
