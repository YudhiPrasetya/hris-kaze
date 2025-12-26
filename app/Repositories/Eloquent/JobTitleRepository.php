<?php

namespace App\Repositories\Eloquent;

use App\Models\JobTitle;
use App\Models\Repositories\JobTitleRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;
use App\Repositories\JobTitleRepositoryInterface as RepositoriesJobTitleRepositoryInterface;

/**
 * Class JobTitleRepository
 *
 * @package App\Repositories\Eloquent
 */
class JobTitleRepository extends RepositoryBase implements RepositoriesJobTitleRepositoryInterface{
    public function __construct(JobTitle $model){
        parent::__construct($model);
    }
}
