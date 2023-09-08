<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyInfoTable extends Migration
{
    public function up()
    {
        Schema::create('company_info', function (Blueprint $table) {
            $table->id();
            $table->string('inn')->unique();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->foreignId('segment_id')->nullable();
            $table->foreign('segment_id')->references('id')->on('company_finance_segments');
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('post_index')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('boss_name')->nullable();
            $table->string('boss_post')->nullable();
            $table->string('authorized_capital_type')->nullable();
            $table->unsignedInteger('authorized_capital_amount')->nullable();
            $table->date('registry_date')->nullable();
            $table->unsignedTinyInteger('registry_category')->nullable();
            $table->unsignedMediumInteger('employees_count')->nullable();
            $table->mediumText('main_activity')->nullable();
            $table->smallInteger('last_finance_year')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_info');
    }
}
