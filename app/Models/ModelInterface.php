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
 * @file   ModelInterface.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models;

use ArrayAccess;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


interface ModelInterface extends AuditableInterface, Arrayable, ArrayAccess, Jsonable, JsonSerializable, QueueableEntity, UrlRoutable {
	public function update(array $attributes = [], array $options = []);

	public function save(array $options = []);
}