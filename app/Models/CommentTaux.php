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
<<<<<<< HEAD
        'id_projet',
=======
        'id_projet'
>>>>>>> 2835338f26257b82903a50f2f99891230fd73d43
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
