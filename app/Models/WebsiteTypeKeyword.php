<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteTypeKeyword extends Model
{
    use HasFactory;

    protected $table = 'website_type_keywords';

    public $timestamps = false;

    protected $fillable = [
        'type_id',
        'word'
    ];
}
