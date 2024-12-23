<div class="max-w-5xl mx-auto">
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg mb-6">
        <!-- Barre supérieure avec statut -->
        <div class="border-b border-white border-opacity-20">
            <div class="container mx-auto px-6 py-2">
                <div class="flex justify-between items-center text-white">
                    <span class="text-sm flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        Dernière activité: il y a 5 minutes
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-coc"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-white w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                    </svg>

                    <!-- Tooltip -->
                    <div id="tooltip-coc" role="tooltip"
                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                        Le prix le plus bas remportera la négociation. <br />
                        Le gagnant recevra une notification.
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            <span class="text-sm">{{ $nombreParticipants ?? '0' }} participants</span>
                        </span>
                        <div class="relative">
                            <button
                                class="flex items-center space-x-1 bg-white bg-opacity-20 rounded-full px-3 py-1 hover:bg-opacity-30 transition-colors">
                                <span class="animate-pulse h-2 w-2 bg-green-400 rounded-full"></span>
                                <span class="text-sm">Négociation en cours</span>
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="relative p-6">
            <div class="relative">
                <div
                    class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <!-- Titre et informations principales -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white p-2 rounded-lg shadow-md">
                                <i class="fas fa-truck text-blue-600 text-xl"></i>
                            </div>
                            <h1 class="text-xl md:text-2xl font-bold text-white tracking-wide">
                                NÉGOCIATION POUR L'APPEL OFFRE
                            </h1>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <span
                                class="flex items-center bg-white bg-opacity-20 text-white px-3 py-1 rounded-full text-sm hover:bg-opacity-30 transition-colors cursor-pointer">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Date prévue: 25 Mars 2024
                            </span>
                            <span
                                class="flex items-center bg-white bg-opacity-20 text-white px-3 py-1 rounded-full text-sm hover:bg-opacity-30 transition-colors cursor-pointer">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Distance: 250 km
                            </span>
                        </div>
                    </div>

                    <!-- Statut et actions -->
                    <div class="flex items-center space-x-4">
                        <div class="bg-white rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow">
                            <div class="text-center">
                                <span class="text-gray-600 text-sm block">Offres reçues</span>
                                <span class="text-2xl font-bold text-blue-600">12</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Informations de la négociation -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="text-sm text-gray-600">Offre initiale</div>
            <div class="text-lg font-bold">{{ $offgroupe->lowestPricedProduct . ' FCFA' }}</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="text-sm  text-gray-600">Meilleure offre</div>
            <div class="text-lg font-bold">{{ $prixLePlusBas ? $prixLePlusBas . ' FCFA' : 'N/A' }}</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="text-sm text-gray-600">Temps restant</div>
            <div id="countdown-container" x-data="countdownTimer({{ json_encode($oldestCommentDate) }}, {{ json_encode($time) }})" class="flex items-center space-x-2">
                <div id="countdown" x-show="oldestCommentDate"
                    class="bg-red-200 text-red-600 font-bold px-4 py-2 rounded-lg flex items-center">
                    <div x-text="hours">--</div>j
                    <span>:</span>
                    <div x-text="minutes">--</div>m
                    <span>:</span>
                    <div x-text="seconds">--</div>s
                </div>
            </div>
        </div>
    </div>
    <div class="container mx-auto px-4" x-data="{ showDetails: false, showOffers: false }">

        <!-- Détails du produit -->
        <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
            <div class="p-3">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Détails du produit</h2>
                    <button @click="showDetails = !showDetails"
                        class="text-blue-600 hover:text-blue-800 focus:outline-none">
                        <i class="fas" :class="showDetails ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>

                <div x-show="showDetails" x-collapse class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6">
                        {{-- Section Image --}}
                        <div class="relative group">
                            <img src="{{ asset('post/all/' . $produit->photoProd1) }}" alt="Produit"
                                class="w-full h-80 object-cover rounded-lg shadow-md transition-transform duration-300 group-hover:scale-105">
                        </div>

                        {{-- Section Détails --}}
                        <div class="space-y-6">
                            {{-- En-tête du produit --}}
                            <div class="border-b border-gray-200 pb-4">
                                <h1 class="text-2xl font-bold text-gray-800 mb-3">{{ $produit->name }}</h1>
                                <a href="{{ route('biicf.postdet', $produit->id) }}"
                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                    <span>Voir le produit</span>
                                    <svg class="w-5 h-5 ml-2 transform transition-transform group-hover:translate-x-1"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                    </svg>
                                </a>
                            </div>

                            {{-- Section des spécifications --}}
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Spécifications</h3>
                                <ul class="space-y-4">
                                    @php
                                        $specifications = [
                                            'Quantité' => $produit->quantity . ' unités',
                                            'Référence du produit' => $produit->reference . ' unités',
                                            'Quantité minimale' => $produit->qteProd_max ?? 'Non spécifiée',
                                            'Lieu de livraison' => $produit->localite ?? 'Non spécifié',
                                            'Continent' => $produit->user->continent,
                                            $produit->user->sous_region,
                                            $produit->user->pays,
                                            $produit->user->departe,
                                            $produit->user->ville,
                                            $produit->user->commune ?? 'Non spécifié',

                                            'Date de récupération' => isset($offgroupe->dateTot, $offgroupe->dateTard)
                                                ? "{$offgroupe->dateTot} - {$offgroupe->dateTard}"
                                                : 'Non spécifiée',
                                        ];
                                    @endphp

                                    @foreach ($specifications as $label => $value)
                                        <li class="flex flex-col sm:flex-row sm:items-start gap-2">
                                            <span
                                                class="font-medium text-gray-700 min-w-[160px]">{{ $label }}:</span>
                                            <span class="text-gray-600 flex-1">{{ $value }}</span>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Discussion -->
        <div class="flex-1 w-full md:w-auto">
            <!-- Discussion de négociation -->
            <div class="bg-white shadow-lg rounded-lg p-2">

                <!-- Tableau des offres -->
                <div class="bg-white rounded-lg shadow-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold">Négociation en direct</h2>
                            <button @click="showOffers = !showOffers"
                                class="text-blue-600 hover:text-blue-800 focus:outline-none">
                                <i class="fas" :class="showOffers ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                        </div>

                        <div x-show="showOffers" x-collapse>

                            <!-- Zone de chat -->
                            <div class="border rounded-lg mb-4">
                                <!-- Messages -->
                                <div
                                    class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-200 font-normal space-y-3 relative dark:border-slate-700/40">
                                    <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                                    <!-- L'investisseur unique et tous les autres utilisateurs voient la partie de négociation -->
                                    @php
                                        // Trouver le plus grand taux dans la liste des commentaires
                                        $maxPrice = $comments->max('prixTrade');

                                        // Trouver le commentaire le plus récent avec le taux maximal
                                        $latestMaxPriceComment = $comments
                                            ->where('prixTrade', $maxPrice)
                                            ->sortByDesc('created_at') // Trier par date la plus récente
                                            ->first();
                                    @endphp
                                    @foreach ($comments as $comment)
                                        <!-- Message -->
                                        <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                                            <div class="flex items-start gap-3">
                                                <!-- Photo utilisateur -->
                                                <img src="{{ asset($comment->user->photo) }}" alt="Profile Picture"
                                                    class="w-10 h-10 rounded-full object-cover shadow-md" />
                                                <div class="flex-1">
                                                    <!-- Informations utilisateur -->
                                                    <div class="flex justify-between items-center">
                                                        <span
                                                            class="font-semibold text-gray-800 text-sm">{{ $comment->user->name }}</span>
                                                        <span
                                                            class="text-xs text-gray-400">{{ $comment->created_at }}</span>
                                                    </div>
                                                    <!-- Message -->
                                                    <p class="text-sm text-gray-600">
                                                        Je peux descendre le prix a <span
                                                            class="text-green-500 font-semibold">
                                                            {{ number_format($comment->prixTrade, 2, ',', ' ') }}
                                                            FCFA</p>
                                                    </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <!-- Offre et bouton -->
                                            <div class="flex justify-between items-center">
                                                <span class="text-lg font-bold text-gray-800">
                                                    {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA</p>
                                                </span>
                                                @if ($comment->id == $latestMaxPriceComment->id)
                                                    <button
                                                        class="flex items-center gap-2 text-green-600 hover:text-green-700 font-medium py-2 px-4 bg-green-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.588 4.89a1 1 0 00.95.69h5.127c.969 0 1.371 1.24.588 1.81l-4.15 3.02a1 1 0 00-.364 1.118l1.588 4.89c.3.921-.755 1.688-1.54 1.118l-4.15-3.02a1 1 0 00-1.176 0l-4.15 3.02c-.785.57-1.838-.197-1.539-1.118l1.588-4.89a1 1 0 00-.364-1.118L2.792 9.317c-.783-.57-.38-1.81.588-1.81h5.127a1 1 0 00.95-.69l1.588-4.89z" />
                                                        </svg>
                                                        Meilleure offre
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Zone de saisie -->
                                <div class="border-t p-4">
                                    <form wire:submit.prevent="commentoffgroup">
                                        @if (!$offgroupe->count)
                                            <div class="flex space-x-4">
                                                <div class="flex-1">
                                                    <div class="relative">
                                                        <span
                                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></span>
                                                        <input type="number" name="prixTrade" id="prixTrade"
                                                            wire:model="prixTrade"
                                                            class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-purple-500 focus:ring-purple-500"
                                                            placeholder="Faire une offre..." required>
                                                    </div>
                                                </div>
                                                <button type="submit"
                                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                                                    <span>Envoyer</span>
                                                    <span wire:loading.remove>

                                                        <i class="fas fa-paper-plane"></i>
                                                    </span>
                                                    <span wire:loading>
                                                        <svg class="w-5 h-5 animate-spin inline-block"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                                        </svg>
                                                    </span>
                                                </button>
                                            </div>
                                        @endif

                                    </form>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('negotiation', () => ({
            showDetails: false,
            showOffers: false,
        }))
    })
</script>
<script src="{{ asset('js/countdown.js') }}?v=1.0.0" defer></script>

</div>
