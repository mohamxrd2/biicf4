<div class="container px-4 py-6 mx-auto">






    <div class="overflow-hidden bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Détails de la Commande
                @if ($notification->type_achat == 'Take Away')
                    Retrait au magasin
                @else
                    Avec livraison
                @endif
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
                    <li class="flex justify-between py-1"><span class="font-medium">date prevue de recuperation:</span>
                        <span>{{ $achatdirect->date_tot }} - {{ $achatdirect->date_tard }}</span>
                    </li>
                    <li class="flex justify-between py-1"><span class="font-medium">periode:</span>
                        <span>{{ $achatdirect->dayPeriod }}</span>
                    </li>
                    <li class="flex justify-between py-1"><span class="font-medium">heure prevue de recuperation:</span>
                        <span>{{ $achatdirect->timeStart }} - {{ $achatdirect->timeEnd }}</span>
                    </li>
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
                                <img src="{{ $achatdirect->photoProd ? asset('post/all/' . $achatdirect->photoProd) : asset('img/noimg.jpeg') }}"
                                    alt="Image produit" class="w-20 h-20 object-cover rounded-lg">
                                <div class="ml-4 flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Commande avec livraison</h3>
                                    <p class="text-sm text-gray-600">
                                        <strong>Lieu de livraison :</strong> {{ $achatdirect->localite }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <strong>Conditionnement :</strong> {{ $produits->condProd }}
                                    </p>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">Produit : {{ $produits->name }}
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <strong>Quantité demandée :</strong> {{ $achatdirect->quantité }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        <strong>Prix Unitaire :</strong> {{ $produits->prix }} FCFA
                                    </p>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ number_format($achatdirect->montantTotal, 2, ',', '.') }}
                                    FCFA</div>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé de la commande -->
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold text-gray-800">Résumé de la commande</h2>
                        <div class="mt-2 space-y-1 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Sous-total :</span>
                                <span>{{ number_format($achatdirect->montantTotal, 2, ',', '.') }}
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
                    <button wire:click="takeaway"
                        class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Procéder a la
                        confirmation</button>

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
                                                    <form wire:submit.prevent="accepter" enctype="multipart/form-data">

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
