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
 * @file   SeederBase.php
 * @date   2020-10-30 6:9:57
 */

namespace App\Helpers;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;


class SeederBase extends Seeder {
	/**
	 * @var \Symfony\Component\Console\Style\SymfonyStyle
	 */
	protected SymfonyStyle $out;

	public function __construct() {
		$this->out = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());
	}

	protected function get(string $file) {
		return require(resource_path('seeds/' . $file . '.php'));
	}

	protected function getJson(string $file): Collection {
		return collect(json_decode(File::get(resource_path('seeds/' . $file . '.json')), JSON_OBJECT_AS_ARRAY));
	}

	protected function logFailed(string $message) {
		File::append(storage_path('logs/seed.log'), "$message\n");
	}

	protected function message(bool $success, string $format, ...$p) {
		$this->out->writeln(
			sprintf('[%s][%-7s] ' . $format,
				date('Y-m-d H:i:s'),
				$success ? '<fg=green>Success</>' : '<fg=red>Failed</>',
				...$p
			)
		);
	}

	protected function getMessage(bool $success, string $format, ...$p) {
		return sprintf('%-7s: ' . $format,
			$success ? '<fg=green>Success</>' : '<fg=red>Failed</>',
			...$p
		);
	}

	protected function ellipsis(string $str) {
		$bar = "[#] ############################################## [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% ";
		$width = (new Terminal())->getWidth() ?? SymfonyStyle::MAX_LINE_LENGTH;
		return mb_strimwidth($str, 0, $width - strlen($bar), "...");
	}

	protected function key_filter(array $value, ...$keys) {
		return array_filter($value, function($value, $key) use($keys) {
			return !in_array($key, $keys);
		}, ARRAY_FILTER_USE_BOTH);
	}

	protected function getConnection(string $name): ConnectionInterface {
		$conn = DB::connection(connection($name));
		$conn->disableQueryLog();
		$conn->unsetEventDispatcher();

		return $conn;
	}
}
