<div class="flex flex-col items-center p-6">
    @if (session()->has('message'))
        <div class="mb-4 bg-green-500 text-white p-2 rounded">
            {{ session('message') }}
        </div>
    @endif

    <!-- Formulaire de retrait -->
    <div class="mt-4 w-full max-w-sm" wire:loading.class="opacity-50">
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

    </div>
</div>
