<?php

namespace App\Repositories\Eloquent;

use App\Models\Leave;
use App\Repositories\LeaveRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LeaveRepository
 *
 * @package App\Repositories\Eloquent
 */
class LeaveRepository extends RepositoryBase implements LeaveRepositoryInterface{
    public function __construct(Leave $model)
        {
            return parent::__construct($model);
        }
}
