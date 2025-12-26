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
 * @file   all.php
 * @date   18/09/2020 21.52
 */

use Illuminate\Support\Str;


$perms = collect([]);
$list = [
	'audit',
	'user',
	'employee',
	'customer',
	'customer machine',
	'attendance',
	'vehicle',
	'task',
	'priority',
	'annual leave',
	'position',
	'assignment',
	'calendar',
	'machine',
	'job title',
	'tracker',
    'fingerprintdevice',

	'report attendance',
	'report salary',
	'report service',
	'report sales',

	'location country',
	'location state',
	'location city',
	'location district',
	'location village',
];

collect($list)->sort()->each(function ($item, $key) use (&$perms) {
	$actions = ['index', 'create', 'destroy', 'show', 'edit', 'store', 'update'];
	collect($actions)->each(function ($action, $key) use ($item, &$perms) {
		$item = Str::of($item);
		$item = $item->snake('.')->append(" $action")->snake('.');

		$desc = Str::of(str_replace('.', ' ', Str::beforeLast((string)$item, '.')));
		$desc = $desc->split('/\s/i')->map(function ($item) {
			return strlen($item) < 4 ? strtoupper($item) : Str::title(str_replace('_', ' ', $item));
		});
		$date = (new DateTime())->format('Y-m-d H:i:s');
		$perms->add([
			'name'        => (string)$item,
			'slug'        => (string)$item,
			'description' => Str::ucfirst(strtolower("Can $action " . str_replace(['CP', 'CNP'], ['Card Present', 'Card Not Present'], $desc->join(' ')))),
			'created_at'  => $date,
			'updated_at'  => $date,
		]);
	});
});

return $perms->toArray();
