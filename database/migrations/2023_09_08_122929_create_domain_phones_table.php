<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainPhonesTable extends Migration
{
    public function up()
    {
        Schema::create('domain_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id');
            $table->foreign('domain_id')->references('id')->on('domains')->cascadeOnDelete();
            $table->string('phone');
        });
    }

    public function down()
    {
        Schema::dropIfExists('domain_phones');
    }
}
