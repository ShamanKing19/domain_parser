<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company_info';

    protected $fillable = [
        'inn',
        'name',
        'type',
        'segment_id',
        'segment_id',
        'region',
        'city',
        'address',
        'post_index',
        'registration_date',
        'boss_name',
        'boss_post',
        'authorized_capital_type',
        'authorized_capital_amount',
        'registry_date',
        'registry_category',
        'employees_count',
        'main_activity',
        'last_finance_year'
    ];
}
