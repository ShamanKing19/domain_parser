<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $table = 'domains';

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
        'has_basket'
    ];
}
