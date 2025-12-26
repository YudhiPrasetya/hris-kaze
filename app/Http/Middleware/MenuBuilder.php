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
 * @file   MenuBuilder.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\Middleware;

use App\Managers\Menu\Builder;
use App\Managers\Menu\Item;
use App\Managers\Menu\Menu;
use App\Models\Menu as MenuModel;
use App\Models\Role;
use App\Repositories\Eloquent\MenuRepository;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MenuBuilder {
	/**
	 * @var \App\Repositories\Eloquent\MenuRepository
	 */
	private MenuRepository $repository;

	/**
	 * @var \App\Managers\Menu\Menu
	 */
	private Menu $menu;

	public function __construct(MenuRepository $repository, Menu $menu) {
		$this->repository = $repository;
		$this->menu = $menu;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next) {
		if (Auth::check()) {
			$self = $this;
			/**
			 * @var $user Authenticatable
			 */
			$user = Auth::user();
			$role = $user->roles()->first();
			$parents = $this->repository->parentOnly();

			// @formatter:off
			$this->menu->make('SidebarMenu', function (Builder $m) use ($parents, $self, $role) {
				foreach ($parents as $model) {
					$self->createMenu($m, $model, $role);
				}
			});
			// @formatter:on
		}

		return $next($request);
	}

	/**
	 * @param Builder|Item $menu
	 * @param MenuModel    $model
	 * @param Role         $role
	 */
	private function createMenu($menu, MenuModel &$model, Role $role) {
		if ($model->enabled) {
			if ($role->level >= $model->role()->level) {
				/**
				 * @var $self \App\Http\Middleware\MenuBuilder
				 */
				$self = $this;
				$options = array_merge($model->attrs->__toArray(), ['route' => $model->route,]);
				$options = array_remove_empty($options);

				if ($model->hasChildren()) {
					$item = $menu->add($model->title, array_merge($options));

					foreach ($model->children() as $child) {
						$self->createMenu($item, $child, $role);
					}
				}
				else if ($model->divider) {
					if (is_a($menu, Item::class)) {
						$menu->divide();
					}
					else {
						if (!$menu->last()->isDivider())
							$menu->divide();
					}
				}
				else {
					$menu->add($model->title, $options);
				}
			}
		}
	}
}
