<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceSegment extends Model
{
    use HasFactory;

    protected $table = 'company_finance_segments';

    protected $fillable = [
        'name'
    ];
}
