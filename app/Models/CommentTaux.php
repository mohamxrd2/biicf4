<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentTaux extends Model
{
    use HasFactory;

    protected $table = 'comment_taux';

    protected $fillable = [
        'taux',
        'code_unique',
        'id_invest',
        'id_emp',
        'id_projet',
        'id_demande_credit',
    ];

    /**
     * Relation avec l'investisseur (User)
     */
    public function investisseur()
    {
        return $this->belongsTo(User::class, 'id_invest');
    }

    /**
     * Relation avec l'emprunteur (User)
     */
    public function emprunteur()
    {
        return $this->belongsTo(User::class, 'id_emp');
    }
}
