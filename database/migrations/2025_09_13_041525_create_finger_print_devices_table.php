<?php

use App\Helpers\MigrationBase;
// use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class CreateFingerPrintDevicesTable extends MigrationBase
{
    public function __construct(){
        parent::__construct('master', 'finger_print_devices');
    }

    // public function up()
    // {
    //     Schema::create('finger_print_devices', function (Blueprint $table) {
    //         $table->id();
    //         $table->integer('no');
    //         $table->string('ip_address');
    //         $table->integer('port');
    //         $table->string('description');
    //         $table->timestamps();
    //         $table->softDeletes();
    //     });
    // }

    /**
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param \Illuminate\Database\Schema\Builder $schema
     *
     * @throws \App\Exceptions\SchemaNoFoundException
     * @throws \App\Exception\TableNotFoundException
     */
    protected function create(Blueprint $table, Builder $schema){
        $table->id();
        $table->integer('no');
        $table->string('ip_address');
        $table->integer('port');
        $table->string('description');
        $table->timestamps();
        $table->softDeletes();
    }



}
