<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'actor_type',
        'investissement',
        'phone',
        'continent',
        'sous_region',
        'country',
        'departe',
        'ville',
        'commune',
        'parrain',
        'active_zone',
        'photo',
        'last_seen',
        'admin_id'
    ];



    public function Admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function produitService()
    {
        return $this->hasMany(ProduitService::class, 'user_id');
    }
    public function consommation()
    {
        return $this->hasMany(Consommation::class, 'id_user');
    }

    // Définition de la relation parrain
    public function parrain()
    {
        return $this->belongsTo(User::class, 'parrain');
    }

    public function parrainees()
    {
        return $this->hasMany(User::class, 'parrain');
    }

    public function achatsDirectsEnvoyes()
    {
        return $this->hasMany(AchatDirect::class, 'userSender');
    }

    public function achatsDirectsReçus()
    {
        return $this->hasMany(AchatDirect::class, 'userTrader');
    }
    public function countdown()
    {
        return $this->hasOne(Countdown::class,'user_id');
    }
    public function userQuantites()
    {
        return $this->hasMany(userquantites::class, 'user_id');
    }

    public function livraisons()
    {
        return $this->hasMany(livraisons::class, 'user_id'); // Correction ici
    }

    public function psaps()
    {
        return $this->hasMany(Psap::class, 'user_id');
    }
}
