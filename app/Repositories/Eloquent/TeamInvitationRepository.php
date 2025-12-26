<?php
/**
 * This file is part of the Laravel project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   TeamInvitationRepository
 * @date   22/03/2021 16:15
 */

namespace App\Repositories\Eloquent;

use App\Models\TeamInvitation;
use App\Repositories\TeamInvitationRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class TeamInvitationRepository
 *
 * @package App\Repositories\Eloquent
 */
class TeamInvitationRepository extends RepositoryBase implements TeamInvitationRepositoryInterface {
	public function __construct(TeamInvitation $model) {
		parent::__construct($model);
	}
}
