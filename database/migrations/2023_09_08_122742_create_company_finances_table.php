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
            $table->unsignedFloat('income');
            $table->unsignedFloat('outcome');
            $table->unsignedFloat('profit');
            $table->primary(['inn_id', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_finances');
    }
}
