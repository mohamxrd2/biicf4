<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AddclientMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $userData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $userData)
    {
        $this->userData = $userData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Génération du lien de vérification
        $verificationUrl = route('verification.verify', [
            'id' => $this->userData['id'], // L'ID de l'utilisateur
            'token' => $this->userData['email_verification_token'], // Le token de vérification
        ]);

        return $this->subject("Création d'un client Mail")
                    ->to($this->userData['email'])
                    ->markdown('emails.admin.addclient', [
                        'userData' => $this->userData,
                        'verificationUrl' => $verificationUrl, // Passer le lien de vérification à la vue d'e-mail
                    ]);
    }
}
