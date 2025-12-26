<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

class CreateJobTitleTable extends MigrationBase
{
    public function __construct()
    {
        parent::__construct('master', 'job_titles');
    }

    protected function create(Blueprint $table, Builder $schema): void
    {
        $table->id();
        $table->string('name');
        $table->string('description');
        $table->timestamps();
        $table->softDeletes();
    }
}
