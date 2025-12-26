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
 * @file   Breakpoint.php
 * @date   20/09/2020 04.26
 */

namespace App\Enums\Bootstrap;

use App\Enums\EnumBase;


/**
 * Class Display
 *
 * @method static Display NONE()
 * @method static Display INLINE()
 * @method static Display INLINE_BLOCK()
 * @method static Display BLOCK()
 * @method static Display TABLE()
 * @method static Display TABLE_CELL()
 * @method static Display TABLE_ROW()
 * @method static Display FLEX()
 * @method static Display INLINE_FLEX()
 *
 * @see https://getbootstrap.com/docs/4.5/utilities/display/
 * @package App\Enums\Bootstrap
 */
class Display extends EnumBase {
	const NONE = 'none';

	const INLINE = 'inline';

	const INLINE_BLOCK = 'inline-block';

	const BLOCK = 'block';

	const TABLE = 'table';

	const TABLE_CELL = 'table-cell';

	const TABLE_ROW = 'table-row';

	const FLEX = 'flex';

	const INLINE_FLEX = 'inline-flex';

	protected function __init(...$parameters) {
		$this->setFormat([
			'd-{value}',
			'd-{component}-{value}',
		]);
	}
}
