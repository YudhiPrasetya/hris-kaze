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
 * @file   ModelIndexDropped.php
 * @date   2020-09-28 14:30:24
 */

namespace App\Events;

class ModelIndexDropped {
	public $indexName;

	/**
	 * Create a new event instance.
	 *
	 * @param $indexName
	 */
	public function __construct($indexName) {
		$this->indexName = $indexName;
	}
}