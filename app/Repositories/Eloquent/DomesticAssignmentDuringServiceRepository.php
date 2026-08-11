<?php
namespace App\Repositories\Eloquent;

use App\Models\DomesticAssignmentDuringService;
use App\Repositories\DomesticAssignmentDuringServiceRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;

class DomesticAssignmentDuringServiceRepository extends RepositoryBase implements DomesticAssignmentDuringServiceRepositoryInterface {
	public function __construct(DomesticAssignmentDuringService $model) {
		parent::__construct($model);
	}
}
