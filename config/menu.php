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
 * @file   menu.php
 * @date   24/08/2020 09.32
 */

return [
	'settings' => [
		'default' => array(
			'auto_activate'    => true,
			'activate_parents' => true,
			'active_class'     => 'active',
			'restful'          => false,
			'cascade_data'     => true,
			'rest_base'        => '',      // string|array
			'active_element'   => 'item',  // item|link
		),
	],

	'views' => [
		'bootstrap-items' => 'menu::bootstrap-navbar-items',
	],
];