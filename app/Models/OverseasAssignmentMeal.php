<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OverseasAssignmentMeal extends ModelBase{
    use HasFactory;

    protected $fillable = [
        'position_id', 'amountJPY'
    ];

    public function __construct(array $attributes = []){
        $this->setConnection(connection('master'));
        $this->setTable(table('master.overseas_assignment_meal', onlyName: true));

        parent::__construct($attributes);
    }
}
