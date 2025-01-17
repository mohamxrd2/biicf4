<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Models\User;
use App\Notifications\CountdownNotificationAd;
use App\Notifications\VerifUser;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TakeawayService
{
    public function process($notification, $achatdirect, $prixFin)
    {
        DB::beginTransaction();

        try {
            // Vérification des données requises
            if (!$notification || !$achatdirect) {
                Log::error('Notification ou achatdirect non défini.', [
                    'notification' => $notification,
                    'achatdirect' => $achatdirect,
                ]);
                throw new Exception('Données manquantes pour traiter la demande.');
            }

            // Génération et mise à jour du code de vérification
            $codeVerification = $this->generateVerificationCode();
            $achatdirect->update(['code_verification' => $codeVerification]);

            // Envoi des notifications
            $this->sendNotifications($achatdirect, $prixFin, $codeVerification);
            $notification->update(['reponse' => 'accepte']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur takeaway:', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    protected function generateVerificationCode(): int
    {
        return random_int(1000, 9999);
    }

    protected function sendNotifications($achatdirect, $prixFin, $codeVerification): void
    {
        // Notification au client
        $details = [
            'prixFin' => $prixFin,
            'code_unique' => $achatdirect->code_unique,
            'type_achat' => 'Take Away',
            'id' => $achatdirect->id,
        ];

        $userSender = User::findOrFail($achatdirect->userSender);
        Notification::send($userSender, new CountdownNotificationAd($details));
        event(new NotificationSent($userSender));

        // Notification au fournisseur
        $dataFournisseur = [
            'code_unique' => $achatdirect->code_unique,
            'CodeVerification' => $codeVerification,
            'client' => $achatdirect->userSender,
            'id_achat' => $achatdirect->id,
        ];

        $userTrader = User::findOrFail($achatdirect->userTrader);

        Notification::send($userTrader, new VerifUser($dataFournisseur));
        event(new NotificationSent($userTrader));
    }
}
