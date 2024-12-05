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
            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">

                @if (session()->has('success'))
                    <div class="mt-4 text-green-500">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mt-4 text-red-500">
                        {{ session('error') }}
                    </div>
                @endif
                <h2 class="mb-4 text-xl font-medium">{{ $demandeur->name }}, vous a fait une demande de retrait</h2>

                <h1 class="text-4xl font-semibold">{{ $amount }} CFA</h1>

                <div class="flex w-full mt-5">
                    @if ($notification->reponse)
                        <div class="p-2 bg-gray-300 border rounded-md ">
                            <p class="font-medium text-center text-md">Réponse envoyée</p>
                        </div>
                    @else
                        <button wire:click='accepteRetrait'
                            class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md">


                            <span wire:loading.remove>
                                Accepter
                            </span>
                            <span wire:loading>
                                Chargement...
                                <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                </svg>
                            </span>
                        </button>

                        <button wire:click='refusRetrait' class="flex p-2 font-medium text-white bg-red-700 rounded-md"><svg
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="mr-2 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                            </svg>
                            <span wire:loading.remove>
                                Refuser
                            </span>
                            <span wire:loading>
                                Chargement...
                                <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                </svg>
                            </span>

                        </button>
                    @endif

                </div>


            </div>
        @elseif ($notification->type === 'App\Notifications\DepositSos')
            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
                <!-- Messages de notification -->
                @if (session()->has('success'))
                    <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Affichage des erreurs de validation -->
                @if ($errors->any())
                    <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Titre -->
                <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Détails de la demande</h2>

                <!-- Informations de l'utilisateur -->
                <div class="flex items-center mb-6 space-x-4">
                    <div class="flex-shrink-0">
                        <img src="{{ $userDeposit->photo ?? 'https://via.placeholder.com/100' }}"
                            alt="Photo de l'utilisateur" class="w-16 h-16 border border-gray-300 rounded-full">
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900">{{ $userDeposit->name ?? 'Utilisateur inconnu' }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $userDeposit->email ?? 'Email non disponible' }}</p>
                    </div>
                </div>

                <!-- Détails du dépôt -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 text-center border rounded-lg bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-700">Montant demandé</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($amountDeposit ?? 0, 0, ',', ' ') }}
                            CFA</p>
                    </div>
                    <div class="p-4 text-center border rounded-lg bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-700">R.O.I Attendu</h3>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($roiDeposit ?? 0, 0, ',', ' ') }}
                            CFA</p>
                    </div>
                </div>

                <!-- Sélection de l'opérateur -->
                <label for="operator" class="block mb-2 text-gray-700">Opérateur</label>
                <select id="operator" class="w-full p-2 mb-6 border border-gray-300 rounded-md" required
                    wire:model="operator">
                    <option value="" disabled selected>Choisir l'opérateur où vous voulez recevoir l'argent</option>
                    <option value="Wave">Wave</option>
                    <option value="Orange Money">Orange Money</option>
                    <option value="Moov Money">Moov Money</option>
                    <option value="MTN Money">MTN Money</option>
                    <option value="Tresor Pay">Tresor Pay</option>
                </select>
                @error('operator')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Numéro de réception -->
                <label for="phonenumber" class="block mb-2 text-gray-700">Le numéro de réception</label>
                <input type="text" id="phonenumber" wire:model="phonenumber"
                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Entrez le numéro de réception">
                @error('phonenumber')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Boutons d'action -->
                <div class="flex justify-around mt-4">
                    @if ($existingRequest)
                        <p class="font-bold text-red-600 ">Demande Expiré !</p>
                    @else
                        @if ($notification->reponse)
                            <div
                                class="px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-transparent rounded-md shadow-sm cursor-not-allowed">
                                Réponse envoyée
                            </div>
                        @else
                            <button wire:click="acceptDeposit"
                                class="px-6 py-2 font-semibold text-white bg-green-500 rounded-md shadow-md hover:bg-green-600">
                                Accepter
                            </button>
                            <button wire:click="rejectDeposit"
                                class="px-6 py-2 font-semibold text-white bg-red-500 rounded-md shadow-md hover:bg-red-600">
                                Refuser
                            </button>
                        @endif

                    @endif

                </div>
            </div>
        @elseif ($notification->type === 'App\Notifications\DepositRecu')
            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
                @if (session()->has('success'))
                    <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
                        {{ session('error') }}
                    </div>
                @endif
                <!-- Titre -->
                <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Détails de la demande</h2>

                <!-- Informations de l'utilisateur -->
                <div class="flex items-center mb-6 space-x-4">
                    <div class="flex-shrink-0">
                        <img src="{{ $userDeposit->photo ?? 'https://via.placeholder.com/100' }}"
                            alt="Photo de l'utilisateur" class="w-16 h-16 border border-gray-300 rounded-full">
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900">{{ $userDeposit->name ?? 'Utilisateur inconnu' }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $userDeposit->email ?? 'Email non disponible' }}</p>
                    </div>
                </div>

                <!-- Informations sur la demande -->
                <div class="p-4 mb-4 border rounded-lg bg-gray-50">
                    <h3 class="mb-2 font-medium text-gray-600 text-md">Numéro de réception de l'argent</h3>
                    <p class="text-xl font-semibold text-gray-700">{{ $phonenumber }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 text-center border rounded-lg bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-700">Opérateur</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $operatorRecu }}</p>
                    </div>
                    <div class="p-4 text-center border rounded-lg bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-700">Montant à envoyer</h3>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($roiDeposit ?? 0, 0, ',', ' ') }}
                            CFA</p>
                    </div>
                </div>

                <!-- Zone de téléchargement du reçu -->
                <div class="mt-4">
                    <div class="relative">
                        <label for="receipt" class="block text-sm font-medium text-gray-700">Télécharger le reçu</label>
                        @if (!$receipt)
                            <!-- Zone de téléchargement stylisée -->
                            <label for="receipt"
                                class="flex flex-col items-center justify-center w-full h-40 mt-1 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                    <path fill-rule="evenodd"
                                        d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="mt-2 text-sm text-gray-600">Cliquez ou déposez le reçu</span>
                            </label>
                        @else
                            <!-- Affichage de l'image téléchargée et bouton de suppression -->
                            <div class="relative">
                                <img src="{{ $receipt->temporaryUrl() }}" alt="Aperçu du reçu"
                                    class="w-full h-auto border border-gray-300 rounded-md shadow-lg">
                                <button wire:click="$set('receipt', null)" type="button"
                                    class="absolute p-1 text-white bg-red-600 rounded-full top-2 right-2 hover:bg-red-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                        <input wire:model="receipt" type="file" id="receipt" class="hidden" accept="image/*">
                        @error('receipt')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-around mt-4">
                    @if ($notification->reponse)
                        <div
                            class="px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-transparent rounded-md shadow-sm cursor-not-allowed">
                            Réponse envoyée
                        </div>
                    @else
                        <button wire:click="sendRecu"
                            class="px-6 py-2 font-semibold text-white bg-blue-500 rounded-md shadow-md hover:bg-blue-600">
                            Envoyer
                        </button>
                        <button wire:click="resetForm"
                            class="px-6 py-2 font-semibold text-black bg-gray-300 rounded-md shadow-md hover:bg-gray-400">
                            Annuler
                        </button>
                    @endif
                </div>
            </div>
        @elseif ($notification->type === 'App\Notifications\DepositSend')
            <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
                @if (session()->has('success'))
                    <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-300 rounded-md">
                        {{ session('error') }}
                    </div>
                @endif
                <!-- Titre -->
                <h2 class="mb-6 text-2xl font-semibold text-center text-gray-800">Verification du recu</h2>

                <!-- Informations de l'utilisateur -->
                <div class="flex items-center mb-6 space-x-4">
                    <div class="flex-shrink-0">
                        <img src="{{ $userDeposit->photo ?? 'https://via.placeholder.com/100' }}"
                            alt="Photo de l'utilisateur" class="w-16 h-16 border border-gray-300 rounded-full">
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900">{{ $userDeposit->name ?? 'Utilisateur inconnu' }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $userDeposit->email ?? 'Email non disponible' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 text-center border rounded-lg bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-700">Montant envoyé</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($amountDeposit ?? 0, 0, ',', ' ') }}
                        </p>
                    </div>
                    <div class="p-4 text-center border rounded-lg bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-700">Montant à recevoir</h3>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($roiDeposit ?? 0, 0, ',', ' ') }}
                            CFA</p>
                    </div>
                </div>


                <div class="flex justify-around mt-4">
                    @if ($notification->reponse)
                        <div
                            class="px-4 py-2 text-sm font-medium text-black bg-gray-300 border border-transparent rounded-md shadow-sm cursor-not-allowed">
                            Réponse envoyée
                        </div>
                    @else
                        <button wire:click="montantRecu"
                            class="px-6 py-2 font-semibold text-white bg-green-500 rounded-md shadow-md hover:bg-green-600">
                            j'ai recu
                        </button>
                        <button wire:click="nonrecu"
                            class="px-6 py-2 font-semibold text-white bg-red-500 rounded-md shadow-md hover:bg-red-600">
                            Non, je n'es pas recu
                        </button>
                    @endif
                </div>

            </div>

            {{-- general --}}
        @elseif ($notification->type === 'App\Notifications\VerifUser')
            @livewire('verif-user', ['id' => $id])
        @elseif ($notification->type === 'App\Notifications\mainleveclient')
            @livewire('mainleveclient', ['id' => $id])
        @endif
    </div>




@endsection
