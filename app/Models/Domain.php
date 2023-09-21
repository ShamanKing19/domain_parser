<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereIn;
use Orchid\Screen\AsMultiSource;

class Domain extends Model
{
    use HasFactory, AsMultiSource, Filterable;

    protected $table = 'domains';

    public $timestamps = false;

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
        'has_basket' => Where::class
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'status',
        'cms',
        'has_ssl',
        'has_https_redirect',
        'has_catalog',
        'has_basket'
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
        'updated_at'
    ];

    public function phones() : HasMany
    {
        return $this->hasMany(\App\Models\Domain\Phone::class, 'domain_id', 'id');
    }

    public function emails() : HasMany
    {
        return $this->hasMany(\App\Models\Domain\Email::class, 'domain_id', 'id');
    }

    public function companies() : BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'domains_inns', 'domain_id', 'inn_id');
    }
}
