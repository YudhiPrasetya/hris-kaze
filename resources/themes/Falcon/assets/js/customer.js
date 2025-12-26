/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   customer.js
 * @date   2021-03-27 12:0:37
 */

import { autoFetchLocations } from '../../.includes/js/locations';

$(function () {
	autoFetchLocations('#country_id', '#state_id', '#city_id', '#district_id', '#village_id');
});