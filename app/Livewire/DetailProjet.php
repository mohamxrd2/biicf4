<?php

namespace App\Livewire;

use App\Models\Cfa;
use App\Models\Projet;
use App\Models\Wallet;
use Livewire\Component;
use App\Models\CommentTaux;
use App\Models\Transaction;
use App\Models\AjoutMontant;
use App\Models\Countdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DetailProjet extends Component
{
    public $projet;
    public $images = [];
    public $montant = ''; // Stocke le montant saisi
    public $solde; // Stocke le solde de l'utilisateur
    public $insuffisant = false; // Pour vérifier si le solde est insuffisant
    public $nombreInvestisseursDistinct = 0;
    public $sommeRestante = 0;

    public $pourcentageInvesti = 0;

    public $sommeInvestie = 0;
    public $montantVerifie = false;

    public $tauxTrade;
    public $commentTauxList = [];

    public function mount($id)
    {
        $this->projet = Projet::with('demandeur')->find($id);
        $this->images = array_filter([
            $this->projet->photo1,
            $this->projet->photo2,
            $this->projet->photo3,
            $this->projet->photo4,
            $this->projet->photo5 // Ajoutez autant de photos que vous avez dans la base de données
        ]);

        $userId = Auth::id();

        $wallet = Wallet::where('user_id', $userId)->first();

        $this->solde = $wallet ? $wallet->balance : 0;

        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');

        $this->sommeInvestie = AjoutMontant::where('id_projet', $this->projet->id)
            ->sum('montant'); // Somme des montants investis

        // Calculer le pourcentage investi
        if ($this->projet->montant > 0) {
            $this->pourcentageInvesti = ($this->sommeInvestie / $this->projet->montant) * 100; // Calculer le pourcentage investi
        } else {
            $this->pourcentageInvesti = 0; // Si le montant est 0, le pourcentage est 0
        }

        // Calculer la somme restante à investir
        $this->sommeRestante = $this->projet->montant - $this->sommeInvestie; // Montant total - Somme investie

        $this->montantVerifie = AjoutMontant::where('id_projet', $this->projet->id)
            ->where('montant', $this->projet->montant) // Assurez-vous que le champ 'montant' existe dans votre modèle
            ->exists(); // Renvoie true si le montant existe



        $this->commentTauxList = CommentTaux::with('investisseur') // Assurez-vous que la relation est définie dans le modèle CommentTaux
            ->where('id_projet', $this->projet->id)
            ->orderBy('taux', 'asc') // Trier du plus petit au plus grand (ordre croissant)
            ->get();
    }

    public function updatedMontant()
    {
        // Vérifier si le montant saisi dépasse le solde
        $this->insuffisant = !empty($this->montant) && $this->montant > $this->solde;
    }

    public function confirmer()
    {
        // Vérifier que le montant est valide, non vide, numérique et supérieur à zéro
        $montant = floatval($this->montant); // Convertir le montant en float

        // if (empty($this->montant) || !is_numeric($montant) || $montant <= 0) {
        //     session()->flash('error', 'Veuillez saisir un montant valide.');
        //     return;
        // }

        // Récupérer le projet et le wallet de l'utilisateur (investisseur)
        $projet = $this->projet;

        // Vérifiez si le projet et le demandeur existent
        if (!$projet || !$projet->demandeur->id) {
            session()->flash('error', 'Le projet ou le demandeur est introuvable.');
            return;
        }

        $wallet = Wallet::where('user_id', Auth::id())->first();
        // Récupérer le wallet de l'utilisateur demandeur
        $walletDemandeur = Wallet::where('user_id', $this->projet->id_user)->first();

        // Vérifier que l'utilisateur possède un wallet
        if (!$wallet) {
            session()->flash('error', 'Votre portefeuille est introuvable.');
            return;
        }

        // Vérifier que le solde du wallet est suffisant
        if ($wallet->balance < $montant) {
            session()->flash('error', 'Votre solde est insuffisant pour cette transaction.');
            return;
        }

        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        // Sauvegarder le montant dans la table `ajout_montant`
        try {
            $ajoumontant = AjoutMontant::create([
                'montant' => $montant, // Utilisez la valeur float
                'id_invest' => Auth::id(),
                'id_emp' => $projet->demandeur->id, // Vérifiez que cela n'est pas nul
                'id_projet' => $projet->id,
            ]);

            // // Mettre à jour le solde du wallet de l'investisseur
            // $wallet->balance -= $montant;
            // $wallet->save();

            // Mettre à jour le solde du COI (Compte des Opérations d'Investissement)
            $coi = $wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte
            if ($coi) {
                $coi->Solde -= $montant; // Débiter le montant du solde du COI
                $coi->save();
            }

            // Mettre à jour ou créer un enregistrement dans la table CFA
            $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            // Mettre à jour ou créer un enregistrement dans la table CFA
            if ($cfa) {
                // Si le compte CFA existe, on additionne le montant
                $cfa->Solde += $montant; // Ajoute le montant au solde existant dans le CFA
                $cfa->save();
            }

            $reference_id = $this->generateIntegerReference();

            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Envoie', $montant, $reference_id,  'financement  de credit d\'achat',  'effectué');
            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Reception', $montant, $reference_id,  'reception de financement  de credit d\'achat',  'effectué');

            // Committer la transaction
            DB::commit();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }

        // Mettre à jour le solde de l'utilisateur (investisseur)
        // $wallet->balance -= $montant; // Utilisez la valeur float
        // $wallet->save();

        // $this->createTransaction(Auth::id(), $projet->demandeur->id, 'Envoie', $montant);

        // Message de succès
        session()->flash('success', 'Le montant a été ajouté avec succès.');

        // Réinitialiser le montant saisi et le drapeau de vérification de solde insuffisant
        $this->montant = '';
        $this->insuffisant = false;

        // Rafraîchir les propriétés du composant
        $this->sommeInvestie = AjoutMontant::where('id_projet', $this->projet->id)->sum('montant'); // Met à jour la somme investie
        $this->sommeRestante = $this->projet->montant - $this->sommeInvestie; // Met à jour la somme restante
        $this->pourcentageInvesti = ($this->sommeInvestie / $this->projet->montant) * 100; // Met à jour le pourcentage investi

        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');

        $this->montantVerifie = AjoutMontant::where('id_projet', $this->projet->id)
            ->where('montant', $this->projet->montant) // Assurez-vous que le champ 'montant' existe dans votre modèle
            ->exists(); // Renvoie true si le montant existe
        if ($this->montantVerifie) {
            Countdown::create([
                'user_id' => Auth::id(), // Utilisez la valeur float
                'userSender' => $this->projet->demandeur->id,
                'start_time' => now(), // Vérifiez que cela n'est pas nul
                'code_unique' => $this->projet->id,
                'difference' => 'taux_projet',
            ]);
        }
    }

    public function commentForm()
    {
        // Validation du champ tauxTrade
        $this->validate([
            'tauxTrade' => 'required|numeric|min:0',
        ]);

        // Vérification de l'objet projet
        if (!$this->projet || !$this->projet->id) {
            session()->flash('error', 'Le projet est introuvable.');
            return;
        }

        // Insérer dans la table commentTaux
        try {
            CommentTaux::create([
                'taux' => $this->tauxTrade,
                'id_invest' => auth()->id(),
                'id_emp' => $this->projet->id_user,
                'id_projet' => $this->projet->id,
            ]);

            // Réinitialiser le champ tauxTrade après l'insertion
            $this->tauxTrade = '';

            // Optionnel: Ajouter une notification ou un message de succès
            session()->flash('message', 'Commentaire sur le taux ajouté avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout du commentaire: ' . $e->getMessage());
        }

        // Commenter cette ligne une fois que vous avez vérifié

        $this->commentTauxList = CommentTaux::with('investisseur') // Assurez-vous que la relation est définie dans le modèle CommentTaux
            ->where('id_projet', $this->projet->id)
            ->orderBy('taux', 'asc') // Trier du plus petit au plus grand (ordre croissant)
            ->get();
    }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = $status;
        $transaction->save();
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }

    public function joursRestants()
    {
        $dateFin = \Carbon\Carbon::parse($this->projet->durer);
        $dateActuelle = now();
        $joursRestants = $dateActuelle->diffInDays($dateFin);
        return max(0, $joursRestants); // Retournez 0 si le projet est déjà terminé
    }

    public function render()
    {
        return view('livewire.detail-projet', [
            'joursRestants' => $this->joursRestants(),
            'images' => $this->images,
            'nombreInvestisseurs' => $this->nombreInvestisseursDistinct,
            'sommeRestante' => $this->sommeRestante,
            'pourcentageInvesti' => $this->pourcentageInvesti,
        ]);
    }
}
