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
            <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">
                <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                    </span>{{ $produtOffre->name }}</h2>

                <p class="mb-3"><strong>Quantité traité:</strong> [{{ $produtOffre->qteProd_min }} -
                    {{ $produtOffre->qteProd_max }}]</p>

                <p class="mb-3"><strong>Prix d'artiche:</strong> {{ $produtOffre->prix }} Fcfa
                </p>

                <a href="{{ route('biicf.postdet', $notification->data['produit_id']) }}"
                    class="mb-3 text-white bg-purple-600 hover:bg-purple-800 text-center py-2 rounded-xl flex justify-center">
                    Voir le produit
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>



            </div>
        @elseif ($notification->type === 'App\Notifications\AppelOffre')
            <div class="grid grid-cols-2 gap-4 p-4">
                <div class="lg:col-span-1 col-span-2">

                    <h2 class="text-3xl font-semibold mb-2">{{ $notification->data['productName'] }}</h2>

                    <div class="w-full gap-y-2  mt-4">

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Prix unitaire maximal</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['lowestPricedProduct'] }}
                            </p>
                        </div>

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Quantité</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['quantity'] }}</p>
                        </div>

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Payement</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['payment'] }}</p>
                        </div>
                        @if ($notification->data['Livraison'])
                            <div class="w-full flex justify-between items-center py-4  border-b-2">
                                <p class="text-md font-semibold">Livraison</p>
                                <p class="text-md font-medium text-gray-600">{{ $notification->data['Livraison'] }}</p>
                            </div>
                        @endif

                        @if ($notification->data['specificity'])
                            <div class="w-full flex justify-between items-center py-4  border-b-2">
                                <p class="text-md font-semibold">Specificité</p>
                                <p class="text-md font-medium text-gray-600">{{ $notification->data['specificity'] }}</p>
                            </div>
                        @endif


                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Date au plus tôt</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['dateTot'] }}</p>
                        </div>

                        <div class="w-full flex justify-between items-center py-4  border-b-2">
                            <p class="text-md font-semibold">Date au plus tard</p>
                            <p class="text-md font-medium text-gray-600">{{ $notification->data['dateTard'] }}</p>
                        </div>


                    </div>



                </div>
                <div class="lg:col-span-1 col-span-2">

                    <div class="p-4">

                        <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2" uk-sticky="media: 1024; end: #js-oversized; offset: 80">



                            <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">
    
    
    
                                <!-- comments -->
                                <div class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">
    
    
                                        <div class="w-full h-full flex items-center justify-center">
                                            <p class="text-gray-800"> Aucune offre n'a été soumise</p>
                                        </div>
                                  
                                </div>
                                
    
                                <!-- add comment -->
                                <form>
                                    <label for="chat" class="sr-only">Your message</label>
                                    <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                                        <button type="button"
                                            class="inline-flex justify-center p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 20 18">
                                                <path fill="currentColor"
                                                    d="M13 5.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM7.565 7.423 4.5 14h11.518l-2.516-3.71L11 13 7.565 7.423Z" />
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M18 1H2a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1Z" />
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 5.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0ZM7.565 7.423 4.5 14h11.518l-2.516-3.71L11 13 7.565 7.423Z" />
                                            </svg>
                                            <span class="sr-only">Upload image</span>
                                        </button>
                                        <button type="button"
                                            class="p-2 text-gray-500 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13.408 7.5h.01m-6.876 0h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM4.6 11a5.5 5.5 0 0 0 10.81 0H4.6Z" />
                                            </svg>
                                            <span class="sr-only">Add emoji</span>
                                        </button>
                                        <textarea id="chat" rows="1"
                                            class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Your message..."></textarea>
                                        <button type="submit"
                                            class="inline-flex justify-center p-2 text-blue-600 rounded-full cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600">
                                            <svg class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                                <path
                                                    d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                            </svg>
                                            <span class="sr-only">Send message</span>
                                        </button>
                                    </div>
                                </form>
    
    
    
    
                            </div>
    
                        </div>

                        

                    </div>

                </div>

            </div>


        @endif




    </div>




@endsection
