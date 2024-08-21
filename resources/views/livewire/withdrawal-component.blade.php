<div class="flex flex-col items-center p-6">
    @if (session()->has('message'))
        <div class="mb-4 bg-green-500 text-white p-2 rounded">
            {{ session('message') }}
        </div>
    @endif
    <button wire:click="showForm" class="px-4 py-2 flex items-center bg-red-600 text-white rounded-lg">
        <svg class="w-4 h-4 text-white font-bold mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 4.5 15 15m0 0V8.25m0 11.25H8.25" />
        </svg>
        <p>Retrait</p>
    </button>
    <!-- Formulaire de retrait -->
    <div class="mt-4 w-full max-w-sm" wire:loading.class="opacity-50">
        @if ($formVisible)
            <form wire:submit.prevent="initiateWithdrawal" class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Montant à retirer :</label>
                    <input type="number" id="amount" wire:model="amount" min="1"
                        class="shadow appearance-none border rounded w-full py-2 px-3 mb-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('amount')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="psap" class="block text-gray-700 text-sm font-bold mb-2">ID ou Username du PSAP
                        :</label>
                    <input type="text" id="psap" wire:model="psap"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('psap')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-red-600 text-white font-bold py-2 px-4 rounded hover:bg-red-700 focus:outline-none focus:shadow-outline">
                    Confirmer Retrait
                </button>



                @if (session()->has('error'))
                    <div class="mt-4 bg-red-500 text-white p-2 rounded">
                        {{ session('error') }}
                    </div>
                @endif
            </form>

        @endif
    </div>
</div>
