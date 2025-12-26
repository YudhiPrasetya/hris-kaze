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
 * @file   ModelIndexCreated.php
 * @date   2020-09-28 14:30:10
 */

namespace App\Events;

class ModelIndexCreated {
	public $indexName;

	public $indexFields;

	/**
	 * Create a new event instance.
	 *
	 * @param $indexName
	 * @param $indexFields
	 */
	public function __construct($indexName, $indexFields) {
		$this->indexName = $indexName;
		$this->indexFields = $indexFields;
	}
}