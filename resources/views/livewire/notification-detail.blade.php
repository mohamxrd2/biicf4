<div>
    @if ($notification)
        {{-- Achat Direct --}}
        @if ($notification->type === 'App\Notifications\AchatBiicf')
            @livewire('Achatdirect', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\livraisonAchatdirect')
            @livewire('livraisonAchatdirect', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\CountdownNotificationAd')
            @livewire('CountdownNotificationAd', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\commandVerifAd')
            @livewire('command-verif-ad', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\mainleveAd')
            @livewire('mainleve-ad', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\Confirmation')
            @livewire('confirmation-notif', ['id' => $notificationId])


            {{-- Appel Offre Direct --}}
        @elseif ($notification->type === 'App\Notifications\AppelOffre')
            @livewire('appeloffre', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\AppelOffreTerminer')
            @livewire('appeloffreterminer', ['id' => $notificationId])

            {{-- Appel offre grouper --}}
        @elseif ($notification->type === 'App\Notifications\AOGrouper')
            @livewire('appeloffregrouper', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\AppelOffreGrouperNotification')
            @livewire('appeloffregroupernegociation', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\AppelOffreTerminerGrouper')
            @livewire('appeloffreterminergrouper', ['id' => $notificationId])


            {{-- fournisseur offre negocier --}}
        @elseif ($notification->type === 'App\Notifications\OffreNotifGroup')
            @livewire('enchere', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\NegosTerminer')
            @livewire('offrenegosterminer', ['id' => $notificationId])


            {{-- fournisseur offre grouper --}}
        @elseif ($notification->type === 'App\Notifications\OffreNegosNotif')
            @livewire('offre-groupe-quantite', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\OffreNegosDone')
            @livewire('Offre-negos-done', ['id' => $notificationId])


            {{--  retrait  --}}
        @elseif ($notification->type === 'App\Notifications\Retrait')
            @livewire('retrait', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\DepositSos')
            @livewire('deposit-sos', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\DepositRecu')
            @livewire('deposit-recu', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\DepositSend')
            @livewire('deposit-send', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\RetraitCode')
            @livewire('retrait-code', ['id' => $notificationId])


            {{-- general --}}
        @elseif ($notification->type === 'App\Notifications\VerifUser')
            @livewire('verif-user', ['id' => $notificationId])
        @elseif ($notification->type === 'App\Notifications\mainleveclient')
            @livewire('mainleveclient', ['id' => $notificationId])
        @endif
    @endif
</div>
