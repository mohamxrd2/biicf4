<?php

namespace App\Livewire;

use App\Models\DemandeCredi;
use App\Models\Gelement;
use App\Models\Investisseur;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\DemandeCreditNotification;
use App\Services\generateIntegerReference;
use App\Services\generateUniqueReference;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Demandecredit extends Component
{
    public $referenceCode, $price, $creditotal, $duration, $financementType,
        $username, $bailleur, $startDate, $startTime, $endDate, $endTime, $roi,
        $quantite, $search = '', $users = [], $user_id, $sommedemnd, $montantmax,
        $quantiteMax, $nameProd, $quantiteMin, $messages = [];


    public function mount()
    {
        $data = session('eligibility_data');

        if ($data) {
            $this->handleEligibility(
                $data['prix'],
                $data['montantmax'],
                $data['quantiteMax'],
                $data['quantiteMin'],
                $data['nameProd']
            );
        }
    }

    public function handleEligibility($prix, $montantmax, $quantiteMax, $quantiteMin, $nameProd)
    {
        $this->sommedemnd = $prix;
        $this->montantmax = $montantmax;
        $this->quantiteMax = $quantiteMax;
        $this->quantiteMin = $quantiteMin;
        $this->nameProd = $nameProd;
        $this->referenceCode = $this->generateReferenceCode();
    }

    // Méthode appelée lors de la mise à jour de la recherche
    public function updatedSearch()
    {
        if (!empty($this->search)) {
            // Récupérer l'ID de l'utilisateur connecté
            $currentUserId = auth()->id();

            // Recherche des utilisateurs dont le nom d'utilisateur correspond à la saisie,
            // mais exclure l'utilisateur connecté
            $this->users = User::where('username', 'like', '%' . $this->search . '%')
                ->where('id', '!=', $currentUserId) // Exclure l'utilisateur connecté
                ->get();

            Log::info('Search updated.', ['search' => $this->search]);
        } else {
            // Si la barre de recherche est vide, ne rien afficher
            $this->users = [];
        }
    }

    // Méthode pour sélectionner un utilisateur
    public function selectUser($userId, $userName)
    {
        $this->user_id = $userId;
        $this->search = $userName;
        $this->users = [];
    }

    public function submit()
    {
        // Validation améliorée avec des règles dynamiques pour quantite
        $this->validate([
            'roi' => 'required|numeric|min:5',
            'quantite' => 'required|numeric|min:' . $this->quantiteMin . '|max:' . $this->quantiteMax,
            'sommedemnd' => 'required|numeric',
            'financementType' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'bailleur' => 'nullable|string',
            'endDate' => 'required|date',
            'duration' => 'required|date',
        ], [
            'roi.min' => 'Le retour sur investissement doit être supérieur à 5.',
            'quantite.min' => 'La quantité minimum est de ' . $this->quantiteMin,
            'quantite.max' => 'La quantité maximum est de ' . $this->quantiteMax,
        ]);

        try {
            // Calculs - déplacés en haut pour être disponibles dans tout le bloc
            $montantTotal = $this->sommedemnd * $this->quantite;
            $interet = $montantTotal * $this->roi / 100;
            $creditTotal = $montantTotal + $interet;

            // Vérifier l'éligibilité du crédit avant de poursuivre
            if (!$this->verifierEligibiliteCredit($creditTotal)) {
                // Si l'éligibilité échoue, on sort de la fonction
                return;
            }

            // Préparation des données communes
            $commonData = [
                'demande_id' => $this->referenceCode,
                'objet_financement' => 'Achat du produit ' . $this->nameProd,
                'montant' => $montantTotal,
                'duree' => $this->duration,
                'type_financement' => $this->financementType,
                'bailleur' => $this->bailleur,
                'id_user' => auth()->id(),
                'date_debut' => now()->format('Y-m-d H:i:s'),
                'date_fin' => $this->endDate,
                'taux' => $this->roi,
                'status' => 'en cours',
            ];

            // Traitement si un investisseur spécifique est sélectionné
            if ($this->user_id) {
                // Recherche de l'investisseur par user_id
                $investor = Investisseur::where('user_id', $this->user_id)->first();

                if (!$investor) {
                    session()->flash('error', "L'investisseur sélectionné n'existe pas ou n'est pas un investisseur.");
                    return;
                }

                $investorId = $investor->id;
                Log::info('Investor found.', ['investor_id' => $investorId]);

                // Compléter les données spécifiques
                $commonData['id_investisseurs'] = json_encode($investorId);

                // Créer la demande
                $demande = DemandeCredi::create($commonData);

                // Préparer les données pour la notification
                $notificationData = [
                    'demande_id' => $this->referenceCode,
                    'id_projet' => null,
                    'montant' => $creditTotal,
                    'duree' => $this->duration,
                    'type_financement' => $this->financementType,
                    'bailleur' => $this->bailleur,
                    'user_id' => Auth::id(),
                    'id_investisseur' => $investorId,
                ];

                $owner = User::find($this->user_id);

                // Envoyer la notification à l'investisseur
                Notification::send($owner, new DemandeCreditNotification($notificationData));

                $this->dispatch('formSubmitted', 'Demande de Financement envoyée avec succès');
            }
            // Traitement si un type de bailleur est sélectionné
            else if ($this->bailleur) {
                // Récupérer l'ID de l'utilisateur actuel
                $currentUserId = Auth::id();

                // Récupérer les investisseurs correspondant au type de bailleur sauf l'utilisateur actuel
                $investisseurs = Investisseur::where('invest_type', $this->bailleur)
                    ->with('user')
                    ->whereHas('user', function($query) use ($currentUserId) {
                        $query->where('id', '!=', $currentUserId);
                    })
                    ->get();

                // Vérifier si des investisseurs ont été trouvés
                if ($investisseurs->isEmpty()) {
                    $this->dispatch('formSubmitted', 'Aucun investisseur avec ce type trouvé');
                    Log::warning('Investors not found for bailleur type.', [
                        'bailleur' => $this->bailleur,
                        'user_id' => $currentUserId,
                    ]);
                    return;
                }

                // Récupérer les IDs des utilisateurs associés
                $userIds = $investisseurs->pluck('user.id')->toArray();

                // Compléter les données spécifiques
                $commonData['id_investisseurs'] = json_encode($userIds);

                // Créer la demande
                $demande = DemandeCredi::create($commonData);

                // Traitements des notifications aux investisseurs éligibles
                foreach ($investisseurs as $investisseur) {
                    $userId = $investisseur->user_id;

                    if ($investisseur->tranche) {
                        // Nettoyer et diviser la tranche
                        $trancheCleaned = str_replace('.', '', $investisseur->tranche);
                        $parts = explode('-', $trancheCleaned);

                        // Déterminer les bornes
                        $borneInferieure = isset($parts[0]) ? (int) $parts[0] : null;
                        $borneSuperieure = isset($parts[1]) ? (int) $parts[1] : null;

                        Log::info("Analyse de tranche: {$borneInferieure}-{$borneSuperieure} pour creditTotal: {$creditTotal}");

                        // Vérifier si le montant total se trouve dans la tranche
                        if (
                            $borneInferieure !== null &&
                            $borneSuperieure !== null &&
                            $creditTotal >= $borneInferieure &&
                            $creditTotal <= $borneSuperieure
                        ) {
                            $investisseurUser = User::find($userId);

                            if ($investisseurUser) {
                                Notification::send($investisseurUser, new DemandeCreditNotification($demande));
                                Log::info("Notification envoyée à l'utilisateur ID: {$investisseurUser->id}");
                            } else {
                                Log::warning("Utilisateur non trouvé pour l'ID: {$userId}");
                            }
                        } else {
                            Log::info("Montant {$creditTotal} hors tranche {$borneInferieure}-{$borneSuperieure}");
                        }
                    } else {
                        Log::warning("Tranche non définie pour l'investisseur avec user_id: {$userId}");
                    }
                }

                $this->dispatch('formSubmitted', 'Demandes de crédit envoyées avec succès');
            } else {
                // Aucun investisseur ou bailleur sélectionné
                $this->dispatch('formSubmitted', 'Veuillez sélectionner un investisseur ou un type de bailleur');
                return;
            }

            // Réinitialiser les champs après soumission réussie
            $this->reset(['roi', 'quantite', 'endDate', 'duration', 'financementType', 'user_id', 'bailleur', 'search']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la soumission de la demande: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $this->dispatch('formSubmitted', 'Une erreur est survenue lors de la soumission: ' . $e->getMessage());
        }
    }

    public function verifierEligibiliteCredit($creditTotal)
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            $this->addError('eligibilite', "Votre portefeuille n'a pas été trouvé.");
            return false;
        }

        $cedd = $wallet->cedd->Solde ?? 0;
        $cefp = $wallet->cefp->Solde ?? 0;

        $garantie = $cedd + $cefp;
        $minimumRequis = $creditTotal * 0.25;

        if ($garantie < $minimumRequis) {
            $manque = $minimumRequis - $garantie;
            $this->addError('eligibilite', "Vous devez avoir au moins 25% du montant demandé en CEDD + CEFP. Il vous manque : " . number_format($manque, 0, ',', ' ') . " FCFA.");
            return false;
        }

        // Déterminer comment répartir le gel
        $montantCoupéCEDD = 0;
        $montantCoupéCEFP = 0;

        if ($cedd >= $minimumRequis) {
            // CEDD seul peut couvrir
            $montantCoupéCEDD = $minimumRequis;
        } elseif ($cefp >= $minimumRequis) {
            // CEFP seul peut couvrir
            $montantCoupéCEFP = $minimumRequis;
        } else {
            // Sinon, on répartit en deux parts égales
            $moitie = $minimumRequis / 2;
            $montantCoupéCEDD = min($moitie, $cedd);
            $montantCoupéCEFP = $minimumRequis - $montantCoupéCEDD; // le reste vient du CEFP
        }

        try {
            // Générer le code unique pour l'achat
            $referenceService = new generateUniqueReference();
            $codeUnique = $referenceService->generate();

            // Enregistrer dans les transactions gelées
            Gelement::create([
                'reference_id' => $codeUnique,
                'id_wallet' => $wallet->id,
                'amount' => $minimumRequis,
            ]);

            // Décrémenter les soldes du wallet
            if ($montantCoupéCEDD > 0) {
                $wallet->cedd->decrement('Solde', $montantCoupéCEDD);
            }

            if ($montantCoupéCEFP > 0) {
                $wallet->cefp->decrement('Solde', $montantCoupéCEFP);
            }

            // Créer la transaction
            $reference_service = new generateIntegerReference();
            $reference_id = $reference_service->generate();

            $TransactionService = new TransactionService();
            $TransactionService->createTransaction(
                $user->id,
                $user->id,
                'Gele',
                $minimumRequis,
                $reference_id,
                'Gele pour Demande de crédit (CEDD: ' . number_format($montantCoupéCEDD, 0, ',', ' ') . ' / CEFP: ' . number_format($montantCoupéCEFP, 0, ',', ' ') . ')',
                'COC'
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors du gel des fonds: ' . $e->getMessage());
            $this->addError('eligibilite', "Une erreur est survenue lors du gel des fonds: " . $e->getMessage());
            return false;
        }
    }

    // Fonction pour générer un code de référence de 5 chiffres
    private function generateReferenceCode()
    {
        return rand(10000, 99999); // Générer un nombre aléatoire de 5 chiffres
    }

    public function render()
    {
        return view('livewire.demandecredit');
    }
}
