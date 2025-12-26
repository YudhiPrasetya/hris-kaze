<?php

namespace App\Repositories\Eloquent;

use App\Models\FingerPrintDeviceData;

use App\Repositories\FingerPrintDeviceDataRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;

/**
 * Class FingerPrintDeviceDataRepository
 *
 * @package App\Repositories\Eloquent
 */
class FingerPrintDeviceDataRepository extends RepositoryBase implements FingerPrintDeviceDataRepositoryInterface{
    public function __construct(FingerPrintDeviceData $model)
    {
        parent::__construct($model);
    }
}
