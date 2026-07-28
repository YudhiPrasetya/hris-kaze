<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends ModelBase{
    use HasFactory;

    protected $fillable = [
        'country', 'currency', 'currency_code'
    ];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.currencies', onlyName: true));

		parent::__construct($attributes);
	}

}
