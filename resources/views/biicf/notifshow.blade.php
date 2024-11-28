@extends('biicf.layout.navside')

@section('title', 'Details notification')

{{-- show cest ici  deja--}}

@section('content')


    <div class=" mx-auto">

        {{-- <livewire:notification-show :id="$id"/> --}}
        {{-- @livewire('notification-show', ['id' => $id]) --}}
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
        @elseif ($notification->type === 'App\Notifications\VerifUser')
            @livewire('verif-user', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\mainleveclient')
            @livewire('mainleveclient', ['id' => $id])
        @endif
    </div>




@endsection
