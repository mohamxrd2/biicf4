<div class="flex justify-center mt-4">
    @if (session()->has('message'))
        <div class="mb-4 bg-green-500 text-white p-2 rounded">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mt-4 bg-red-500 text-white p-2 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulaire de retrait -->

        <form wire:submit.prevent="initiateWithdrawal" wire:loading.class="opacity-50" class="w-full md:w-1/2 bg-white border border-gray-300 rounded-xl p-4">
            <h2 class="text-center font-semibold text-xl mb-4">Faire un retrait</h2>
            <div class="mb-4">
              
                <input type="number" id="amount" wire:model="amount" min="1"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    placeholder="Motant à retirer">
                @error('amount')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">

                <input type="text" id="psap" wire:model="psap"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="ID ou Username du PSAP">
                @error('psap')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>


            <div class="mt-6 relative">
                <button type="submit"
                    class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium"
                    wire:loading.attr="disabled">
                    Confirmer Retrait
                </button>
    
                <!-- Texte de chargement -->
                <div wire:loading wire:target="submitDeposit"
                    class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75 rounded-md">
                    <span class="text-gray-700 font-semibold">Traitement en cours...</span>
                </div>
            </div>

        </form>

    
</div>
