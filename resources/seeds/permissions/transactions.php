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
 * @file   transactions.php
 * @date   18/09/2020 21.46
 */

use App\Models\Permission;


$date = (new DateTime())->format('Y-m-d H:i:s');

return array(
	// Reporting
	[
		'name'        => 'report.download',
		'slug'        => 'report.download',
		'description' => 'Can download reporting',
		'model'       => Permission::class,
		'created_at'  => $date,
		'updated_at'  => $date,
	],
	[
		'name'        => 'access.dashboard',
		'slug'        => 'access.dashboard',
		'description' => 'Able to login/access dashboard',
		'model'       => Permission::class,
		'created_at'  => $date,
		'updated_at'  => $date,
	],
);