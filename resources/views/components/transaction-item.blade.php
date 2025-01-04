@props(['transaction', 'userId'])

<div x-data="{ showDetails: false }" class="relative">
    <div @click="showDetails = true" class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 rounded-xl transition duration-150 ease-in-out">
        <div class="flex items-center">
            <!-- Icône de transaction -->
            <div class="flex-shrink-0">
                @switch($transaction->type)
                    @case('Réception')
                        <div class="p-2 text-green-500 bg-green-100 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                    @break
                    @case('Envoie')
                        <div class="p-2 text-red-500 bg-red-100 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                        </div>
                    @break
                    @case('Gele')
                        <div class="p-2 text-blue-500 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @break
                    @case('Commission')
                        <div class="p-2 text-purple-500 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @break
                @endswitch
            </div>

            <!-- Détails de la transaction -->
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-900">
                    @switch($transaction->type)
                        @case('Réception')
                            <span class="text-green-600">{{ $transaction->description }}</span>
                        @break
                        @case('Envoie')
                            <span class="text-red-600">{{ $transaction->description }}</span>
                        @break
                        @case('Gele')
                            <span class="text-blue-600">{{ $transaction->description }}</span>
                        @break
                        @case('Commission')
                            <span class="text-purple-600">{{ $transaction->description }}</span>
                        @break
                    @endswitch
                </p>
                <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Montant -->
        <div class="text-right">
            <p class="text-sm font-medium {{ $transaction->type == 'Réception' ? 'text-green-600' : ($transaction->type == 'Commission' ? 'text-purple-600' : ($transaction->type == 'Gele' ? 'text-blue-600' : 'text-red-600')) }}">
                {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
            </p>
            <p class="text-xs text-gray-500">{{ $transaction->type_compte }}</p>
        </div>
    </div>

    <!-- Modal des détails -->
    <div x-show="showDetails" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div x-show="showDetails" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="showDetails = false"></div>

            <!-- Modal -->
            <div x-show="showDetails" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="showDetails = false" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Détails de la Transaction
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Type</p>
                                    <p class="font-medium">{{ $transaction->type }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Montant</p>
                                    <p class="font-medium">{{ number_format($transaction->amount, 2, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Référence</p>
                                    <p class="font-medium">{{ $transaction->reference_id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Statut</p>
                                    <p class="font-medium">{{ $transaction->status }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Type de compte</p>
                                    <p class="font-medium">{{ $transaction->type_compte }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Date</p>
                                    <p class="font-medium">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">Description</p>
                                <p class="font-medium">{{ $transaction->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js pour le modal -->
@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
