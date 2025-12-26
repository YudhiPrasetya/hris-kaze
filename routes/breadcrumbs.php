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
 * @file   breadcrumbs.php
 * @date   2020-10-29 5:31:15
 */

use App\Facades\Breadcrumbs;
use App\Managers\Breadcrumbs\Generator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


Breadcrumbs::for('home',
	function (Generator $trail) {
		$trail->add('<span class="fad fa-home" style="min-height: 21px" data-fa-transform="grow-3"></span>', route('home'), ['home']);
	});

$routes = [
	'report.attendance'          => ['Attendance Report', []],
	'report.salary'              => ['Salary Report', []],
	'report.attendance.employee' => ['Attendance Detail Report', ['employee:name']],
	'report.salary.employee'     => ['Salary Detail Report', ['employee:name']],
	'attendance'                 => ['Attendance', ['attendance:at']],
	'settings.attendance'        => ['Settings :: Attendance', []],
	'settings.calendar'          => ['Settings :: Calendar', ['calendar:title']],
	'calendar'                   => ['Settings :: Calendar', ['calendar:title']],
	'machine'                    => ['Machines', ['machine:name']],
	'vehicle'                    => ['Vehicles', ['vehicle:plat_number']],
	'customer'                   => ['Customers', ['customer:name']],
	'customer.machine'           => ['Machines', ['customer:name', 'machine:serial_number']],
	'assignment'                 => ['Assignments', ['assignment:service_no']],
	'task'                       => ['Tasks', ['task:title']],
	'employee'                   => ['Employees', ['employee:name']],
	'user'                       => ['Users', ['user:username']],
	'audit'                      => ['Audit Trails', ['audit:id']],
    'fingerprintdevice'          => ['Finger Print Devices', ['fingerprintdevice:name']],
    'jobtitle'                   => ['Job Title', ['jobtitle:name']],
    'workingshift'               => ['Working Shift', ['workingshift:name']],
    'position'                   => ['Positions', ['position:name']],
    'reasonforleave'             => ['Reason For Leave', ['reasonforleave:name']],
    'devicelog'                  => ['Pull Data', ['devicelog:name']],
    'permit'                     => ['Permits', ['permit:name']]
	//'tracker'                    => ['Trackers', ['tracker']],
];

/**
 * @var $coll \Illuminate\Routing\RouteCollection
 */
$coll = Route::getFacadeRoot()->getRoutes();

collect($routes)->each(function ($params, $routeName) use ($coll) {
	list($title, $args) = $params;
	$paths = [];

	if (count($args) > 1) {
		collect(explode('.', $routeName))->each(function ($path, $i) use ($coll, &$paths, $params) {
			$paths[] = $path;
			$r = implode('.', $paths);

			// Show
			if (!Breadcrumbs::has("$r.show")) {
				//$p = implode('.', array_slice($paths, 0, -$i));

				Breadcrumbs::for("$r.show",
					function (Generator $trail) use ($coll, $r, $i, $params) {
						list($title, $args) = $params;

						$num = func_num_args();
						$params = [];
						collect(func_get_args())->each(function ($model, $i) use ($trail, $args, $num, &$params) {
							if ($i && $i < $num - 1) {
								list($key, $labelKey) = explode(':', $args[$i - 1]);
								$label = '';

								if (is_array($model)) {
									$params = $model;
								}
								else {
									$primaryKey = $model->getPrimaryKey();
									$params[$key] = $model->$primaryKey;
									$label = $model->$labelKey;
								}

								$trail->parent((implode('.', array_keys($params))) . ".show", array_merge($params, ['_title' => $label]));
							}
						});

						$fargs = func_get_args();
						if (is_array($fargs[1])) {
							$params = $fargs[1];
							$label = $params['_title'] ?: '';
							unset($params['_title']);
							$trail->add("$label", route("$r.show", $params), ["$r.show", $params]);
						}
						else {
							$params = collect($args)->map(function ($v, $i) use ($fargs) {
								list($key, $labelKey) = explode(':', $v);
								$model = $fargs[$i + 1];
								$primaryKey = $model->getPrimaryKey();

								return [
									'key'   => $key,
									'value' => $model->$primaryKey,
								];
							})->pluck('value', 'key');

							list($key, $labelKey) = explode(':', array_slice($args, -$i, 1)[0]);
							$model = func_get_arg($num - 1);
							$label = $model->$labelKey;

							$trail->add("$label", route("$r.show", $params->toArray()), ["$r.show", $params->toArray()]);
						}
					});
			}

			// Edit
			else if (!Breadcrumbs::has("$r.edit")) {
				Breadcrumbs::for("$r.edit",
					function (Generator $trail) use ($r, $i, $params) {
						list($title, $args) = $params;

						$num = func_num_args();
						$params = [];
						collect(func_get_args())->each(function ($model, $i) use ($trail, $args, $num, &$params) {
							if ($i && $i < $num - 1) {
								list($key, $labelKey, $k) = explode(':', $args[$i - 1]);
								$label = '';

								if (is_array($model)) {
									$params = $model;
								}
								else {
									$primaryKey = $model->getPrimaryKey();
									$params[$key] = $model->$primaryKey;
									$label = $model->$labelKey;
								}

								$trail->parent((implode('.', array_keys($params))) . ".show", array_merge($params, ['_title' => $label]));
							}
						});

						$fargs = func_get_args();
						$params = collect($args)->map(function ($v, $i) use ($fargs) {
							list($key, $labelKey) = explode(':', $v);
							$model = $fargs[$i + 1];
							$primaryKey = $model->getPrimaryKey();

							return [
								'key'   => $key,
								'value' => $model->$primaryKey,
							];
						})->pluck('value', 'key');

						list($key, $labelKey) = explode(':', array_slice($args, -$i, 1)[0]);
						$model = func_get_arg($num - 1);
						$label = $model->$labelKey;
						$trail->parent("$r.show", array_merge($params->toArray(), ['_title' => $label]));
						$trail->add("Edit", route("$r.edit", $params->toArray()), ["$r.edit", $params->toArray()]);
					});
			}

			// create
			else if (!Breadcrumbs::has("$r.create")) {
				Breadcrumbs::for("$r.create",
					function (Generator $trail) use ($r, $i, $params) {
						list($title, $args) = $params;

						$num = func_num_args();
						$params = [];
						collect(func_get_args())->each(function ($model, $i) use ($trail, $args, $num, &$params) {
							if ($i) {
								list($key, $labelKey) = explode(':', $args[$i - 1]);
								$primaryKey = $model->getPrimaryKey();
								$params[$key] = $model->$primaryKey;
								$label = $model->$labelKey;

								//debug($label);
								$trail->parent((implode('.', array_keys($params))) . ".show", array_merge($params, ['_title' => $label]));
							}
						});

						$fargs = func_get_args();
						$params = collect($args)->map(function ($v, $i) use ($fargs) {
							if (isset($fargs[$i + 1])) {
								list($key, $labelKey) = explode(':', $v);
								$model = $fargs[$i + 1];
								$primaryKey = $model->getPrimaryKey();

								return [
									'key'   => $key,
									'value' => $model->$primaryKey,
								];
							}

							return [];
						})->pluck('value', 'key');

						list($key, $labelKey) = explode(':', array_slice($args, -$i, 1)[0]);
						$model = func_get_arg($num - 1);
						$label = $model->$labelKey;
						$trail->add("Create New $title", route("$r.create", $params->toArray()), ["$r.create", $params->toArray()]);
					});
			}
		});
	}
	else {
		Breadcrumbs::for($routeName,
			function (Generator $trail, $model = null) use ($routeName, $params) {
				list($title, $param) = $params;
				$args = [];

				if (count($param) > 0) {
					$trail->parent(Str::beforeLast($routeName, '.'));
					list($key, $label) = explode(':', $param[0]);
					$primaryKey = $model->getPrimaryKey();
					$args = [$key => $model->$primaryKey];
					$title = $model->$label;
				}
				else {
					$trail->parent('home');
				}

				$trail->add($title, route($routeName, $args));
			});

		Breadcrumbs::for("$routeName.index",
			function (Generator $trail) use ($coll, $routeName, $params) {
				$trail->parent('home');

				if ($coll->getByName("$routeName.index")) {
					list($title, $param) = $params;
					$trail->add($title, route("$routeName.index"), ["$routeName.index"]);
				}
			});

		Breadcrumbs::for("$routeName.show",
			function (Generator $trail, $model = null) use ($coll, $routeName, $params) {
				list($title, $param) = $params;
				if ($model) list($key, $labelKey) = explode(':', is_array($param) && count($param) ? $param[0] : '');

				if (is_array($model)) {
					$params = $model;
					$title = $model['_title'] ?: '';
					unset($params['_title']);
				}
				else {
					if ($model) {
						$primaryKey = $model->getPrimaryKey();
						$title = $model->$labelKey;
						$params = ["$key" => $model->$primaryKey];
					}
					else {
						$params = [];
					}
				}

				if ($coll->getByName("$routeName.index")) $trail->parent("$routeName.index");
				else $trail->parent("home");
				$trail->add($title, route("$routeName.show", $params), ["$routeName.show", $params]);
			});

		Breadcrumbs::for("$routeName.edit",
			function (Generator $trail, $model) use ($routeName, $params) {
				list($title, $param) = $params;
				list($key, $labelKey) = explode(':', $param[0]);

				if (is_array($model)) {
					$params = $model;
					$title = $model['_title'] ?: '';
					unset($params['_title']);
				}
				else {
					$primaryKey = $model->getPrimaryKey();
					$title = $model->$labelKey;
					$params = ["$key" => $model->$primaryKey];
				}

				$trail->parent("$routeName.show", array_merge($params, ['_title' => $title]));
				$trail->add("Edit", route("$routeName.show", $params), ["$routeName.show", $params]);
			});

		Breadcrumbs::for("$routeName.create",
			function (Generator $trail) use ($routeName, $params) {
				list($title, $param) = $params;
				list($key, $labelKey) = explode(':', $param[0]);

				if (func_num_args() > 1) {
					if (is_array(func_get_arg(1))) {
						$params = func_get_arg(1);
						$title = $params['_title'] ?: '';
						unset($params['_title']);
					}
					else {
						$model = func_get_arg(1);
						$primaryKey = $model->getPrimaryKey();
						$title = $model->$labelKey;
						$params = ["$key" => $model->$primaryKey];
					}

					$trail->parent("$routeName.show", array_merge($params, ['_title' => $title]));
				}
				else {
					$params = [];
					$trail->parent("$routeName.index");
				}

				$trail->add("Create New", route("$routeName.create", $params), ["$routeName.create", $params]);
			});
	}
});

Breadcrumbs::for("tracker",
	function (Generator $trail) {
		$trail->parent('home');
		$trail->add("Tracker", route("tracker"));
	});
