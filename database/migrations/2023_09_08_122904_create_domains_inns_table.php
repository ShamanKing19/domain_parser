<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsInnsTable extends Migration
{
    public function up()
    {
        Schema::create('domains_inns', function (Blueprint $table) {
            $table->foreignId('domain_id');
            $table->foreign('domain_id')->references('id')->on('domains')->cascadeOnDelete();
            $table->foreignId('inn_id');
            $table->foreign('inn_id')->references('id')->on('company_info')->cascadeOnDelete();
            $table->primary(['domain_id', 'inn_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('domains_inns');
    }
}
