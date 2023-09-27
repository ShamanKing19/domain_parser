<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebsiteTypeSeeder extends Seeder
{
    public static $defaultTypeList = [
        'IT',
        'Ecom'
    ];

    public function run(): void
    {
        foreach(self::$defaultTypeList as $type) {
            DB::table('website_types')->insert([
                'name' => $type
            ]);
        }
    }
}
