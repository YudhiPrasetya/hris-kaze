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
 * @file   scout-driver.php
 * @date   2020-10-29 5:31:14
 */

return [
	'mysql' => [
		'mode'                         => 'LIKE_EXPANDED',
		'model_paths'                  => [app_path(), app_path('Models'), app_path('Models/World')],
		'min_search_length'            => 0,
		'min_fulltext_search_length'   => 4,
		'min_fulltext_search_fallback' => 'LIKE',
		'query_expansion'              => false,
	],
];
