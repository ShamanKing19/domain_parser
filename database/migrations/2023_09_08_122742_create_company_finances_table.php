<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyFinancesTable extends Migration
{
    public function up()
    {
        Schema::create('company_finances', function (Blueprint $table) {
            $table->foreignId('inn_id');
            $table->foreign('inn_id')->references('id')->on('company_info')->cascadeOnDelete();
            $table->smallInteger('year');
            $table->unsignedFloat('income', 20);
            $table->unsignedFloat('outcome', 20);
            $table->float('profit', 20);
            $table->primary(['inn_id', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_finances');
    }
}
