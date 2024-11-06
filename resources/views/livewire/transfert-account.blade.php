<div class="flex justify-center mt-4">
    {{-- In work, do what you enjoy. --}}

    <form wire:submit.prevent="submitTransfert" class="w-full p-4 bg-white border border-gray-300 md:w-1/2 rounded-xl">
        @if (session()->has('successMessage'))
            <div class="p-4 mt-2 mb-6 text-white bg-green-400 rounded-md">
                {{ session('successMessage') }}
            </div>
        @endif

        @if (session()->has('errorMessage'))
            <div class="p-4 mt-2 mb-6 text-white bg-red-400 rounded-md">
                {{ session('errorMessage') }}
            </div>
        @endif
        <h2 class="text-xl font-semibold text-center">Transfert entre compte</h2>

        <div class="mt-4">

            <input type="number" id="amount" wire:model="amount" required
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Entrez le montant" required>
            @error('amount')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>
        <div class="mt-4">
            <select name="" id="" wire:model="account1" required
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="" disabled>Choisissez le compte de prélevement</option>
                <option value="COC">COC</option>
                <option value="COI">COI</option>
                <option value="CEDD">CEDD</option>
                <option value="CEFP">CEFP</option>

            </select>
            @error('account1')
                <span class="text-red-500">{{ $message }}</span>
            @enderror


        </div>

        <div class="mt-4">
            <select name="" id="" wire:model="account2" required
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="" disabled>Choisissez le compte de reception</option>
                <option value="COC">COC</option>
                <option value="COI">COI</option>
                <option value="CEDD">CEDD</option>
                <option value="CEFP">CEFP</option>

            </select>
            @error('account2')
                <span class="text-red-500">{{ $message }}</span>
            @enderror


        </div>

        <div class="relative mt-6">
            <button type="submit"
                class="w-full py-3 font-medium text-white transition-colors bg-purple-600 rounded-md hover:bg-purple-700"
                wire:loading.attr="disabled">
                Soumettre
            </button>

            <!-- Texte de chargement -->
            <div wire:loading wire:target="submitDeposit"
                class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75 rounded-md">
                <span class="font-semibold text-gray-700">Traitement en cours...</span>
            </div>
        </div>

    </form>
    <script>
        document.addEventListener('livewire:load', function () {
        Livewire.on('reloadPage', function () {
            location.reload(); // Recharger la page
        });
    });
    </script>
</div>
