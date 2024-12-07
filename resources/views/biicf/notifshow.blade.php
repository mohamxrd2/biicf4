@extends('biicf.layout.navside')

@section('title', 'Details notification')

{{-- show cest ici  deja --}}

@section('content')

    @php
        use App\Models\ProduitService;
    @endphp

    <div class=" mx-auto">


        {{-- Achat Direct --}}
        @if ($notification->type === 'App\Notifications\AchatBiicf')
            @livewire('Achatdirect', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\livraisonAchatdirect')
            @livewire('livraisonAchatdirect', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\CountdownNotificationAd')
            @livewire('CountdownNotificationAd', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\commandVerifAd')
            @livewire('command-verif-ad', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\mainleveAd')
            @livewire('mainleve-ad', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\Confirmation')
            @livewire('confirmation-notif', ['id' => $id])


            {{-- Appel Offre Direct --}}
        @elseif ($notification->type === 'App\Notifications\AppelOffre')
            @livewire('appeloffre', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\AppelOffreTerminer')
            @livewire('appeloffreterminer', ['id' => $id])

            {{-- Appel offre grouper --}}
        @elseif ($notification->type === 'App\Notifications\AOGrouper')
            @livewire('appeloffregrouper', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\AppelOffreGrouperNotification')
            @livewire('appeloffregroupernegociation', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\AppelOffreTerminerGrouper')
            @livewire('appeloffreterminergrouper', ['id' => $id])


            {{-- fournisseur offre negocier --}}
        @elseif ($notification->type === 'App\Notifications\OffreNotifGroup')
            @livewire('enchere', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\NegosTerminer')
            @livewire('offrenegosterminer', ['id' => $id])


            {{-- fournisseur offre grouper --}}
        @elseif ($notification->type === 'App\Notifications\OffreNegosNotif')
            @livewire('offre-groupe-quantite', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\OffreNegosDone')
            @livewire('Offre-negos-done', ['id' => $id])


            {{--  retrait  --}}
        @elseif ($notification->type === 'App\Notifications\Retrait')
            @livewire('retrait', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\DepositSos')
            @livewire('deposit-sos', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\DepositRecu')
            @livewire('deposit-recu', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\DepositSend')
            @livewire('deposit-send', ['id' => $id])


            {{-- general --}}
        @elseif ($notification->type === 'App\Notifications\VerifUser')
            @livewire('verif-user', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\mainleveclient')
            @livewire('mainleveclient', ['id' => $id])
        @endif
    </div>




@endsection
