<?php

namespace App\Models;

use App\Models\Company\FinanceYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company_info';

    public $timestamps = false;

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
        'last_finance_year',
        'updated_at'
    ];

    public function segment() : HasOne
    {
        return $this->hasOne(\App\Models\Company\FinanceSegment::class, 'id', 'segment_id');
    }

    public function financeYears() : HasMany
    {
        return $this->hasMany(FinanceYear::class, 'inn_id', 'id');
    }

    public function domains() : BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'domains_inns', 'inn_id', 'domain_id');
    }
}
