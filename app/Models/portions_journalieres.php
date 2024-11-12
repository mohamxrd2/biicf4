<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class portions_journalieres extends Model
{
    use HasFactory;

    protected $table = 'portions_journalieres';

    protected $fillable = [
        'credit_id',
        'date_portion',
        'portion_capital',
        'portion_interet',
        'id_projet_accord',
    ];

    protected $casts = [
        'date_portion' => 'date',
    ];

    /**
     * Relation avec le crédit concerné.
     */
    public function credit()
    {
        return $this->belongsTo(credits::class, 'credit_id');
    }
    public function projet()
    {
        return $this->belongsTo(projets_accordé::class, 'id_projet_accord');
    }


}
