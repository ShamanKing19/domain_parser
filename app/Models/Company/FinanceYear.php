<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceYear extends Model
{
    use HasFactory;

    protected $table = 'company_finances';

    protected $fillable = [
        'inn_id',
        'year',
        'income',
        'outcome',
        'profit'
    ];
}
