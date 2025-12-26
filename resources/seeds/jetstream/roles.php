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
 * @file   roles.php
 * @date   19/09/20 04.17
 */

$perms = require(resource_path('seeds/permissions/all.php'));
$transactions = require(resource_path('seeds/permissions/transactions.php'));
$roles = require(resource_path('seeds/roles.php'));
$all = array_merge($perms, $transactions);

return collect($roles)->map(function ($item, $key) use ($all) {
	return [
		'key'         => $item['slug'],
		'name'        => $item['name'],
		'description' => $item['description'],
		'permissions' => array_remove_empty(collect($all)->map(function ($item, $key) {
			return $item['slug'];
		})),
	];
});
