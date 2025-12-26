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
 * @file   BrowseType.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Form\Fields;

class BrowseType extends FileType {
	public function render(array $options = [], $showLabel = true, $showField = true, $showError = true): string {
		$btnAttr = $this->getOption('btnAttr', []);

		if ($this->hasOption('btnAttr')) {
			$class = explode(' ', $this->getOption('btnAttr.class') ?: "");
			if (!in_array('btn-browse', $class)) $class[] = 'btn-browse';
			$this->setOption('btnAttr.class', implode(' ', $class));
		}

		$options['btnAttr'] = $this->formHelper->prepareAttributes($this->getOption('btnAttr', []));

		return parent::render($options, $showLabel, $showField, $showError);
	}

	protected function getDefaults(): array {
		return [
			'attr' => [
				'class' => '',
			],
			'btnLabel' => 'Browse',
			'btnAttr' => [
				'class' => 'btn btn-falcon-primary d-inline-block position-relative overflow-hidden',
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	protected function getTemplate(): string {
		return 'browse';
	}
}
