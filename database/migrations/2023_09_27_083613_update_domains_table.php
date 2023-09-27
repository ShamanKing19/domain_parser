<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            // Поле для заполнения вручную
            $table->foreignId('type_id')
                ->nullable()
                ->after('has_basket')
                ->references('id')
                ->on('website_types');

            // Автоматически определяемое поле
            $table->foreignId('auto_type_id')
                ->nullable()
                ->after('type_id')
                ->references('id')
                ->on('website_types');
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropConstrainedForeignId('type_id');
            $table->dropConstrainedForeignId('auto_type_id');
        });
    }
};
