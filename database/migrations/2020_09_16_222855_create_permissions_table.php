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
 * @file   2020_09_16_222855_create_permissions_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;


class CreatePermissionsTable extends MigrationBase {
	private string $model_morph_key;

	/**
	 * CreatePermissionsTable constructor.
	 *
	 * @throws \Exception
	 */
	public function __construct() {
		$tableNames = config('permission.table_names');

		if (empty($tableNames)) {
			throw new \Exception(
				'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'
			);
		}

		parent::__construct('master', $tableNames);

		$this->model_morph_key = config('permission.column_names')['model_morph_key'];
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function down() {
		$tableNames = config('permission.table_names');

		if (empty($tableNames)) {
			throw new \Exception(
				'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.'
			);
		}

		Schema::dropIfExists($tableNames['role_has_permissions']);
		Schema::dropIfExists($tableNames['model_has_roles']);
		Schema::dropIfExists($tableNames['model_has_permissions']);
		Schema::dropIfExists($tableNames['roles']);
		Schema::dropIfExists($tableNames['permissions']);
	}

	protected function create_roles(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('name');
		$table->string('alias');
		$table->integer('level')->default(0);
		$table->string('guard_name');
		$table->timestamps();

		$this->createUnique('name');
		$this->createUnique('name', 'guard_name');
	}

	protected function create_permissions(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('name');
		$table->string('guard_name');
		$table->text('description')->nullable(true);
		$table->timestamps();

		$this->createUnique('name');
		$this->createUnique('name', 'guard_name');
	}

	protected function create_model_has_permissions(Blueprint $table, Builder $schema) {
		$tableNames = config('permission.table_names');
		$this->unsignedBigIntegerForeign('permission_id', 'id', 'master.' . $tableNames['permissions'])
		     ->cascadeOnDelete();

		$table->string('model_type');
		$table->unsignedBigInteger($this->model_morph_key);
		//$table->index([$this->model_morph_key, 'model_type'], 'model_has_permissions_model_id_model_type_index');
		$this->createIndex($this->model_morph_key, 'model_type');

		//$table->primary(['permission_id', $this->model_morph_key, 'model_type'per,ion],'model_has_permissions_permission_model_type_primary');
		$this->createPrimary('permission_id', $this->model_morph_key, 'model_type');
	}

	protected function create_model_has_roles(Blueprint $table, Builder $schema) {
		$tableNames = config('permission.table_names');
		$this->unsignedBigIntegerForeign('role_id', 'id', 'master.' . $tableNames['roles'])
		     ->cascadeOnDelete();

		$table->string('model_type');
		$table->unsignedBigInteger($this->model_morph_key);
		//$table->index([$this->model_morph_key, 'model_type'], 'model_has_roles_model_id_model_type_index');
		$this->createIndex($this->model_morph_key, 'model_type');

		//$table->primary(['role_id', $this->model_morph_key, 'model_type'],'model_has_roles_role_model_type_primary');
		$this->createPrimary('role_id', $this->model_morph_key, 'model_type');
	}

	protected function create_role_has_permissions(Blueprint $table, Builder $schema) {
		$tableNames = config('permission.table_names');

		$this->unsignedBigIntegerForeign('permission_id', 'id', 'master.' . $tableNames['permissions'])
		     ->cascadeOnDelete();
		$this->unsignedBigIntegerForeign('role_id', 'id', 'master.' . $tableNames['roles'])
		     ->cascadeOnDelete();

		//$table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
		$this->createPrimary('permission_id', 'role_id');
	}
}
