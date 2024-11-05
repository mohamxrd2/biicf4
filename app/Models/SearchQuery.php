<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    use HasFactory;

    protected $table = 'search_queries';

    protected $fillable = [
        'query',
        'created_at',
        'updated_at',
        'nombre_posts',
    ];
}
