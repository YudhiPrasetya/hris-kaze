<?php

namespace App\Repositories\Eloquent;

use App\Models\ReasonForLeave;
use App\Models\Repositories\ReasonForLeaveRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;
use App\Repositories\ReasonForLeaveRepositoryInterface as RepositoriesReasonForLeaveRepositoryInterface;

/**
 * Class ReasonForLeaveRepository
 *
 * @package App\Repositories\Eloquent
 */
class ReasonForLeaveRepository extends RepositoryBase implements RepositoriesReasonForLeaveRepositoryInterface{
    public function __construct(ReasonForLeave $model)
    {
        parent::__construct($model);
    }
}
