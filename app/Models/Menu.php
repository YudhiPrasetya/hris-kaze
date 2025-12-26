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
 * @file   Menu.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models;

use App\Casts\Json;
use App\Casts\JsonCasts;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Menu
 *
 * @property int    id
 * @property int    parent_id
 * @property string name
 * @property string title
 * @property string route
 * @property Json   attrs
 * @property int    level
 * @property int    role_id
 * @property bool   divider
 *
 * @package App\Models\Master
 */
class Menu extends ModelBase {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	protected $casts = [
		'attrs' => JsonCasts::class,
	];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.menu', onlyName: true));

		parent::__construct($attributes);
	}

	public function hasChildren(): bool {
		return $this->children()->count() > 0;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function children(): Collection {
		return $this->hasMany(__CLASS__, 'parent_id', 'id')
		            ->orderBy('level')
		            ->getResults();
	}

	/**
	 * @return \App\Models\Menu
	 */
	public function parent(): ?Menu {
		return $this->hasOne(__CLASS__, 'id', 'parent_id')
		            ->getResults();
	}

	public function setParent(Menu $parent) {
		return $this->hasOne(__CLASS__, 'parent_id', 'id')
		            ->save($parent);
	}

	/**
	 * @return \App\Models\Role|null
	 */
	public function role(): ?Role {
		return $this->hasOne(Role::class, 'id', 'role_id')
		            ->getResults();
	}

	/**
	 * @param \App\Models\Role $role
	 *
	 * @return false|\Illuminate\Database\Eloquent\Model
	 */
	public function setRole(Role $role) {
		return $this->hasOne(Role::class, 'id', 'role_id')
		            ->save($role);
	}

	public function addChild(Menu $child) {
		return $this->hasMany(__CLASS__, 'parent_id', 'id')
		            ->save($child);
	}

	public function addChildren(array $children) {
		return $this->hasMany(__CLASS__, 'parent_id', 'id')
		            ->saveMany($children);
	}
}
