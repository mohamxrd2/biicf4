<?php

namespace App\Livewire;

use App\Events\AjoutQuantiteOffre;
use App\Models\AppelOffreGrouper as ModelsAppelOffreGrouper;
use App\Models\gelement;
use App\Models\Transaction;
use App\Models\userquantites;
use App\Models\Wallet;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Livewire\Component;

class Appeloffregrouper extends Component
{
    public $notification;
    public $id;
    public $appelOffreGroup;
    public $datePlusAncienne;
    public $sumquantite;
    public $appelOffreGroupcount;
    public $quantite;
    public $localite;
    public $selectedOption;
    public $groupages;
    public $existingQuantite;
    public $modalOpen;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $Idoffre = $this->notification->data['offre_id'] ?? null;

        // Attempt to retrieve the grouped offer by its ID
        $this->appelOffreGroup = ModelsAppelOffreGrouper::find($Idoffre);
        $codesUniques = $this->appelOffreGroup->codeunique;
        $this->datePlusAncienne = ModelsAppelOffreGrouper::where('codeunique', $codesUniques)->min('created_at');


        $this->sumquantite = userquantites::where('code_unique', $codesUniques)->sum('quantite');
        $this->appelOffreGroupcount = ModelsAppelOffreGrouper::where('codeunique', $codesUniques)
            ->distinct('user_id') // Prend uniquement les valeurs uniques de user_id
            ->count('user_id');   // Compte les valeurs distinctes

        // Charger les groupages
        $this->groupages = userquantites::where('code_unique', $this->notification->data['code_unique'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Vérifier si l'utilisateur a déjà soumis une quantité pour ce code unique
        $this->existingQuantite = userquantites::where('code_unique', $this->appelOffreGroup->codeunique)
            ->where('user_id', Auth::id())
            ->first();
    }

    protected $listeners = ['compteReboursFini'];

    public function compteReboursFini()
    {
        // Mettre à jour l'attribut 'finish' du demandeCredit
        $this->appelOffreGroup->update([
            'count' => true,
            $this->dispatch(
                'formSubmitted',
                'Temps écoule, Groupage terminé.'
            )
        ]);
    }


    public function storeOffre()
    {
        DB::beginTransaction(); // Démarrer une transaction

        try {
            // Valider les données du formulaire
            $validatedData = $this->validate([
                'quantite' => 'required|integer',
                'localite' => 'nullable|string',
            ]);

            // Récupérer l'utilisateur actuel et l'appel d'offre en cours
            $user = Auth::id();
            $appelOffreGroup = $this->appelOffreGroup;

            // Vérifier si le groupe d'appel d'offre existe
            if (!$appelOffreGroup) {
                session()->flash('error', 'Appel d\'offre introuvable.');
                return;
            }

            $userWallet = Wallet::where('user_id', $user)->first();

            if (!$userWallet) {
                Log::error('Portefeuille introuvable.', ['userId' => $user]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            // Calcul du coût total
            $prixUnitaire = $appelOffreGroup->lowestPricedProduct ?? 0;
            $quantite = $validatedData['quantite'];
            $montantTotal = $prixUnitaire * $quantite;

            // Vérifier les fonds disponibles
            if ($userWallet->balance < $montantTotal) {
                session()->flash('error', 'Fonds insuffisants pour soumettre cette quantité.');
                return;
            }

            // Décrémente le solde utilisateur
            $userWallet->decrement('balance', $montantTotal);



            // Vérifier si l'utilisateur a déjà soumis une quantité pour ce code unique
            $existingQuantite = userquantites::where('code_unique', $appelOffreGroup->codeunique)
                ->where('user_id', $user)
                ->first();
            // Vérifier si l'utilisateur a déjà soumis une quantité pour ce code unique
            $existingGelement = gelement::where('reference_id', $appelOffreGroup->codeunique)
                ->where('id_wallet', $userWallet->id)
                ->first();

            if ($existingQuantite) {
                // Mise à jour de la quantité existante
                $existingQuantite->quantite += $quantite;
                $existingQuantite->save();

                if ($existingQuantite) {
                    $existingGelement->amount += $montantTotal;
                    $existingGelement->save();
                }
            } else {
                // Création d'un nouvel enregistrement
                userquantites::create([
                    'code_unique' => $appelOffreGroup->codeunique,
                    'user_id' => $user,
                    'localite' => $validatedData['localite'],
                    'quantite' => $quantite,
                ]);

                // Enregistrement dans la table `gelement`
                gelement::create([
                    'id_wallet' => $userWallet->id,
                    'amount' => $montantTotal,
                    'reference_id' => $appelOffreGroup->codeunique,
                ]);
            }


            $this->createTransaction($user, $user, 'Gele', $montantTotal, $this->generateIntegerReference(), 'Gele Pour ' . 'Groupage de ' . $appelOffreGroup->productName, 'effectué', 'COC');


            // Mise à jour des données pour le composant Livewire
            $this->groupages = userquantites::where('code_unique', $appelOffreGroup->codeunique)
                ->orderBy('created_at', 'asc')
                ->get();
            $this->sumquantite = ModelsAppelOffreGrouper::where('codeunique', $appelOffreGroup->codeunique)
                ->sum('quantity');
            $this->appelOffreGroupcount = ModelsAppelOffreGrouper::where('codeunique', $appelOffreGroup->codeunique)
                ->distinct('user_id')
                ->count('user_id');

            // Réinitialiser les champs du formulaire
            $this->reset('quantite', 'localite');

            // Fermer le modal
            $this->modalOpen = false;

            // Commit de la transaction
            DB::commit();

            // Message de succès
            session()->flash('success', 'Quantité ajoutée ou mise à jour avec succès');
        } catch (Exception $e) {
            // Annuler les changements
            DB::rollBack();

            // Log l'erreur et afficher un message d'erreur
            Log::error('Erreur lors de l\'ajout ou mise à jour : ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de l\'ajout ou mise à jour de la quantité.');
        }
    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status,  string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->type_compte = $type_compte;
        $transaction->status = $status;
        $transaction->save();
    }
    public function render()
    {
        return view('livewire.appeloffregrouper');
    }
}
