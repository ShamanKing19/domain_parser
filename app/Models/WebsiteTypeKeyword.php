<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteTypeKeyword extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'website_type_keywords';
    protected $fillable = [
        'type_id',
        'word'
    ];
}
