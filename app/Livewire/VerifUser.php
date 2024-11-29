<?php

namespace App\Livewire;

use App\Models\AchatDirect;
use App\Models\AppelOffreUser;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class VerifUser extends Component
{
    public $code_verif;
    public $notification;
    public $id;
    public $achatdirect;
    public $appeloffre;

    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        // Détermine si l'achat direct ou l'appel d'offre est présent
        if (isset($this->notification->data['achat_id'])) {
            $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);
        }

        if (isset($this->notification->data['id_appeloffre'])) {
            $this->appeloffre = AppelOffreUser::find($this->notification->data['id_appeloffre']);
        }

        // Assurez-vous que l'un des deux objets est disponible
        if (!$this->achatdirect && !$this->appeloffre) {
            abort(404, 'Aucune donnée correspondante trouvée dans la notification.');
        }
    }

    public function getCodeVerifProperty()
    {
        // Nettoie le code en enlevant les espaces blancs
        return trim($this->code_verif);
    }

    public function verifyCode()
    {
        // Validation du code de vérification
        $this->validate([
            'code_verif' => 'required|string|size:4', // Taille de 4 caractères
        ], [
            'code_verif.required' => 'Le code de vérification est requis.',
            'code_verif.string' => 'Le code de vérification doit être une chaîne.',
            'code_verif.size' => 'Le code de vérification doit être exactement de 4 caractères.',
        ]);

        if (trim($this->code_verif) === trim($this->notification->data['CodeVerification'])) {
            session()->flash('succes', 'Code valide.');
        } else {
            session()->flash('error', 'Code invalide.');
        }
    }
    public function render()
    {
        return view('livewire.verif-user');
    }
}
