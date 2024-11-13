<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class portions_journalieres extends Model
{
    use HasFactory;

    protected $table = 'portions_journalieres';

    protected $fillable = [
        'id_user',
        'date_portion',
        'portion_capital',
        'portion_interet',
    ];

    protected $casts = [
        'date_portion' => 'date',
    ];

    /**
     * Relation avec le crédit concerné.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }



}
