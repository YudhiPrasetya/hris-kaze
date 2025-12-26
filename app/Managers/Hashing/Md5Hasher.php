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
 * @file   Md5Hasher.php
 * @date   5/09/2020 13.46
 */

namespace App\Managers\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Hashing\AbstractHasher;


class Md5Hasher extends AbstractHasher implements HasherContract {
	/**
	 * Indicates whether to perform an algorithm check.
	 *
	 * @var bool
	 */
	protected bool $verifyAlgorithm;

	public function __construct(array $options = []) {
		$this->verifyAlgorithm = false;
	}

	/**
	 * Hash the given value.
	 *
	 * @param string $value
	 * @param array  $options
	 *
	 * @return string
	 */
	public function make($value, array $options = []): string {
		return md5($value);
	}

	/**
	 * Check if the given hash has been hashed using the given options.
	 *
	 * @param string $hashedValue
	 * @param array  $options
	 *
	 * @return bool
	 */
	public function needsRehash($hashedValue, array $options = []): bool {
		return false;
	}

	public function check($value, $hashedValue, array $options = []): bool {
		return $hashedValue == md5($value);
	}
}