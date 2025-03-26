<!-- resources/views/components/negociation.blade.php -->

<div class="bg-white rounded-lg shadow-lg mb-6">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Négociation en direct</h2>
            <button @click="showOffers = !showOffers" class="text-blue-600 hover:text-blue-800 focus:outline-none">
                <i class="fas" :class="showOffers ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>

        <div x-show="showOffers" x-collapse>
            <!-- Zone de chat -->
            <div class="border rounded-lg mb-4">
                <!-- Messages -->
                <div
                    class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-200 font-normal space-y-3 relative">
                    @if ($achatdirect->count)
                        <div class="flex flex-col items-center justify-center h-full">
                            <div class="bg-red-50 p-4 rounded-lg text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-400 mx-auto mb-3"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-red-800 mb-1">Négociation terminée</h3>
                                <p class="text-red-600">Cette session de négociation est clôturée.</p>
                            </div>
                        </div>
                    @elseif($comments->isEmpty())
                        <div class="flex flex-col items-center justify-center h-full">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400 mx-auto mb-3"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-blue-800 mb-1">Aucune offre pour le moment</h3>
                                <p class="text-blue-600">Soyez le premier à faire une offre !</p>
                            </div>
                        </div>
                    @else
                        @php
                            $minPrice = $comments->min('prixTrade');
                            $oldestMinPriceComment = $comments
                                ->where('prixTrade', $minPrice)
                                ->sortBy('created_at')
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
                                            <span class="text-xs text-gray-400">{{ $comment->created_at }}</span>
                                        </div>
                                        <!-- Message -->
                                        <p class="text-sm text-gray-600">
                                            Je peux faire <span class="text-green-500 font-semibold">
                                                {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA</p>
                                        </span> la livraison.
                                        </p>
                                    </div>
                                </div>
                                <!-- Offre et bouton -->
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-800">
                                        {{ number_format($comment->prixTrade, 2, ',', ' ') }} FCFA</p>
                                    </span>
                                    @if ($comment->id == $oldestMinPriceComment->id)
                                        <button
                                            class="flex items-center gap-2 text-green-600 hover:text-green-700 font-medium py-2 px-4 bg-green-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.588 4.89a1 1 0 00.95.69h5.127c.969 0 1.371 1.24.588 1.81l-4.15 3.02a1 1 0 00-.364 1.118l1.588 4.89c.3.921-.755 1.688-1.54 1.118l-4.15-3.02a1 1 0 00-1.176 0l-4.15 3.02c-.785.57-1.838-.197-1.539-1.118l1.588-4.89a1 1 0 00-.364-1.118L2.792 9.317c-.783-.57-.38-1.81.588-1.81h5.127a1 1 0 00.95-.69l1.588-4.89z" />
                                            </svg>
                                            Meilleure offre
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Zone de saisie -->
                <div class="border-t p-4">
                    <form wire:submit.prevent="soumissionDePrix">
                        @error('prixTrade')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if (!$achatdirect->count)
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></span>
                                        <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
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
                                    <span wire:loading wire:target="soumissionDePrix">
                                        <svg class="w-5 h-5 animate-spin inline-block"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-2">
                                <span class="text-sm">La période de négociation est terminée</span>
                            </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
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
