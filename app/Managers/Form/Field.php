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
 * @file   Field.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Form;

class Field {
	// Simple fields
	const COLLECTION = 'collection';

	const TEXT = 'text';

	const TEXTAREA = 'textarea';

	const SELECT = 'select';

	const CHOICE = 'choice';

	const CHECKBOX = 'checkbox';

	const SWITCH = 'switch';

	const RADIO = 'radio';

	const PASSWORD = 'password';

	const REPEATED = 'repeated';

	const HIDDEN = 'hidden';

	const FILE = 'file';

	const BROWSE = 'browse';

	const STATIC = 'static';

	const INPUT_GROUP = 'input_group';

	//Date time fields
	const DATE = 'date';

	const DATETIME_LOCAL = 'datetime-local';

	const DATETIME = 'datetime';

	const MONTH = 'month';

	const TIME = 'time';

	const WEEK = 'week';

	//Special Purpose fields
	const COLOR = 'color';

	const SEARCH = 'search';

	const IMAGE = 'image';

	const EMAIL = 'email';

	const URL = 'url';

	const TEL = 'tel';

	const NUMBER = 'number';

	const RANGE = 'range';

	const ENTITY = 'entity';

	const FORM = 'form';

	//Buttons
	const BUTTON_SUBMIT = 'submit';

	const BUTTON_RESET = 'reset';

	const BUTTON_BUTTON = 'button';
}
