<div class="max-w-5xl mx-auto">
    <x-negociation-card name="NÉGOCIATION POUR L'APPEL OFFRE" mot='haut' :lastActivity="$lastActivity" :nombreParticipants="$nombreParticipants"
        :isNegociationActive="$isNegociationActive" :commentCount="$commentCount" />

    <x-information-nego :offreIniatiale="$offreIniatiale" :prixLePlusBas="$prixLePlusBas" :id="$id" />



    <div class="container mx-auto px-4" x-data="{ showDetails: true, showOffers: true }">

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

        <x-negociation :achatdirect="$offgroupe" :comments="$comments" :successMessage="$successMessage" :errorMessage="$errorMessage" />


    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('negotiation', () => ({
            showDetails: true,
            showOffers: true,
        }))
    })
</script>

</div>
