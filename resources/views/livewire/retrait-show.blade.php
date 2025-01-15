<div class="min-h-screen " wire:poll.1500ms>
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-5xl mx-auto p-6">
            {{-- Messages de notification --}}
            @if (session()->has('message'))
                <div class="p-4 mb-6 text-white bg-green-500 rounded-lg shadow-md flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif
            
            @if (session()->has('error'))
                <div class="p-4 mb-6 text-white bg-red-500 rounded-lg shadow-md flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        
            {{-- Carte principale --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-blue-500/10 overflow-hidden border border-gray-100">
                {{-- En-tête avec effet de gradient amélioré --}}
                <div class="relative overflow-hidden px-8 pt-10 pb-8 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-gradient-to-br from-blue-500/20 to-purple-500/20 blur-3xl"></div>
                    <div class="relative">
                        <h2 class="text-3xl font-bold text-gray-900">
                            Détails de la demande de retrait
                        </h2>
                        <div class="mt-2 text-md font-medium text-gray-600">
                            Référence: <span class="text-indigo-600">{{ $retrait->reference }}</span>
                        </div>
                    </div>
                </div>
        
                {{-- Section des informations détaillées --}}
                <div class="border-t border-gray-100 bg-gray-50/50">
                    <dl class="divide-y divide-gray-100">
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Nom de l'utilisateur</dt>
                            <dd class="col-span-2 text-sm text-gray-900 font-medium">
                                {{ $retrait->user->name ?? 'Utilisateur inconnu' }}
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Montant demandé</dt>
                            <dd class="col-span-2">
                                <span class="text-lg font-bold text-indigo-600">
                                    {{ number_format($retrait->amount, 0, ',', ' ') }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">FCFA</span>
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Nom de la banque</dt>
                            <dd class="col-span-2">
                                <span class="text-sm font-bold text-gray-900">
                                    {{ $retrait->bank_name ?? 'Banque inconnue' }}
                                </span>
                            </dd>
                        </div>

                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Domiciliation</dt>
                            <dd class="col-span-2">
                                <span class="text-sm font-bold text-gray-900">
                                    {{ $retrait->user->country ?? 'Domiciliation inconnue' }}
                                </span>
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">RIB</dt>
                            <dd class="col-span-2">
                                <span class="font-mono text-sm text-gray-900 bg-gray-100 px-3 py-1.5 rounded">
                                    {{ $retrait->rib }}
                                </span>
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">IBAN</dt>
                            <dd class="col-span-2">
                                <span class="font-mono text-sm text-gray-900 bg-gray-100 px-3 py-1.5 rounded">
                                    {{ $retrait->iban ?? 'Non spécifié' }}
                                </span>
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Code BIC</dt>
                            <dd class="col-span-2 text-sm text-gray-900">
                                {{ $retrait->code_bic ?? 'Non spécifié' }}
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Clé IBAN</dt>
                            <dd class="col-span-2 text-sm text-gray-900">
                                {{ $retrait->cle_iban ?? 'Non spécifié' }}
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Code banque</dt>
                            <dd class="col-span-2 text-sm text-gray-900">
                                {{ $retrait->code_bank ?? 'Non spécifié' }}
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Code guichet</dt>
                            <dd class="col-span-2 text-sm text-gray-900">
                                {{ $retrait->code_guiche ?? 'Non spécifié' }}
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Numéro de compte</dt>
                            <dd class="col-span-2 text-sm text-gray-900">
                                {{ $retrait->numero_compte ?? 'Non spécifié' }}
                            </dd>
                        </div>
        
                        <div class="grid grid-cols-3 px-8 py-5 hover:bg-white/80 transition-colors duration-200">
                            <dt class="text-sm font-semibold text-gray-600">Statut actuel</dt>
                            <dd class="col-span-2">
                                @if ($retrait->status === 'En cours')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                                        </svg>
                                        En attente
                                    </span>
                                @elseif($retrait->status === 'Accepté')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Accepté
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Refusé
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
        
                {{-- Section des codes --}}
                @if ($code1 || $code2)
                    <div class="p-6 bg-gray-50 border-t border-gray-100">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Code initiateur</label>
                                <input type="text" wire:model="codeRequest1"
                                    class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="Entrez code 1" />
                                @error('codeRequest1')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Code utilisateur joint</label>
                                <input type="text" wire:model="codeRequest2"
                                    class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="Entrez code 2" />
                                @error('codeRequest2')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif
        
                {{-- Section des actions --}}
                <div class="px-8 py-6 bg-white border-t border-gray-100">
                    <div class="flex justify-end gap-4">
                        @if ($retrait->status !== 'En cours')
                            <div class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg">
                                Réponse envoyée
                            </div>
                        @else
                            <button wire:click="rejectRetrait"
                                class="inline-flex items-center px-6 py-3 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Refuser
                            </button>
                            <button wire:click="acceptRetrait"
                                class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg shadow-green-500/25">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Accepter
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
