<div class="min-h-screen " wire:poll.1500ms>
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-5xl mx-auto">
            {{-- Card principale --}}

            @if (session()->has('message'))
                <div class="p-2 mb-4 text-white bg-green-500 rounded">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="p-2 mb-4 text-white bg-red-500 rounded">
                    {{ session('error') }}
                </div>
            @endif
            <div class="bg-white rounded-2xl shadow-xl shadow-blue-500/5 overflow-hidden border border-gray-100">
                {{-- En-tête --}}
                <div class="relative overflow-hidden px-6 pt-8 pb-6">
                    <div
                        class="absolute top-0 right-0 -mt-6 -mr-6 h-24 w-24 rounded-full bg-gradient-to-br from-blue-500/10 to-purple-500/10 blur-2xl">
                    </div>
                    <div class="relative">
                        <h2 class="text-2xl font-bold text-gray-900">
                            Détails de la demande de retrait
                            <div class="mt-1 text-sm font-normal text-gray-500">
                                Référence: {{ $retrait->reference }}
                            </div>
                        </h2>
                    </div>
                </div>

                {{-- Informations détaillées --}}
                <div class="border-t border-gray-100 bg-gray-50/50">
                    <dl class="divide-y divide-gray-100">
                        <div class="grid grid-cols-3 px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                            <dt class="text-sm font-medium text-gray-600">Nom de l'utilisateur</dt>
                            <dd class="col-span-2 text-sm text-gray-900 font-medium">
                                {{ $retrait->user->name ?? 'Utilisateur inconnu' }}
                            </dd>
                        </div>

                        <div class="grid grid-cols-3 px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                            <dt class="text-sm font-medium text-gray-600">Montant demandé</dt>
                            <dd class="col-span-2">
                                <span class="text-sm font-bold text-gray-900">
                                    {{ number_format($retrait->amount, 0, ',', ' ') }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">FCFA</span>
                            </dd>
                        </div>

                        <div class="grid grid-cols-3 px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                            <dt class="text-sm font-medium text-gray-600">RIB</dt>
                            <dd class="col-span-2">
                                <span class="font-mono text-sm text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $retrait->rib }}
                                </span>
                            </dd>
                        </div>

                        <div class="grid grid-cols-3 px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                            <dt class="text-sm font-medium text-gray-600">Statut actuel</dt>
                            <dd class="col-span-2">
                                @if ($retrait->status === 'En cours')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                                        </svg>
                                        En attente
                                    </span>
                                @elseif($retrait->status === 'Accepté')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Accepté
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Refusé
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
                @if ($code1 || $code2)
                    <div class="flex">
                        <div class="flex-1 px-4">
                            <label class="block text-sm font-medium text-gray-700">Code initiateur</label>
                            <input type="text" wire:model="codeRequest1"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Entrez code 1" />
                            @error('codeRequest1')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                <!-- Affiche le message d'erreur -->
                            @enderror
                        </div>
                        <div class="flex-1 px-4">
                            <label class="block text-sm font-medium text-gray-700">Code utilisateur joint</label>
                            <input type="text" wire:model="codeRequest2"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Entrez code 2" />
                            @error('codeRequest2')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                <!-- Affiche le message d'erreur -->
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                @if ($retrait->status !== 'En cours')
                    <div class="px-6 py-4 bg-white border-t border-gray-100">
                        <div class="flex justify-end gap-3">
                            <div
                                class="inline-flex items-center justify-center border px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">

                                Reponse envoyé
                            </div>

                        </div>
                    </div>
                @else
                    <div class="px-6 py-4 bg-white border-t border-gray-100">
                        <div class="flex justify-end gap-3">
                            <button wire:click="rejectRetrait"
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                Refuser
                            </button>
                            <button wire:click="acceptRetrait"
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white  bg-green-600 hover:bg-green-700  rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg shadow-blue-500/25">
                                <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Accepter
                            </button>
                        </div>
                    </div>
                @endif

            </div>


        </div>
    </div>
</div>
