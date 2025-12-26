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
 * @file   AppClear.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;


class AppClear extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:clear {?--y|yes}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear all cache and fix files/directories permission';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		/**
		 * @var $process Process
		 */
		$process = null;
		/**
		 * @var $input ArgvInput
		 */
		$input = new ArgvInput();
		/**
		 * @var $output ConsoleOutput
		 */
		$output = new ConsoleOutput();
		/**
		 * @var $io SymfonyStyle
		 */
		$io = new SymfonyStyle($input, $output);

		$message = null;
		$exitCode = 0;
		$continue = false;

		if (!$this->option('yes')) {
			$continue = $this->confirm('Do you wish to continue?', true);
		}
		else {
			$continue = true;
		}

		if ($continue) {
			$paths = [
				''                  => ['owner' => 'eq:eq', 'context' => 'httpd_sys_content_t'],
				'bootstrap/cache'   => ['owner' => 'eq:nginx', 'context' => 'httpd_sys_rw_content_t', 'mode' => 'g+rw'],
				'storage'           => [
					'owner'   => 'nginx:nginx',
					'context' => 'httpd_sys_rw_content_t',
					'mode'    => 'g+w',
				],
				'storage/framework' => ['owner' => 'nginx:nginx', 'context' => 'httpd_sys_rw_content_t'],
				'storage/logs'      => ['owner' => 'nginx:nginx', 'context' => 'httpd_log_t'],
			];

			foreach ($paths as $path => $perms) {
				$path2 = $this->getRealPath($path);

				foreach ($perms as $type => $perm) {
					$command = null;

					switch ($type) {
						case 'owner':
							$command = sprintf('sudo chown -R %s %s', $perm, $path2);
							$command2 = sprintf('sudo chown -R %s %s', $perm, empty($path) ? '.' : $path);
							break;
						case 'context':
							$command = sprintf('sudo chcon -R -t %s %s', $perm, $path2);
							$command2 = sprintf('sudo chcon -R -t %s %s', $perm, empty($path) ? '.' : $path);
							break;
						case 'mode':
							$command = sprintf('sudo chmod -R %s %s', $perm, $path2);
							$command2 = sprintf('sudo chmod -R %s %s', $perm, empty($path) ? '.' : $path);
							break;
						default:
							break;
					}

					$this->info(sprintf('Changing %-8s: %s', $type, $command2));
					if (!empty($command)) {
						list($exitCode, $message) = $this->runShellCommand($command);
						if ($exitCode) break;
					}
				}
			}

			$this->message(
				sprintf('Update directories/files permission %s! (%d)', $message, $exitCode),
				$exitCode == 0 ? false : true
			);

			$commands = [
				'route:clear',
				'view:clear',
				'cache:clear',
				'config:clear',
				'debugbar:clear',
				'event:clear',
				'app:permission:cache-reset',
				'clockwork:clean',
			];

			foreach ($commands as $cmd) $this->call($cmd);

			$file = new Filesystem();
			$this->info('Deleting log files.');
			collect(['logs', 'clockwork', 'debugbar'])->each(function($item) use($file) {
				foreach ($file->files($this->getRealPath('storage/' . $item)) as $f) {
					$exts = ['.log', '.json', 'json'];

					if (in_array($f->getExtension(), $exts)) {
						$file->delete($f->getRealPath());
					}
				}
			});

			list($exitCode, $message) = $this->runShellCommand('sudo systemctl restart php-fpm');
			$this->message(sprintf('Restarting php-fpm %s!', $message), $exitCode == 0 ? false : true);

			list($exitCode, $message) = $this->runShellCommand('sudo systemctl restart nginx');
			$this->message(sprintf('Restarting nginx %s!', $message), $exitCode == 0 ? false : true);
		}

		return 0;
	}

	private function getRealPath(?string $path = '') {
		return base_path($path);
	}

	private function runShellCommand(string $cmd): array {
		$process = Process::fromShellCommandline($cmd);
		$process->run();

		return [$process->getExitCode(), $process->getExitCodeText()];
	}

	private function message(string $msg, bool $isError) {
		if ($isError) $this->error($msg);
		$this->info($msg);
	}
}
