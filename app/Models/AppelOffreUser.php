<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppelOffreUser extends Model
{
    use HasFactory;

    protected $table = 'appel_offres';

    // Les champs pouvant être remplis via un formulaire
    protected $fillable = [
        'product_name',
        'quantity',
        'payment',
        'livraison',
        'date_tot',
        'date_tard',
        'time_start',
        'time_end',
        'day_period',
        'day_periodFin',
        'specification',
        'reference',
        'localite',
        'prodUsers',
        'code_unique',
        'montant_total',
        'code_verification',
        'count',
        'lowestPricedProduct',
        'image',
        'id_sender',
    ];

    /**
     * Relation : L'utilisateur associé à un appel d'offre.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_sender');
    }

}
