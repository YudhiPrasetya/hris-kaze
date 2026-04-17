<?php

namespace App\Models;

class PPh21TerbaruKategoriA extends ModelBase{
    protected $fillable = [
        'penghasilan_bruto_bulanan',
        'ter'
    ];

    public function __construct(array $attributes = [])
    {
        $this->setConnection(connection('master'));
        $this->setTable(table('master.pph21_tarif_bulanan_a', onlyName: true));

        parent::__construct($attributes);
    }
}