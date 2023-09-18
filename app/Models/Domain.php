<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsMultiSource;

class Domain extends Model
{
    use HasFactory, AsMultiSource, Filterable;

    protected $table = 'domains';

    public $timestamps = false;

    protected $perPage = 500;

    protected $allowedFilters = [
        'cms' => Like::class,
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'cms',
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
