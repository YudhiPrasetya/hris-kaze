<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateFingerprintsTable extends MigrationBase
{
	public function __construct() {
		parent::__construct('master', 'att_fingerprints');
	}

	protected function create(Blueprint $table, Builder $schema): void {
		$table->id();
		$this->unsignedBigIntegerForeign('pin', 'pin', 'master.att_user_info');
		$table->unsignedSmallInteger('finger_id');
		$table->smallInteger('size');
		$table->boolean('valid');
		$table->longText('template');
		$table->timestamps();
		$table->softDeletes();
	}
}
