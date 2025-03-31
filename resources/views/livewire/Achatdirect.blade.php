<div class="container px-4 py-6 mx-auto">
    <div class="overflow-hidden bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Détails de la Commande
                @if ($notification->data['type_achat'] == 'Take Away')
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
                    Vous serez débité de <strong>10%</strong> sur le prix de la marchandise.
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
                                <span>10% </span>
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
                @elseif ($notification->data['type_achat'] == 'Take Away')
                    <button wire:click="refuser" id="btn-refuser" type="submit"
                        class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-red-700">Refuser</button>
                    <button wire:click="takeaway" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700"
                        wire:loading.attr="disabled" wire:loading.class="bg-green-400 cursor-not-allowed">
                        <span wire:loading.remove>Procéder à la confirmation</span> <!-- Texte lorsque non chargé -->
                        <span wire:loading>Chargement...</span> <!-- Texte affiché pendant le chargement -->
                    </button>
                @else
                    <div x-data="{ isOpen: false, open: false, textareaValue: 'Emballage:, Dimension:, Poids:, Autre:' }" x-cloak>
                        <!-- Main action buttons -->
                        <div class="flex space-x-4">
                            <button @click="isOpen = true"
                                class="flex items-center px-6 py-3 text-white transition-colors bg-green-500 rounded-lg hover:bg-green-600 focus:ring-2 focus:ring-green-400">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Acheminement pour négociation
                            </button>
                            <button wire:click="refuser" id="btn-refuser"
                                class="px-6 py-3 text-white transition-colors bg-red-500 rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-400">
                                Refuser
                            </button>
                        </div>

                        <!-- Modal -->
                        <div x-show="isOpen"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="w-full max-w-xl bg-white rounded-xl shadow-2xl">
                                    <!-- Modal Header -->
                                    <div class="flex items-center justify-between p-6 border-b">
                                        <h3 class="text-xl font-semibold text-gray-800">Envoi au livreur</h3>
                                        <button @click="isOpen = false" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="p-6">
                                        <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                                            @if ($nombreLivr)
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <p class="text-gray-700"><strong>Livreurs disponibles :</strong> {{ $livreursCount }}</p>
                                                </div>
                                            @else
                                                <div class="flex items-center space-x-2 text-red-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <p>Aucun livreur disponible dans la zone</p>
                                                </div>
                                            @endif
                                        </div>

                                        @if ($nombreLivr != 0)
                                            <div x-data="{ open: false }">
                                                <button @click="open = !open"
                                                    class="w-full px-4 py-3 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-400">
                                                    Ajouter le nouveau Conditionnement
                                                </button>

                                                <div x-show="open"
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                                    class="mt-4">
                                                    <form wire:submit.prevent="accepter" class="space-y-4">
                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-medium text-gray-700">Détails du conditionnement</label>
                                                            <textarea wire:model="textareaValue" x-model="textareaValue"
                                                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                                                                rows="4" required></textarea>
                                                        </div>

                                                        <div class="space-y-2">
                                                            <label class="block text-sm font-medium text-gray-700">Photo du produit</label>
                                                            <input type="file" wire:model="photoProd"
                                                                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400"
                                                                required />
                                                        </div>

                                                        <div class="flex justify-end space-x-3">
                                                            <button @click="open = false" type="button"
                                                                class="px-4 py-2 text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                                                                Annuler
                                                            </button>
                                                            <button type="submit"
                                                                class="px-4 py-2 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600 disabled:opacity-50"
                                                                wire:loading.attr="disabled">
                                                                <span wire:loading.remove>Confirmer</span>
                                                                <span wire:loading>En cours...</span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
