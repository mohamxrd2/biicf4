<div class="container px-4 py-6 mx-auto">

    <div class="overflow-hidden bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Détails de la Commande
                {{-- @if ($notification->type_achat == 'Take Away')
                    Retrait au magasin
                @else
                    Avec livraison
                @endif --}}
            </h1>
        </div>
        <div class="p-6">


            <!-- Détails de la Commande -->
            <div class="p-4 mb-6 rounded-lg shadow-sm bg-gray-50">
                <h2 class="mb-2 text-lg font-semibold text-gray-700">Informations de la Commande</h2>
                <ul>
                    <li class="flex justify-between py-1"><span class="font-medium">Numéro de Commande:</span>
                        <span>{{ $notification->data['code_unique'] }}</span>
                    </li>
                    <li class="flex justify-between py-1"><span class="font-medium">Date de Commande/
                            Heure:</span>
                        <span>{{ $notification->created_at }}</span>
                    </li>
                    <li class="flex justify-between py-1"><span class="font-medium">Fournisseur:</span>
                        <span>{{ Auth::user()->name }}</span>
                    </li>
                    {{-- <li class="flex justify-between py-1"><span class="font-medium">date prevue de recuperation:</span>
                        <span>{{ $offregroupe->date_tot }} - {{ $offregroupe->date_tard }}</span>
                    </li>
                    <li class="flex justify-between py-1"><span class="font-medium">periode:</span>
                        <span>{{ $offregroupe->dayPeriod }}</span>
                    </li>
                    <li class="flex justify-between py-1"><span class="font-medium">heure prevue de recuperation:</span>
                        <span>{{ $offregroupe->timeStart }} - {{ $offregroupe->timeEnd }}</span>
                    </li> --}}
                    <li class="flex justify-between py-1">
                        <span class="font-medium">Statut:</span>
                        <span
                            class="
                               @if ($notification->reponse == 'accepte') text-green-500
                               @elseif($notification->reponse == 'refuser') text-red-500
                               @else  text-yellow-500 @endif">
                            @if ($notification->reponse == 'accepte')
                                Validé
                            @elseif($notification->reponse == 'refuser')
                                Rejeté
                            @else
                                En attente de validation
                            @endif
                        </span>

                    </li>

                </ul>
            </div>

            <!-- details de la commande -->
            <div class="p-6 rounded-lg ">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Éléments de la Commande</h2>

                <button type="button" data-modal-target="crypto-modal" data-modal-toggle="crypto-modal"
                    class="text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700">
                    <svg aria-hidden="true" class="w-4 h-4 me-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                        </path>
                    </svg>
                    liste des clients groupés/quantité
                </button>

                <!-- Main modal -->
                <div id="crypto-modal" tabindex="-1" aria-hidden="true"
                    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Liste Quantité Individuelles(Commandes)
                                </h3>
                                <button type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-modal-toggle="crypto-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-4 md:p-5">
                                <ul class="my-4 space-y-3">
                                    @foreach ($groupages as $groupage)
                                        <li>
                                            <a href="#"
                                                class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg bg-gray-50 hover:bg-gray-100 group hover:shadow dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white">

                                                <span
                                                    class="flex-1 ms-3 whitespace-nowrap">{{ $groupage->user->name ?? 'Utilisateur inconnu' }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-gray-500 bg-gray-200 rounded dark:bg-gray-700 dark:text-gray-400">
                                                    {{ $groupage->quantite }}
                                                    (unités)
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div>
                                    <a href="#"
                                        class="inline-flex items-center text-xs font-normal text-gray-500 hover:underline dark:text-gray-400">
                                        <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7.529 7.988a2.502 2.502 0 0 1 5 .191A2.441 2.441 0 0 1 10 10.582V12m-.01 3.008H10M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        Vous </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="mb-6 text-sm text-gray-600">
                    Vous serez débité de <strong>1%</strong> sur le prix de la marchandise.
                </p>

                <!-- Informations sur le produit/service -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold text-gray-800">Produit / Service commandé</h2>
                        <div class="mt-2 space-y-4">
                            <!-- Exemple de produit/service -->
                            <div class="flex items-center">
                                <img src="{{ $offregroupe->produit->photoProd1 ? asset('post/all/' . $offregroupe->produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="Image produit" class="w-20 h-20 object-cover rounded-lg">
                                <div class="ml-4 flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Commande avec livraison</h3>
                                    <p class="text-sm text-gray-600">
                                        <strong>Lieu de livraison :</strong> {{ $offregroupe->localite }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <strong>Conditionnement :</strong> {{ $offregroupe->produit->condProd }}
                                    </p>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">Produit :
                                        {{ $offregroupe->produit->name }}
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <strong>Quantité Total(apres groupage) :</strong> {{ $quantites }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        <strong>Prix Unitaire :</strong> {{ $offregroupe->produit->prix }} FCFA
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé de la commande -->
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold text-gray-800">Résumé de la commande</h2>
                        <div class="mt-2 space-y-1 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Sous-total :</span>
                                <span>{{ number_format($prixTotal, 2, ',', '.') }}
                                    FCFA</span>
                            </div>
                            <div class="flex justify-between">
                                <span>frais de service :</span>
                                <span>1% </span>
                            </div>
                            <div class="flex justify-between font-medium text-gray-900">
                                <span>Vous recevrez :</span>
                                <span>{{ number_format($prixFin, 2, ',', '.') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="flex flex-col justify-end gap-4 mt-6 md:flex-row">
                @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                    <div class="w-full p-2 bg-gray-300 border rounded-md">
                        <p class="font-medium text-center text-md">Notification envoyée au client</p>
                    </div>
                @elseif ($notification->type_achat == 'Take Away')
                    <button wire:click="refuser" id="btn-refuser" type="submit"
                        class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-red-700">Refuser</button>
                    <button wire:click="takeaway" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700"
                        wire:loading.attr="disabled" wire:loading.class="bg-green-400 cursor-not-allowed">
                        <span wire:loading.remove>Procéder à la confirmation</span> <!-- Texte lorsque non chargé -->
                        <span wire:loading>Chargement...</span> <!-- Texte affiché pendant le chargement -->
                    </button>
                @else
                    <div x-data="{ isOpen: false, open: false, textareaValue: 'Emballage:..., Dimension:..., Poids:..., Autre:...' }" x-cloak>
                        <!-- Buttons to open modal and refuse -->
                        <button @click="isOpen = true"
                            class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Acheminement pour
                            négociation</button>
                        <button wire:click="refuser" id="btn-refuser" type="submit"
                            class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>

                        <!-- Modal -->
                        <div x-show="isOpen" id="hs-basic-modal"
                            class="fixed top-0 left-0 z-50 w-full h-full overflow-y-auto bg-black bg-opacity-50 hs-overlay hs-overlay-open:opacity-100 hs-overlay-open:duration-500">
                            <div class="m-3 sm:max-w-lg sm:w-full sm:mx-auto">
                                <div class="flex flex-col bg-white border shadow-sm pointer-events-auto rounded-xl">
                                    <div class="flex items-center justify-between px-4 py-3 border-b">
                                        <h3 class="font-bold text-gray-800">Envoi au livreur</h3>
                                        <button @click="isOpen = false" class="text-gray-800 hover:text-gray-600">
                                            <span class="sr-only">Close</span>
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-gray-800">
                                            @if ($nombreLivr)
                                                <div>
                                                    <p><strong>Le nombre total de livreurs
                                                            disponible:</strong>{{ $livreursCount }}</p>
                                                </div>
                                            @else
                                                Aucun livreur disponible dans la zone
                                            @endif
                                        </p>
                                    </div>
                                    @if ($nombreLivr == 0)
                                    @else
                                        <div class="flex items-center justify-end px-4 py-3 border-t">
                                            <div x-data="{ open: false }">
                                                <!-- Button to toggle textarea visibility -->
                                                <button @click="open = !open"
                                                    class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                                                    Ajouter le nouveau Conditionnement
                                                </button>

                                                <!-- Textarea and action buttons -->
                                                <div x-show="open" class="mt-4">
                                                    <form wire:submit.prevent="accepter"
                                                        enctype="multipart/form-data">

                                                        <textarea wire:model="textareaValue" x-model="textareaValue" class="w-full p-2 border border-gray-300 rounded"
                                                            rows="6" required>
                                                        </textarea>

                                                        <!-- Champ de téléchargement de fichier -->
                                                        <input type="file" wire:model="photoProd" class="mt-2"
                                                            required />

                                                        <div class="flex justify-end mt-2 space-x-2">
                                                            <button type="submit"
                                                                class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                                                                <span wire:loading.remove>Confirmer</span>
                                                                <span wire:loading>En cours...</span>
                                                            </button>
                                                            <button @click="open = false"
                                                                class="px-4 py-2 text-gray-800 bg-gray-200 rounded hover:bg-gray-300">
                                                                Annuler
                                                            </button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </div>

</div>
