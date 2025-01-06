<?php

namespace App\Livewire;

use App\Events\AjoutQuantiteOffre;
use App\Models\AppelOffreGrouper as ModelsAppelOffreGrouper;
use App\Models\Countdown;
use App\Models\gelement;
use App\Models\Transaction;
use App\Models\userquantites;
use App\Models\Wallet;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class Appeloffregrouper extends Component
{
    public $notification;
    public $id;
    public $appelOffreGroup;
    public $oldestCommentDate;
    public $sumquantite = 0;
    public $appelOffreGroupcount = 0;
    public $quantite;
    public $localite;
    public $groupages = [];
    public $existingQuantite;
    public $oldestComment;
    public $isOpen = false;
    public $time;
    public $error;
    protected $listeners = ['negotiationEnded' => '$refresh'];

    protected $recuperationTimer;

    // Constructeur pour initialiser RecuperationTimer
    public function boot()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }

    public function mount($id)
    {
        // Récupération de la notification
        $this->notification = DatabaseNotification::find($id);
        if (!$this->notification) {
            session()->flash('error', 'Notification introuvable.');
            return;
        }

        $this->fetchAppelOffreGrouper();
        $this->initializeGroupageData();
    }



    private function fetchAppelOffreGrouper()
    {
        $Idoffre = $this->notification->data['offre_id'] ?? null;

        if ($Idoffre) {
            $this->appelOffreGroup = ModelsAppelOffreGrouper::find($Idoffre);
        }

        $countdown = Countdown::where('code_unique', $this->appelOffreGroup->codeunique)
            ->where('is_active', false)
            ->first();
        if ($countdown && !$this->appelOffreGroup->count) {
            $this->appelOffreGroup->update(['count' => true]);
        }
    }

    private function initializeGroupageData()
    {
        if (!$this->appelOffreGroup) return;

        $codeUnique = $this->appelOffreGroup->codeunique;


        $this->reloadGroupages($codeUnique);
    }

    private function reloadGroupages($codeUnique)
    {
        $this->groupages = userquantites::where('code_unique', $codeUnique)
            ->orderBy('created_at', 'asc')
            ->get();

        $this->sumquantite = userquantites::where('code_unique', $codeUnique)
            ->sum('quantite');
        $this->appelOffreGroupcount = userquantites::where('code_unique', $codeUnique)->distinct('user_id')->count('user_id');

        $this->existingQuantite = userquantites::where('code_unique', $codeUnique)
            ->where('user_id', Auth::id())
            ->first();
    }

    #[On('echo:quantite-channel,AjoutQuantiteOffre')]
    public function actualiserDonnees($event)
    {
        if (!isset($event['codeUnique'], $event['quantite'])) {
            Log::error('Événement invalide reçu.');
            return;
        }
        $this->sumquantite += $event['quantite'];

        $this->reloadGroupages($event['codeUnique']);
        session()->flash('success', "Nouvelle quantité ajoutée avec succès !");
    }

    public function storeOffre()
    {
        DB::beginTransaction();

        try {
            $validatedData = $this->validate([
                'quantite' => 'required|integer|min:1',
                'localite' => 'nullable|string',
            ]);

            $user = Auth::id();
            $quantite = $validatedData['quantite'];

            if (!$this->appelOffreGroup) {
                session()->flash('error', 'Appel d\'offre introuvable.');
                return;
            }

            $userWallet = Wallet::where('user_id', $user)->first();
            if (!$userWallet) {
                Log::error('Portefeuille introuvable.', ['userId' => $user]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            $montantTotal = $this->appelOffreGroup->lowestPricedProduct * $quantite;

            if ($userWallet->balance < $montantTotal) {
                session()->flash('error', 'Fonds insuffisants.');
                return;
            }

            $userWallet->decrement('balance', $montantTotal);

            // Vérifier si l'utilisateur a déjà soumis une quantité pour ce code unique
            $existingQuantite = userquantites::where('code_unique', $this->appelOffreGroup->codeunique)
                ->where('user_id', $user)
                ->first();
            // Vérifier si l'utilisateur a déjà soumis une quantité pour ce code unique
            $existingGelement = gelement::where('reference_id', $this->appelOffreGroup->codeunique)
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
                    'code_unique' => $this->appelOffreGroup->codeunique,
                    'user_id' => $user,
                    'localite' => $validatedData['localite'],
                    'quantite' => $quantite,
                ]);

                // Enregistrement dans la table `gelement`
                gelement::create([
                    'id_wallet' => $userWallet->id,
                    'amount' => $montantTotal,
                    'reference_id' => $this->appelOffreGroup->codeunique,
                ]);
            }

            // Mise à jour des données locales
            $this->sumquantite += $validatedData['quantite'];

            $this->createTransaction($user, $user, 'Gele', $montantTotal, $this->generateIntegerReference(), 'Gele Pour ' . 'Groupage de ' . $this->appelOffreGroup->productName, 'effectué', 'COC');

            $this->reloadGroupages($this->appelOffreGroup->codeunique);

            // Diffusion de l'événement Laravel
            broadcast(new AjoutQuantiteOffre($validatedData['quantite'], $this->appelOffreGroup->codeunique));

            // Commit de la transaction
            DB::commit();
            // Fermer le modal
            $this->isOpen = false;
            $this->reset('quantite', 'localite');
            session()->flash('success', 'Quantité ajoutée avec succès.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erreur: " . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue.');
        }
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
