<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_user_id',
        'sender_admin_id',
        'receiver_user_id',
        'receiver_admin_id',
        'type',
        'type_compte',
        'amount',
        'reference_id',
        'description',
        'status',
    ];

    // Relation avec l'utilisateur (envoyeur)
    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    // Relation avec l'administrateur (envoyeur)
    public function senderAdmin()
    {
        return $this->belongsTo(Admin::class, 'sender_admin_id');
    }

    // Relation avec l'utilisateur (receveur)
    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    // Relation avec l'administrateur (receveur)
    public function receiverAdmin()
    {
        return $this->belongsTo(Admin::class, 'receiver_admin_id');
    }
}
