<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cotisation;
use App\Models\Tontines;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessPayment;
use Illuminate\Support\Facades\Log;

class DetailTontine extends Component
{
    public $id;
    public $tontine;
    public $pourcentage;
    public $cts_reussi;
    public $transCotisation;
    public $cts_sum;
    public $hasMoreTransactions = false;
    public $transactionsLimit = 5;
    public $transactionsOffset = 0;
    public $selectedTransactions = [];

    public function mount($id)
    {
        $this->id = $id;

        // Récupération de la tontine
        $this->tontine = Tontines::findOrFail($this->id);

        // Récupération des cotisations réussies
        $cotisationsReussies = Cotisation::where('tontine_id', $this->id)
            ->where('statut', 'payé')
            ->get();

        // Comptage et somme des montants des cotisations réussies
        $this->cts_reussi = $cotisationsReussies->count();
        $this->cts_sum = $cotisationsReussies->sum('montant');

        // Gestion du risque de division par zéro
        $nombreCotisations = $this->tontine->nombre_cotisations ?: 1;
        $this->pourcentage = ($this->cts_reussi / $nombreCotisations) * 100;

        $this->loadTransactions();
    }

    public function toggleTransactionSelection($transactionId)
    {
        if (in_array($transactionId, $this->selectedTransactions)) {
            $this->selectedTransactions = array_diff($this->selectedTransactions, [$transactionId]);
        } else {
            $this->selectedTransactions[] = $transactionId;
        }
    }

    public function retrySelectedPayments()
    {

        try {
            DB::beginTransaction();

            $successCount = 0;
            $failureCount = 0;

            foreach ($this->selectedTransactions as $transactionId) {
                $cotisation = Cotisation::where('id', $transactionId)
                    ->where('statut', '!=', 'payé')
                    ->first();

                if (!$cotisation) {
                    $failureCount++;
                    continue;
                }

                $wallet = Wallet::where('user_id', $cotisation->user->id)->first();

                // Vérification du solde
                if ($wallet->solde >= $cotisation->montant) {
                    // Débit du wallet
                    $wallet->solde -= $cotisation->montant;
                    $wallet->save();

                    // Mise à jour du statut de la cotisation
                    $cotisation->update([
                        'statut' => 'payé',
                        'date_paiement' => now()
                    ]);

                    // Créer une transaction
                    $cotisation->transactions()->create([
                        'user_id' => $cotisation->user_id,
                        'montant' => $cotisation->montant,
                        'type' => 'debit',
                        'motif' => 'Paiement cotisation tontine',
                        'statut' => 'success'
                    ]);

                    $successCount++;
                } else {
                    $failureCount++;
                    // Mettre à jour le statut pour indiquer un échec dû au solde insuffisant
                    $cotisation->update([
                        'statut' => 'échec',
                        'message_erreur' => 'Solde insuffisant'
                    ]);
                }
            }

            DB::commit();

            // Réinitialiser la sélection
            $this->selectedTransactions = [];

            // Rafraîchir la liste des transactions
            $this->loadTransactions();

            // Message de notification
            if ($successCount > 0 && $failureCount > 0) {
                session()->flash('success', "$successCount paiement(s) réussi(s) et $failureCount échec(s) dû à un solde insuffisant.");
            } elseif ($successCount > 0) {
                session()->flash('success', "Les $successCount paiements ont été traités avec succès.");
            } else {
                session()->flash('error', "Aucun paiement n'a pu être traité. Vérifiez les soldes des comptes.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors du traitement des paiements.');
            Log::error('Erreur lors du retraitement des paiements: ' . $e->getMessage());
        }
    }

    public function loadTransactions()
    {
        $this->transCotisation = Cotisation::where('tontine_id', $this->id)
            ->latest('created_at')
            ->offset($this->transactionsOffset)
            ->limit($this->transactionsLimit)
            ->with('user')
            ->get();

        // Mise à jour du compteur pour la pagination
        $totalTransactions = Cotisation::where('tontine_id', $this->id)->count();
        $this->hasMoreTransactions = ($this->transactionsOffset + $this->transactionsLimit) < $totalTransactions;
    }

    public function loadMoreTransactions()
    {
        $this->transactionsOffset += $this->transactionsLimit;

        $newTransactions = Cotisation::where('tontine_id', $this->id)
            ->latest('created_at')
            ->offset($this->transactionsOffset)
            ->limit($this->transactionsLimit)
            ->with(['user', 'user.wallet'])
            ->get();

        // Append new transactions to existing ones
        $this->transCotisation = $this->transCotisation->concat($newTransactions);

        // Check if there are more transactions to load
        $totalTransactions = Cotisation::where('tontine_id', $this->id)->count();
        $this->hasMoreTransactions = ($this->transactionsOffset + $this->transactionsLimit) < $totalTransactions;
    }

    public function render()
    {
        return view('livewire.detail-tontine');
    }
}
