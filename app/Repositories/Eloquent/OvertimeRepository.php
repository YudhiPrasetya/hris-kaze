<?php

namespace App\Repositories\Eloquent;

use App\Models\Overtime;
use App\Repositories\OvertimeRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;

/**
 * Class OvertimeRepository
 *
 * @package App\Repositories\Eloquent
 */
class OvertimeRepository extends RepositoryBase implements OvertimeRepositoryInterface{
    public function __construct(Overtime $model)
    {
        return parent::__construct($model);
    }
}
