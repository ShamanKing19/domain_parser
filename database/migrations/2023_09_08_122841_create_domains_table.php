<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('real_domain')->nullable();
            $table->integer('status')->nullable();
            $table->string('cms')->nullable();
            $table->string('title')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('keywords')->nullable();
            $table->string('ip')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('hosting')->nullable();
            $table->boolean('has_ssl')->default(false);
            $table->boolean('has_https_redirect')->default(false);
            $table->boolean('has_catalog')->default(false);
            $table->boolean('has_basket')->default(false);
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('domains');
    }
}
