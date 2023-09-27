<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('website_type_keywords', function (Blueprint $table) {
            $table->foreignId('type_id')->references('id')->on('website_types');
            $table->string('word');
            $table->primary(['type_id', 'word']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_type_keywords');
        Schema::dropIfExists('website_types');
    }
};
