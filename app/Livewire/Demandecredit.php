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
        $this->validate([
            'roi' => 'required|numeric',
            'quantite' => 'required|numeric',
            'sommedemnd' => 'required|numeric',
            'financementType' => 'required|string',
            'user_id' => 'nullable|exists:investisseurs,user_id', // Assurez-vous que l'utilisateur sélectionné existe
            'bailleur' => 'nullable|string',
            'endDate' => 'required|date',
            'duration' => 'required|date',
        ]);

        try {
            if ($this->roi < 5) {
                session()->flash('messages', ['Le retour sur investissement doit être supérieur à 5.']);
                return; // Stoppe l'exécution de la fonction
            }

            // Calculs
            $montantMax = $this->sommedemnd * $this->quantite;
            $interet = $montantMax *  $this->roi / 100;
            $creditTotal = $montantMax + $interet;

            // Vérifie si l'investisseur existe
            if ($this->user_id) {
                // Recherche de l'investisseur par user_id
                $investor = Investisseur::where('user_id', $this->user_id)->first();
                // Récupère l'id de l'investisseur
                $investorId = $investor->id ?? null; // ou $investor->id_investisseur selon ton schéma
                Log::info('Investor found.', ['investor_id' => $investorId]);

                // Insérer les données dans la table demande_credi
                $demande = DemandeCredi::create([
                    'demande_id' => $this->referenceCode, // Remplacer par la logique appropriée si nécessaire
                    'objet_financement' => 'Achat du produit ' . $this->nameProd,
                    'montant' => $montantMax,
                    'duree' => $this->duration,
                    'type_financement' => $this->financementType,
                    'bailleur' => $this->bailleur,
                    'id_user' => auth()->id(), // Utilisateur connecté
                    'id_investisseurs' => json_encode($investorId), // Utiliser l'id récupéré
                    'date_debut' => now()->format('Y-m-d H:i:s'),
                    'date_fin' => $this->endDate,
                    'taux' => $this->roi, // Le taux de retour sur investissement
                ]);

                $data = [
                    'demande_id' => $this->referenceCode, // Accéder aux clés du tableau
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
                Notification::send($owner, new DemandeCreditNotification($data));

                // Reset des champs après soumission
                $this->reset();
            } else if ($this->bailleur) {
                // Récupérer l'ID de l'investisseur qui soumet
                $submitterId = Auth::id(); // ou $this->investisseur_id selon ton contexte


                // Récupérer les investisseurs en excluant celui qui soumet
                $investisseurs = Investisseur::where('invest_type', $this->bailleur)
                    ->with('user') // Assure-toi que la relation "user" est définie dans le modèle Investisseur
                    ->where('user_id', '!=', $submitterId) // Exclure l'investisseur qui soumet
                    ->get();

                // Vérifie s'il y a des investisseurs trouvés
                if ($investisseurs->isEmpty()) {
                    // Gérer le cas où aucun investisseur n'est trouvé
                    $this->dispatch('formSubmitted', 'Aucun investeur avec ce type trouver');

                    Log::warning('Investors not found for bailleur type.', [
                        'bailleur' => $this->bailleur,
                        'user_id' => auth()->id(),
                    ]);
                }
                // Récupérer les IDs des utilisateurs associés
                $userIds = $investisseurs->pluck('user.id')->toArray();

                $demande = DemandeCredi::create([
                    'demande_id' => $this->referenceCode, // Remplacer par la logique appropriée si nécessaire
                    'objet_financement' => 'Achat du produit ' . $this->nameProd,
                    'montant' => $creditTotal,
                    'duree' => $this->duration,
                    'type_financement' => $this->financementType,
                    'bailleur' => $this->bailleur,
                    'id_user' => auth()->id(), // Utilisateur connecté
                    'id_investisseurs' =>  json_encode($userIds), // Utilise l'id de l'investisseur actuel
                    'date_debut' => now()->format('Y-m-d H:i:s'),
                    'date_fin' => $this->endDate,
                    'taux' => $this->roi, // Le taux de retour sur investissement
                    'status' => 'en cours', // Le taux de retour sur investissement
                ]);


                // Envoi de la notification aux investisseurs concernés
                foreach ($investisseurs as $investisseur) {
                    // Récupérer 'user_id' de chaque investisseur
                    $userId = $investisseur->user_id;

                    // Rechercher l'investisseur en fonction de 'user_id'
                    $investissement = Investisseur::where('user_id', $userId)->first();

                    if ($investissement) {
                        Log::info("Traitement de l'investisseur avec user_id : {$userId}");

                        if ($investissement->tranche) {
                            // Initialisation des bornes
                            $borneInferieure = null;
                            $borneSuperieure = null;

                            // Nettoyer et diviser la tranche
                            $trancheCleaned = str_replace('.', '', $investissement->tranche); // Supprimer les points
                            $parts = explode('-', $trancheCleaned);

                            // Déterminer les bornes
                            $borneInferieure = isset($parts[0]) ? (int) $parts[0] : null; // Borne inférieure
                            $borneSuperieure = isset($parts[1]) ? (int) $parts[1] : null; // Borne supérieure

                            Log::info("Tranche pour l'investisseur avec user_id {$userId} : Borne inférieure = {$borneInferieure},creditTotal = {$creditTotal}, Borne supérieure = {$borneSuperieure}");

                            // Vérifier si le crédit total se trouve dans la tranche
                            if (
                                $borneInferieure !== null && $borneSuperieure !== null &&
                                $creditTotal >= $borneInferieure && $creditTotal <= $borneSuperieure
                            ) {
                                // Récupérer l'utilisateur associé à l'investisseur
                                $investisseurUser = $investissement->user;

                                if ($investisseurUser) {
                                    // Envoyer la notification
                                    Notification::send($investisseurUser, new DemandeCreditNotification($demande));
                                    Log::info("Notification envoyée à l'utilisateur ID : {$investisseurUser->id} pour l'investisseur avec user_id : {$userId}");
                                } else {
                                    // Log si aucun utilisateur n'est associé
                                    Log::warning("Aucun utilisateur trouvé pour l'investisseur avec user_id : {$userId}");
                                }
                            } else {
                                Log::info("Le crédit total ({$creditTotal}) ne correspond pas à la tranche de l'investisseur avec user_id : {$userId}");
                            }
                        } else {
                            Log::warning("Tranche non valide ou absente pour l'investisseur avec user_id : {$userId}");
                        }
                    } else {
                        Log::error("Investisseur non trouvé avec user_id : {$userId}");
                    }
                }



                // Reset des champs après soumission
                $this->reset();
            }

            if ($this->verifierEligibiliteCredit($creditTotal, $this->referenceCode)) {
                // Si l'utilisateur est éligible, vous pouvez continuer avec la logique de soumission
                $this->dispatch('formSubmitted', 'Demande de crédit soumise avec succès.');
            } else {
                // Si l'utilisateur n'est pas éligible, vous pouvez gérer cela ici
                $this->dispatch('formSubmitted', 'Vous n\'êtes pas éligible pour ce crédit.');
            };
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Assurez-vous que $messages est un tableau et ajoutez les nouvelles erreurs
            $this->messages = array_merge($this->messages, $e->validator->errors()->all());
        }
    }

    public function verifierEligibiliteCredit($creditTotal, $reference)
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
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

        // Geler les montants (tu peux remplacer ça par l'appel aux bons modèles ou services de mise à jour)
        if ($wallet->cedd) {
            $wallet->cedd->decrement('Solde', $montantCoupéCEDD);
        }

        if ($wallet->cefp) {
            $wallet->cefp->decrement('Solde', $montantCoupéCEFP);
        }

        // Enregistrer dans les transactions gelées
        Gelement::create([
            'reference_id' => $reference,
            'id_wallet' => $wallet->id,
            'amount' => $minimumRequis,
        ]);

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
