<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countdown extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'start_time', 'notified', 'code_unique', 'userSender', 'nsender', 'difference'];

    // Cast nsender as array
    protected $casts = [
        'nsender' => 'array',
    ];

    // Relation to senders
    public function senders()
    {
        return $this->belongsToMany(User::class, 'nsender' )
            ->whereIn('id', $this->nsender);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'userSender');
    }
}
