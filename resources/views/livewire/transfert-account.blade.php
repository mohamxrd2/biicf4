<div class="flex justify-center mt-4">
    {{-- In work, do what you enjoy. --}}

    <form wire:submit.prevent="submitTransfert" class="w-full md:w-1/2 bg-white border border-gray-300 rounded-xl p-4">
        @if (session()->has('successMessage'))
            <div class="bg-green-400 text-white p-4 rounded-md mt-2 mb-6">
                {{ session('successMessage') }}
            </div>
        @endif

        @if (session()->has('errorMessage'))
            <div class="bg-red-400 text-white p-4 rounded-md mt-2 mb-6">
                {{ session('errorMessage') }}
            </div>
        @endif
        <h2 class="text-center font-semibold text-xl">Transfert entre compte</h2>

        <div class="mt-4">

            <input type="number" id="amount" wire:model="amount" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Entrez le montant" required>
            @error('amount')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>
        <div class="mt-4">
            <select name="" id="" wire:model="account1" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="" disabled>Choisissez le compte de prélevement</option>
                <option value="COC">COC</option>
                <option value="COI">COI</option>
                <option value="CEDD">CEDD</option>
                <option value="CFA">CFA</option>
                <option value="CEFP">CEFP</option>

            </select>
            @error('account1')
                <span class="text-red-500">{{ $message }}</span>
            @enderror


        </div>

        <div class="mt-4">
            <select name="" id="" wire:model="account2" required
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="" disabled>Choisissez le compte de reception</option>
                <option value="COC">COC</option>
                <option value="COI">COI</option>
                <option value="CEDD">CEDD</option>
                <option value="CFA">CFA</option>
                <option value="CEFP">CEFP</option>

            </select>
            @error('account2')
                <span class="text-red-500">{{ $message }}</span>
            @enderror


        </div>

        <div class="mt-6 relative">
            <button type="submit"
                class="w-full py-3 bg-purple-600 hover:bg-purple-700 transition-colors rounded-md text-white font-medium"
                wire:loading.attr="disabled">
                Soumettre
            </button>

            <!-- Texte de chargement -->
            <div wire:loading wire:target="submitDeposit"
                class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-75 rounded-md">
                <span class="text-gray-700 font-semibold">Traitement en cours...</span>
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
