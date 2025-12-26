<?php

namespace App\Console\Commands;

use App\Contracts\Theme as ThemeContract;
use Illuminate\Console\Command;


class AppThemeList extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:theme:list';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all available themes';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$themes = $this->laravel[ThemeContract::class]->all();
		$headers = ['Name', 'Author', 'Version', 'Parent'];
		$output = [];
		foreach ($themes as $theme) {
			$output[] = [
				'Name'    => $theme->get('name'),
				'Author'  => $theme->get('author'),
				'Version' => $theme->get('version'),
				'Parent'  => $theme->get('parent'),
			];
		}

		$this->table($headers, $output);
	}
}
