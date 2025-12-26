<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateSettingsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'settings');
	}

	protected function create(Blueprint $table, Builder $schema): void {
		$table->id();
		$table->string('section');
		$table->string('key');
		$table->string('value')->nullable();
		$table->timestamps();
		$table->softDeletes();

		$this->createUnique('section', 'key');
	}
}
