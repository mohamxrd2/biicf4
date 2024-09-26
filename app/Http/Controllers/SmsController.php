<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    public function sendsms()
    {


        // Récupérer les informations Twilio depuis le fichier .env
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $verifyServiceId = env('TWILIO_VERIFY_SERVICE_ID');
        $twilio = new Client($sid, $token);


        // Récupérer le numéro de téléphone depuis la requête
        $phoneNumber = '+2250779568126';

        try {
            // Créer la vérification via le service Twilio Verify
            $verification = $twilio->verify->v2->services($verifyServiceId)
                ->verifications
                ->create($phoneNumber, "sms");

            // Retourner une réponse indiquant que le SMS a été envoyé
            return response()->json([
                'message' => 'SMS de vérification envoyé',
                'sid' => $verification->sid
            ], 200);
        } catch (\Exception $e) {
            // Gérer les erreurs
            return response()->json([
                'message' => 'Erreur lors de l\'envoi du SMS de vérification',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
