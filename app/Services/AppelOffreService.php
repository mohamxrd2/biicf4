<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\User;
use App\Notifications\CountdownNotificationAd;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AppelOffreService
{
    public function handleTakeaway($data)
    {
        DB::beginTransaction();
        try {
            // Validation des données
            $this->validateData($data);

            // Créer l'achat direct
            $achatdirect = $this->createAchatDirect($data);

            // Gérer les notifications
            $userSender = $this->handleNotifications($data, $achatdirect);

            // Mettre à jour le statut de la notification
            $data['notification']->update(['reponse' => 'accepte']);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Commande traitée avec succès',
                'achatdirect' => $achatdirect
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement de la commande', [
                'error' => $e->getMessage(),
                'code_unique' => $data['notification']->data['code_unique'] ?? null
            ]);
            throw $e;
        }
    }

    private function validateData($data)
    {
        if (!isset($data['notification']) || !isset($data['appeloffre']) || !isset($data['produit'])) {
            throw new Exception('Données manquantes pour traiter la demande');
        }

        if ($data['notification']->reponse !== null) {
            throw new Exception('Cette commande a déjà été traitée');
        }
    }

    private function createAchatDirect($data)
    {
        return AchatDirect::create([
            'nameProd' => $data['produit']->name,
            'quantité' => $data['appeloffre']->quantity,
            'montantTotal' => $data['prixTotal'],
            'type_achat' => 'achatDirect',
            'localite' => $data['appeloffre']->localite,
            'date_tot' => $data['appeloffre']->date_tot,
            'date_tard' => $data['appeloffre']->date_tard,
            'userTrader' => auth()->id(),
            'userSender' => $data['appeloffre']->id_sender,
            'idProd' => $data['produit']->id,
            'code_unique' => $data['appeloffre']->code_unique,
        ]);
    }

    private function handleNotifications($data, $achatdirect)
    {
        $userSender = User::find($data['appeloffre']->id_sender);
        if (!$userSender) {
            throw new Exception('Utilisateur expéditeur non trouvé');
        }

        $details = [
            'prixFin' => $data['prixFin'] ?? null,
            'code_unique' => $data['notification']->data['code_unique'] ?? null,
            'id' => $achatdirect->id ?? null,
            'type_achat' => 'Take Away'
        ];

        Notification::send($userSender, new CountdownNotificationAd($details));

        $notification = $userSender->notifications()
            ->where('type', CountdownNotificationAd::class)
            ->latest()
            ->first();

        if ($notification) {
            $notification->update(['type_achat' => 'Take Away']);
        }

        event(new NotificationSent($userSender));

        return $userSender;
    }
}
