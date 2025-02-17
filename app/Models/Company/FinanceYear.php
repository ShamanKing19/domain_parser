<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsMultiSource;

class FinanceYear extends Model
{
    use HasFactory, AsMultiSource;

    public $timestamps = false;
    protected $table = 'company_finances';
    protected $fillable = [
        'inn_id',
        'year',
        'income',
        'outcome',
        'profit'
    ];
}
