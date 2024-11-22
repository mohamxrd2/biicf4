<div class="container px-4 py-6 mx-auto">
    

    <div class="overflow-hidden bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Détails de la Commande
                @if ($notification->type_achat == 'Take Away')
                    Take Away
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
                        <span>{{ $notification->data['date_tot'] }} - {{ $notification->data['date_tard'] }}</span>
                    </li>
                    <li class="flex justify-between py-1"><span class="font-medium">periode:</span>
                        <span>{{ $notification->data['dayPeriod'] }}</span>
                    </li>
                    <li class="flex justify-between py-1"><span class="font-medium">heure prevue de recuperation:</span>
                        <span>{{ $notification->data['timeStart'] }} - {{ $notification->data['timeEnd'] }}</span>
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

            <!-- Liste des Produits -->
            <div class="p-4 rounded-lg shadow-sm bg-gray-50">
                <h2 class="mb-2 text-lg font-semibold text-gray-700">Éléments de la Commande</h2>
                <p class="my-3 text-sm text-gray-500">Vous serez débité de 10% sur le prix de la marchandise
                </p>

                <!-- Tableau pour écrans moyens et plus grands -->
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="text-left text-gray-600 bg-gray-100">
                                <th class="px-4 py-2">Produit ou Service</th>
                                <th class="px-4 py-2">Quantité</th>
                                <th class="px-4 py-2">Lieu de livraison</th>
                                <th class="px-4 py-2">Spécificité</th>
                                <th class="px-4 py-2">Prix de la commande</th>
                                <th class="px-4 py-2">Somme finale</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2">{{ $notification->data['nameProd'] }}</td>
                                <td class="px-4 py-2">{{ $notification->data['quantité'] }}</td>
                                <td class="px-4 py-2">{{ $notification->data['localite'] }}</td>
                                <td class="px-4 py-2">{{ $notification->data['specificite'] }}</td>
                                <td class="px-4 py-2">
                                    {{ isset($notification->data['montantTotal']) ? number_format($notification->data['montantTotal'], 2, ',', '.') : 'N/A' }}
                                </td>
                                @php
                                    $prixArtiche = $notification->data['montantTotal'] ?? 0;
                                    $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                                @endphp
                                <td class="px-4 py-2">{{ number_format($sommeRecu, 2, ',', '.') }} Fcfa</td>
                            </tr>
                            <!-- Ajoutez d'autres lignes de produits si nécessaire -->
                        </tbody>
                    </table>
                </div>

                <!-- Liste pour petits écrans -->
                <div class="block md:hidden">
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-700">Produit ou Service:</div>
                            <div class="text-gray-800">{{ $notification->data['nameProd'] }}</div>
                        </div>
                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-700">Quantité:</div>
                            <div class="text-gray-800">{{ $notification->data['quantité'] }}</div>
                        </div>
                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-700">Lieu de livraison:</div>
                            <div class="text-gray-800">{{ $notification->data['localite'] }}</div>
                        </div>
                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-700">Spécificité:</div>
                            <div class="text-gray-800">{{ $notification->data['specificite'] }}</div>
                        </div>
                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-700">Prix de la commande:</div>
                            <div class="text-gray-800">
                                {{ isset($notification->data['montantTotal']) ? number_format($notification->data['montantTotal'], 2, ',', '.') : 'N/A' }}
                                Fcfa
                            </div>

                        </div>
                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-700">Somme finale:</div>
                            @php
                                $prixArtiche = $notification->data['montantTotal'] ?? 0;
                                $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
                            @endphp
                            <div class="text-gray-800">
                                {{ number_format($sommeRecu, 2, ',', '.') }} Fcfa
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                class="flex items-center my-3 text-blue-700 hover:underline">
                Voir le produit
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 ml-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>

            <div class="flex flex-col justify-end gap-4 mt-6 md:flex-row">
                @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                    <div class="w-full p-2 bg-gray-300 border rounded-md">
                        <p class="font-medium text-center text-md">Réponse envoyée</p>
                    </div>
                @elseif ($notification->type_achat == 'Take Away')
                    <button wire:click="takeaway"
                        class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                    <button wire:click="refuser" id="btn-refuser" type="submit"
                        class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                @elseif ($notification->type_achat == 'Reservation')
                    <button wire:click="takeaway"
                        class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
                    <button wire:click="refuser" id="btn-refuser" type="submit"
                        class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                @else
                    <div x-data="{ isOpen: false, open: false, textareaValue: 'Emballage:..., Dimension:..., Poids:..., Autre:...' }" x-cloak>
                        <!-- Buttons to open modal and refuse -->
                        <button @click="isOpen = true"
                            class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>
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
                                                    {{-- <h3>Détails de la commande</h3> --}}
                                                    {{-- <p><strong>ID de client :</strong> {{ $Idsender }}</p>
                                                    <p><strong>Continent du client :</strong>
                                                        {{ $clientContinent }}</p>
                                                    <p><strong>Sous-Region du client :</strong>
                                                        {{ $clientSous_Region }}</p>
                                                    <p><strong>Pays du client :</strong> {{ $clientPays }}
                                                    </p>
                                                    <p><strong>Departement du client :</strong>
                                                        {{ $clientDepartement }}</p>
                                                    <p><strong>Commune du client :</strong>{{ $clientCommune }}
                                                    </p> --}}
                                                    <p><strong>Le nombre total de livreurs disponible
                                                            :</strong>{{ $livreursCount }}</p>


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
                                                        <input type="file" wire:model="photoProd1" class="mt-2"
                                                            required />

                                                        <div class="flex justify-end mt-2 space-x-2">
                                                            <button @click="open = false"
                                                                class="px-4 py-2 text-gray-800 bg-gray-200 rounded hover:bg-gray-300">
                                                                Annuler
                                                            </button>
                                                            <button type="submit"
                                                                class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                                                                <span wire:loading.remove>Envoyer</span>
                                                                <span wire:loading>En cours...</span>
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
