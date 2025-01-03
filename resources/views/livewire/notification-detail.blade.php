<div>
    @if ($notification)
        @switch($notification->type)
            {{-- Achat Direct --}}
            @case ('App\Notifications\AchatBiicf')
                @livewire('Achatdirect', ['id' => $notificationId])
            @case ('App\Notifications\livraisonAchatdirect')
                @livewire('livraisonAchatdirect', ['id' => $notificationId])
            @case ('App\Notifications\CountdownNotificationAd')
                @livewire('CountdownNotificationAd', ['id' => $notificationId])
            @case ('App\Notifications\commandVerifAd')
                @livewire('command-verif-ad', ['id' => $notificationId])
            @case ('App\Notifications\mainleveAd')
                @livewire('mainleve-ad', ['id' => $notificationId])
            @case ('App\Notifications\Confirmation')
                @livewire('confirmation-notif', ['id' => $notificationId])
                {{-- Appel Offre Direct --}}
            @case ('App\Notifications\AppelOffre')
                @livewire('appeloffre', ['id' => $notificationId])
            @case ('App\Notifications\AppelOffreTerminer')
                @livewire('appeloffreterminer', ['id' => $notificationId])
                {{-- Appel offre grouper --}}
            @case ('App\Notifications\AOGrouper')
                @livewire('appeloffregrouper', ['id' => $notificationId])
            @case ('App\Notifications\AppelOffreGrouperNotification')
                @livewire('appeloffregroupernegociation', ['id' => $notificationId])
            @case ('App\Notifications\AppelOffreTerminerGrouper')
                @livewire('appeloffreterminergrouper', ['id' => $notificationId])
                {{-- fournisseur offre negocier --}}
            @case ('App\Notifications\OffreNotifGroup')
                @livewire('enchere', ['id' => $notificationId])
            @case ('App\Notifications\NegosTerminer')
                @livewire('offrenegosterminer', ['id' => $notificationId])
                {{-- fournisseur offre grouper --}}
            @case ('App\Notifications\OffreNegosNotif')
                @livewire('offre-groupe-quantite', ['id' => $notificationId])
            @case ('App\Notifications\OffreNegosDone')
                @livewire('Offre-negos-done', ['id' => $notificationId])
                {{--  retrait  --}}
            @case ('App\Notifications\Retrait')
                @livewire('retrait', ['id' => $notificationId])
            @case ('App\Notifications\DepositSos')
                @livewire('deposit-sos', ['id' => $notificationId])
            @case ('App\Notifications\DepositRecu')
                @livewire('deposit-recu', ['id' => $notificationId])
            @case ('App\Notifications\DepositSend')
                @livewire('deposit-send', ['id' => $notificationId])
            @case ('App\Notifications\RetraitCode')
                @livewire('retrait-code', ['id' => $notificationId])
                {{-- general --}}
            @case ('App\Notifications\VerifUser')
                @livewire('verif-user', ['id' => $notificationId])
            @case ('App\Notifications\mainleveclient')
                @livewire('mainleveclient', ['id' => $notificationId])
                {{-- Ajoutez les autres cas selon vos besoins --}}

                @default
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">{{ $notification->data['title'] ?? 'Notification' }}</h2>
                        <p class="text-gray-600">{{ $notification->data['description'] ?? 'Aucun détail disponible' }}</p>
                        <div class="mt-4 text-sm text-gray-500">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
            @endswitch
        @endif
    </div>
