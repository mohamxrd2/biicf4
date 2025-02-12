<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class remboursements extends Model
{
    use HasFactory;

    protected $table = 'remboursements';

    protected $fillable = [
        'projet_id',
        'credit_id',
        'creditGrp_id',
        'id_user',
        'montant_capital',
        'montant_interet',
        'date_remboursement',
        'statut',
        'description',
    ];

    protected $casts = [
        'date_remboursement' => 'date',
    ];

    /**
     * Relation avec le crédit concerné.
     */
    public function credit()
    {
        return $this->belongsTo(credits::class, 'credit_id');
    }
    public function creditgrp()
    {
        return $this->belongsTo(credits_groupé::class, 'creditGrp_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

}
