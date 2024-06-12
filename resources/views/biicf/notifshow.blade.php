@extends('biicf.layout.navside')

@section('title', 'Details notification')

@section('content')


    <div class="max-w-3xl mx-auto">

        @if (session('success'))
            <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Afficher les messages d'erreur -->
        @if (session('error'))
            <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
                {{ session('error') }}
            </div>
        @endif


        @if ($notification->type === 'App\Notifications\AchatGroupBiicf')

            <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">



                <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                    </span>{{ $notification->data['nameProd'] }}</h2>
                <p class="mb-3"><strong>Quantité:</strong> {{ $notification->data['quantité'] }}</p>
                <p class="mb-3"><strong>Prix totale:</strong> {{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa</p>
                @php
                    $prixArtiche = $notification->data['montantTotal'] ?? 0;
                    $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                @endphp

                <p class="mb-3"><strong>Somme reçu :</strong> {{ number_format($sommeRecu, 2) }} Fcfa</p>

                <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                    class="mb-3 text-blue-700 hover:underline flex">
                    Voir le produit
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
                <p class="my-3 text-sm text-gray-500">Vous aurez debité 10% sur le prix de la marchandise</p>


                <div class="flex gap-2">


                    @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                        <div class="w-full bg-gray-300 border p-2 rounded-md">
                            <p class="text-md font-medium text-center">Réponse envoyée</p>
                        </div>
                    @else
                        <form id="form-accepter" action="{{ route('achatG.accepter') }}" method="POST">
                            @csrf
                            @foreach ($notification->data['userSender'] as $userId)
                                <input type="hidden" name="userSender[]" value="{{ $userId }}">
                            @endforeach

                            <input type="hidden" name="montantTotal" value="{{ $notification->data['montantTotal'] }}">
                            <input type="hidden" name="idProd" value="{{ $notification->data['idProd'] }}">
                            <input type="hidden" name="message"
                                value="commande de produit en cours /Préparation à la livraison">
                            <input type="hidden" name="notifId" value="{{ $notification->id }}">

                            <!-- Bouton accepter -->
                            <button id="btn-accepter" type="submit"
                                class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">
                                Accepter
                            </button>
                        </form>

                        <form id="form-refuser" action="{{ route('achatG.refuser') }}" method="POST">
                            @csrf
                            <input type="hidden" name="montantTotal" value="{{ $notification->data['montantTotal'] }}">
                            @foreach ($notification->data['userSender'] as $userId)
                                <input type="hidden" name="userSender[]" value="{{ $userId }}">
                            @endforeach
                            <input type="hidden" name="message" value="refus de produit">
                            <input type="hidden" name="idProd" value="{{ $notification->data['idProd'] }}">
                            <input type="hidden" name="notifId" value="{{ $notification->id }}">

                            <button id="btn-refuser" type="submit"
                                    class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                        </form>

                    @endif

                </div>



            </div>
        @elseif ($notification->type === 'App\Notifications\AchatBiicf')
            <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">
                <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                    </span>{{ $notification->data['nameProd'] }}</h2>
                <p class="mb-3"><strong>Quantité:</strong> {{ $notification->data['quantité'] }}</p>
                <p class="mb-3"><strong>Localité:</strong> {{ $notification->data['localite'] }}</p>
                <p class="mb-3"><strong>Spécificité:</strong> {{ $notification->data['specificite'] }}</p>
                <p class="mb-3"><strong>Prix d'artiche:</strong> {{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa
                </p>

                @php
                    $prixArtiche = $notification->data['montantTotal'] ?? 0;
                    $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                @endphp

                <p class="mb-3"><strong>Somme reçu :</strong> {{ number_format($sommeRecu, 2) }} Fcfa</p>
                <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                    class="mb-3 text-blue-700 hover:underline flex">
                    Voir le produit
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>

                <p class="my-3 text-sm text-gray-500">Vous aurez debité 10% sur le prix de la marchandise</p>
                <div class="flex gap-2">
                    @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                        <div class="w-full bg-gray-300 border p-2 rounded-md">
                            <p class="text-md font-medium text-center">Reponse envoyé</p>

                        </div>
                    @else
                        <form id="form-accepter" action="{{ route('achatD.accepter') }}" method="POST">
                            @csrf
                            <input type="hidden" name="userSender" value="{{ $notification->data['userSender'] }}">
                            <input type="hidden" name="montantTotal" value="{{ $notification->data['montantTotal'] }}">
                            <input type="hidden" name="message"
                                value="commande de produit en cours /Préparation a la livraison">

                            <input type="hidden" name="notifId" value="{{ $notification->id }}">



                            <!-- Bouton accepter -->

                            <button id="btn-accepter" type="submit"
                                class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>

                        </form>

                        <form id="form-refuser" action="{{ route('achatD.refuser') }}" method="POST">
                            @csrf
                            <input type="hidden" name="montantTotal" value="{{ $notification->data['montantTotal'] }}">
                            <input type="hidden" name="userSender" value="{{ $notification->data['userSender'] }}">
                            <input type="hidden" name="message" value="refus de produit">

                            <input type="hidden" name="notifId" value="{{ $notification->id }}">

                            <button id="btn-refuser" type="submit"
                                class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                        </form>
                    @endif

                </div>
            </div>
        @elseif ($notification->type === 'App\Notifications\OffreNotif')


        notif offre groupee

        
        @endif




    </div>




@endsection
