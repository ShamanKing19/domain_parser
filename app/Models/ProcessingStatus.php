<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsMultiSource;

class ProcessingStatus extends Model
{
    use HasFactory, AsMultiSource, Filterable;

    public $timestamps = false;
    protected $table = 'processing_statuses';
    protected $fillable = [
        'name'
    ];

    protected $allowedSorts = [
        'id',
        'name'
    ];
}
