<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsMultiSource;

class ProcessingStatus extends Model
{
    use HasFactory, AsMultiSource, Filterable;

    protected $table = 'processing_statuses';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    protected $allowedSorts = [
        'id',
        'name'
    ];
}
