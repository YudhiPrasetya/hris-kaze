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
 * @file   MenuSeeder.php
 * @date   2020-10-29 5:31:14
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Models\Menu;
use App\Models\Role;
use App\Repositories\Eloquent\RoleRepository;


class MenuSeeder extends SeederBase {
	//private Role $defaultRole;

	public function __construct(RoleRepository $roleRepository) {
		parent::__construct();

		//$this->defaultRole = $roleRepository->findOneBy(['slug' => 'user']);
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->out->writeln('[#] Adding menu');
		$menu = require_once(resource_path('seeds/menu.php'));

		$level = 0;
		foreach ($menu as $parent) {
			$this->addMenu($parent, ++$level);
		}
	}

	private function addMenu(array $attr, int $level, ?Menu $parent = null) {
		$children = $attr['children'] ?? null;
		if (!is_null($children)) unset($attr['children']);
		if (isset($attr['parent_id'])) unset($attr['parent_id']);
		$attr['level'] = $level;

		$menu = new Menu(array_remove_empty($attr));
		//$menu->setRole($this->defaultRole);
		$ret = $menu->save();

		if (is_null($parent)) {
			if ($attr['divider'] == 1) {
				$this->message($ret, "[%3d] Add divider", $level);
			}
			else {
				$this->message($ret, "[%3d] Add menu      : <fg=blue>%s</>", $level, $attr['title']);
			}
		}

		if (!is_null($parent)) {
			$ret = $parent->addChild($menu);

			if ($attr['divider'] == 1) {
				$this->message($ret !== false, "[%3d] Add divider  -> <fg=blue>%s</>", $level, $parent->title);
			}
			else {
				$this->message($ret !== false, "[%3d] Add child menu: <fg=blue>%s</> \ <fg=blue>%s</>", $level, $parent->title, $attr['title']);
			}
		}

		if ($ret && is_array($children)) {
			$lvl = 0;
			foreach ($children as $child) {
				$this->addMenu($child, ++$lvl, $menu);
			}
		}
	}
}
