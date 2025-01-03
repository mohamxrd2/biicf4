<div id="search-input" class="min-h-screen bg-gray-50">
    {{-- Header avec barre de recherche --}}
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between h-20 px-4 sm:px-6 lg:px-8">

                {{-- Barre de recherche --}}
                <div class="mx-8 w-full">
                    <div class="relative">
                        <input type="search" wire:model.live="keyword"
                            class="w-full h-12 pl-12 pr-4 rounded-full border-2 border-purple-100 focus:border-purple-500
                            focus:ring-2 focus:ring-purple-200 transition-all duration-200"
                            placeholder="Rechercher un produit ou service...">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        {{-- Filtres --}}
        {{-- Filtres --}}
        <div class="bg-purple-50 border-t border-purple-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Zone économique --}}
                    <select wire:model.live="zone_economique"
                        class="w-full h-11 pl-4 pr-8 rounded-lg border-2 border-purple-100 focus:border-purple-500
                focus:ring-2 focus:ring-purple-200 bg-white transition-all duration-200">
                        <option value="">Zone économique</option>
                        <option value="proximite">Proximité</option>
                        <option value="locale">Locale</option>
                        <option value="departementale">Départementale</option>
                        <option value="nationale">Nationale</option>
                        <option value="sous_regionale">Sous Régionale</option>
                        <option value="continentale">Continentale</option>
                    </select>

                    {{-- Type --}}
                    <select wire:model.live="type"
                        class="w-full h-11 pl-4 pr-8 rounded-lg border-2 border-purple-100 focus:border-purple-500
                focus:ring-2 focus:ring-purple-200 bg-white transition-all duration-200">
                        <option value="">Type</option>
                        <option value="Produit">Produit</option>
                        <option value="Service">Service</option>
                    </select>

                    {{-- Quantité --}}
                    <div class="relative">
                        <input type="number" wire:model.live="qte"
                            class="w-full h-11 pl-4 pr-8 rounded-lg border-2 border-purple-100 focus:border-purple-500
                    focus:ring-2 focus:ring-purple-200 bg-white transition-all duration-200"
                            placeholder="Quantité minimale">
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                        </div>
                    </div>

                    {{-- Prix --}}
                    <div class="relative">
                        <input type="number" wire:model.live="prix"
                            class="w-full h-11 pl-4 pr-8 rounded-lg border-2 border-purple-100 focus:border-purple-500
                    focus:ring-2 focus:ring-purple-200 bg-white transition-all duration-200"
                            placeholder="Prix maximum">
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0
                            2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0
                            11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Boutons de filtrage optionnels --}}
                    <div class="md:col-span-4 flex flex-wrap gap-2 mt-2">
                        <button wire:click="resetFilters"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200
                    text-gray-800 text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003
                            8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Réinitialiser les filtres
                        </button>

                        <button
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700
                    text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1
                            1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013
                            6.586V4z" />
                            </svg>
                            Appliquer les filtres
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Contenu principal --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($resultCount == 0)
            {{-- État vide amélioré --}}
            <div class="flex flex-col items-center justify-center py-12">

                <h2 class="text-2xl font-bold text-gray-900 mb-2">Aucun résultat trouvé</h2>
                <p class="text-gray-500 text-center max-w-md">
                    Essayez de modifier vos critères de recherche ou explorez d'autres catégories.
                </p>
            </div>
        @elseif ($keyword == '')
            {{-- Message de bienvenue --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-8 py-12">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">
                        Trouvez les meilleurs produits et services
                    </h1>
                    <p class="text-gray-500 mb-8">
                        Utilisez la barre de recherche ou parcourez les catégories pour découvrir des offres
                        exceptionnelles de nos fournisseurs vérifiés.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Statistiques --}}
                        <div class="bg-purple-50 rounded-xl p-6">
                            <div class="text-3xl font-bold text-purple-600 mb-2">12,334</div>
                            <div class="text-sm text-gray-600">Produits disponibles</div>
                        </div>
                        {{-- Autres stats... --}}
                        <div class="bg-purple-50 rounded-xl p-6">
                            <div class="text-3xl font-bold text-purple-600 mb-2">1,234+</div>
                            <div class="text-sm text-gray-600">Utilisateur actifs</div>
                        </div>
                        {{-- Autres stats... --}}
                        <div class="bg-purple-50 rounded-xl p-6">
                            <div class="text-3xl font-bold text-purple-600 mb-2">1,234+</div>
                            <div class="text-sm text-gray-600">Credi accordé</div>
                        </div>

                    </div>
                </div>
            </div>
        @else
            {{-- Liste des produits --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($produits as $produit)
                    <div
                        class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl
                        transform transition-all duration-300 hover:-translate-y-1">
                        {{-- Image --}}
                        <div class="relative aspect-w-16 aspect-h-9">
                            <img src="{{ asset($produit->photoProd1 ? 'post/all/' . $produit->photoProd1 : 'img/noimg.jpeg') }}"
                                class="w-full h-48 object-cover transform transition-transform duration-300
                                group-hover:scale-105"
                                alt="{{ $produit->name }}">
                            <div class="absolute top-4 right-4">
                                <div
                                    class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full
                                    shadow-lg flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0
                                            00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364
                                            1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0
                                            00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                                            1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1
                                            0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-sm font-medium">4.5</span>
                                </div>
                            </div>
                        </div>

                        {{-- Contenu --}}
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">
                                    {{ $produit->name }}
                                </h3>
                                <span class="text-lg font-bold text-purple-600">
                                    {{ number_format($produit->prix, 0, ',', ' ') }} XOF
                                </span>
                            </div>

                            <div class="flex items-center text-gray-500 text-sm mb-4">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8
                                        8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $produit->user->commune }} • {{ $produit->user->ville }}
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($produit->created_at)->diffForHumans() }}
                                </span>
                                <a href="{{ route('biicf.postdet', $produit->id) }}"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700
                                    text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    Voir le produit
                                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12 flex justify-center">
                @if ($produits instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $produits->links() }}
                @endif
            </div>
        @endif
    </main>
</div>

@if($error)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ $error }}</span>
    </div>
@endif
