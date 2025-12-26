<?php
/**
 * This file is part of the Laravel project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   CalendarEventRepository
 * @date   22/03/2021 16:08
 */

namespace App\Repositories\Eloquent;

use App\Models\CalendarEvent;
use App\Repositories\CalendarEventRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class CalendarEventRepository
 *
 * @package App\Repositories\Eloquent
 */
class CalendarEventRepository extends RepositoryBase implements CalendarEventRepositoryInterface {
	public function __construct(CalendarEvent $model) {
		parent::__construct($model);
	}
}
