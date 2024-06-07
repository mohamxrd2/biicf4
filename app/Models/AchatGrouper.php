<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchatGrouper extends Model
{
    use HasFactory;

    protected $table = 'achat_group';

    protected $fillable = [

        'photoProd',
        'nameProd',
        'quantité',
        'montantTotal',
        'message',
        'localite',
        'reponse',
        'userTrader',
        'userSender',
        'idProd',
        'specificite',
    ];

    public function userTrader()
    {
        return $this->belongsTo(User::class, 'userTrader');
    }

    public function userSender()
    {
        return $this->belongsTo(User::class, 'userSender');
    }

    public function produit()
    {
        return $this->belongsTo(ProduitService::class, 'idProd');
    }
}
