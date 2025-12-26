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
 * Class Breakpoint
 *
 * @method static static AUTO()
 * @method static static EXTRA_SMALL()
 * @method static static SMALL()
 * @method static static MEDIUM()
 * @method static static LARGE()
 * @method static static EXTRA_LARGE()
 *
 * @see https://getbootstrap.com/docs/4.5/layout/grid/#grid-options
 * @package App\Enums\Bootstrap
 */
class Breakpoint extends EnumBase {
	const AUTO = 'auto';

	const EXTRA_SMALL = null;

	const SMALL = 'sm';

	const MEDIUM = 'md';

	const LARGE = 'lg';

	const EXTRA_LARGE = 'xl';

	protected function __init(...$parameters) {
		$this->setFormat(
			'{component}',
			'{component}-{value:auto}',
			'{component}-{d[1:12]}',
			'{component}-{value}-{s:[auto]}',
			'{?component}-{value}-{d[1:12]}'
		);
	}
}
