<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteType extends Model
{
    use HasFactory;

    protected $table = 'website_types';

    protected $fillable = [
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
