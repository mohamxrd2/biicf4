
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
    @if (session()->has('success'))
        <div class="mt-4 text-green-500">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 text-red-500">
            {{ session('error') }}
        </div>
    @endif
    <div class="space-y-6">
        <div class="border-b pb-4">
            <h2 class="text-2xl font-semibold text-gray-800">
                Demande de retrait
            </h2>
            <p class="text-gray-600 mt-2">
                {{ $demandeur->name }} vous a fait une demande de retrait
            </p>
        </div>

        <div class="text-center p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Montant du retrait</h3>
            <p class="text-4xl font-bold text-blue-700">{{ $amount }} CFA</p>
        </div>

        @if ($code1 || $code2 )

        <div class="flex">
            <div class="flex-1 px-4">
                <label class="block text-sm font-medium text-gray-700">Code initiateur</label>
                <input type="text" wire:model="codeRequest1" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Entrez code 1"  />
                @error('codeRequest1')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span> <!-- Affiche le message d'erreur -->
                @enderror
            </div>
            <div class="flex-1 px-4">
                <label class="block text-sm font-medium text-gray-700">Code utilisateur joint</label>
                <input type="text" wire:model="codeRequest2" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Entrez code 2"  />
                @error('codeRequest2')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span> <!-- Affiche le message d'erreur -->
                @enderror
            </div>
        </div>
        
            
        @endif

        <div class="flex justify-center pt-4">
            @if ($notification->reponse)
                <div class="p-2 bg-gray-300 border rounded-md ">
                    <p class="font-medium text-center text-md">Réponse envoyée</p>
                </div>
            @else
                <button wire:click='accepteRetrait'
                    class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md">


                    <span wire:loading.remove>
                        Accepter
                    </span>
                    <span wire:loading>
                        Chargement...
                        <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                        </svg>
                    </span>
                </button>

                <button wire:click='refusRetrait' class="flex p-2 font-medium text-white bg-red-700 rounded-md">

                    <span wire:loading.remove>
                        Refuser
                    </span>
                    <span wire:loading>
                        Chargement...
                        <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                        </svg>
                    </span>

                </button>
            @endif
        </div>
    </div>


</div>
