<div class="flex justify-center mt-4">


    <!-- Formulaire de retrait -->

    <form wire:submit.prevent="initiateWithdrawal" wire:loading.class="opacity-50"
        class="w-full p-4 bg-white border border-gray-300 md:w-1/2 rounded-xl">

        @if (session()->has('message'))
            <div class="p-2 mb-4 text-white bg-green-500 rounded">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-2 mt-4 text-white bg-red-500 rounded">
                {{ session('error') }}
            </div>
        @endif
        <h2 class="mb-4 text-xl font-semibold text-center">Faire un retrait</h2>
        <div class="mb-4">

            <input type="number" id="amount" wire:model="amount" min="1"
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Motant à retirer">
            @error('amount')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">

            <input type="text" id="psap" wire:model="psap"
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="ID ou Username du PSAP">
            @error('psap')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>


        <div class="relative mt-6">
            <button type="submit"
                class="w-full py-3 font-medium text-white transition-colors bg-purple-600 rounded-md hover:bg-purple-700"
                wire:loading.attr="disabled">
                Confirmer Retrait
            </button>

            <!-- Texte de chargement -->
            <div wire:loading wire:target="submitDeposit"
                class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75 rounded-md">
                <span class="font-semibold text-gray-700">Traitement en cours...</span>
            </div>
        </div>

    </form>


</div>
