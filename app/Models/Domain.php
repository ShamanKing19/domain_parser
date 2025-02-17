<?php

namespace App\Models;

use App\Models\Domain\Email;
use App\Models\Domain\Phone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereIn;
use Orchid\Filters\Types\WhereMaxMin;
use Orchid\Screen\AsMultiSource;

class Domain extends Model
{
    use HasFactory, AsMultiSource, Filterable;

    public $timestamps = false;
    protected $table = 'domains';
    protected $perPage = 500;

    protected $allowedFilters = [
        'id' => Where::class,
        'domain' => Like::class,
        'real_domain' => Like::class,
        'status' => WhereIn::class,
        'cms' => WhereIn::class,
        'title' => Like::class,
        'description' => Like::class,
        'keywords' => Like::class,
        'has_ssl' => Where::class,
        'has_https_redirect' => Where::class,
        'has_catalog' => Where::class,
        'has_basket' => Where::class,
        'type_id' => Where::class,
        'processing_status_id' => Where::class,
        'income' => WhereMaxMin::class
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'domain',
        'real_domain',
        'status',
        'cms',
        'title',
        'description',
        'keywords',
        'has_ssl',
        'has_https_redirect',
        'has_catalog',
        'has_basket',
        'type_id',
        'processing_status_id',
        'income'
    ];

    protected $fillable = [
        'domain',
        'real_domain',
        'status',
        'cms',
        'title',
        'description',
        'keywords',
        'ip',
        'country',
        'city',
        'hosting',
        'has_ssl',
        'has_https_redirect',
        'has_catalog',
        'has_basket',
        'type_id',
        'auto_type_id',
        'processing_status_id',
        'income',
        'updated_at'
    ];

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class, 'domain_id', 'id');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'domain_id', 'id');
    }

    /**
     * Тип сайта, установленный вручную
     *
     * @return HasOne
     */
    public function type(): HasOne
    {
        return $this->hasOne(WebsiteType::class, 'id', 'type_id');
    }

    /**
     * Статус обработки
     *
     * @return HasOne
     */
    public function processingStatus(): HasOne
    {
        return $this->hasOne(ProcessingStatus::class, 'id', 'processing_status_id');
    }

    /**
     * Получение компании с наибольшей выручкой (зачастую компания одна, поэтому так)
     *
     * @return Company|null
     */
    public function getCompanyAttribute(): Company|null
    {
        return $this->companies()->get()->sortBy(function ($value) {
            return $value->last_finance_year_income;
        }, SORT_REGULAR, true)->first();
    }

    /**
     * Компании, привязанные к домену
     *
     * @return BelongsToMany
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'domains_inns', 'domain_id', 'inn_id');
    }

    /**
     * Получение выручки с последнего года финансовой отчётности
     *
     * @return float|mixed
     */
    public function getLastYearIncomeAttribute()
    {
        return $this->company->last_finance_year_income ?? 0.0;
    }
}
