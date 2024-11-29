@extends('biicf.layout.navside')

@section('title', 'Details notification')

{{-- show cest ici  deja --}}

@section('content')

    @php
        use App\Models\ProduitService;
    @endphp

    <div class=" mx-auto">

        {{-- <livewire:notification-show :id="$id"/> --}}
        {{-- @livewire('notification-show', ['id' => $id]) --}}

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
        @elseif ($notification->type === 'App\Notifications\livraisonAppelOffre')
            @livewire('livraisonappeloffre', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\CountdownNotificationAp')
            @livewire('countdown-notification-ap', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\commandVerifAp')
            @livewire('command-verif-ap', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\mainleveAp')
            @livewire('mainleve-ap', ['id' => $id])



            {{-- general --}}
        @elseif ($notification->type === 'App\Notifications\VerifUser')
            @livewire('verif-user', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\mainleveclient')
            @livewire('mainleveclient', ['id' => $id])
        @endif
    </div>




@endsection
