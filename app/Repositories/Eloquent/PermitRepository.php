<?php

namespace App\Repositories\Eloquent;

use App\Models\Permit;
use App\Repositories\PermitRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;

/**
 * Class PermitRepository
 *
 * @package App\Repositories\Eloquent
 */
class PermitRepository extends RepositoryBase implements PermitRepositoryInterface{
    public function __construct(Permit $model){
        parent::__construct($model);
    }
}
