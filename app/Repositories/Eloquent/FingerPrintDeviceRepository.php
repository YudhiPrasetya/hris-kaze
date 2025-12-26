<?php

namespace App\Repositories\Eloquent;

use App\Models\FingerPrintDevice;

use App\Repositories\FingerPrintDeviceRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;

/**
 * Class FingerprintDeviceRepository
 *
 * @package App\Repositories\Eloquent
 */
class FingerPrintDeviceRepository extends RepositoryBase implements FingerPrintDeviceRepositoryInterface{
    public function __construct(FingerPrintDevice $model){
        parent::__construct($model);
    }
}
