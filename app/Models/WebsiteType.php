<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsMultiSource;

class WebsiteType extends Model
{
    use HasFactory, AsMultiSource, Filterable;

    protected $table = 'website_types';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    protected $allowedSorts = [
        'id',
        'name'
    ];

    /**
     * Все домены, привязанные к данному типу
     *
     * @return HasMany
     */
    public function domains() : HasMany
    {
        return $this->hasMany(\App\Models\Domain::class, 'auto_type_id', 'id');
    }

    /**
     * Домены, привязанные к данному типу вручную
     *
     * @return HasMany
     */
    public function manualDomains() : HasMany
    {
        return $this->hasMany(\App\Models\Domain::class, 'type_id', 'id');
    }
}
