<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchatDirect extends Model
{
    use HasFactory;

    protected $table = 'achat_direct';

    protected $fillable = [
        'photoProd',
        'nameProd',
        'quantité',
        'montantTotal',
        'message',
        'localite',
        'code_unique',
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
