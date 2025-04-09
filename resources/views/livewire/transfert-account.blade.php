<div class="flex justify-center mt-4">
    {{-- In work, do what you enjoy. --}}

    <form wire:submit.prevent="submitTransfert" class="p-4 w-full bg-white rounded-xl border border-gray-300 md:w-1/2">
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
                class="block mt-1 w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                placeholder="Entrez le montant" required>
            @error('amount')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>
        <div class="mt-4">
            <select name="" id="" wire:model="account1" required
                class="block mt-1 w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="" disabled>Choisissez le compte de prélevement</option>
                <option value="COC">COC</option>
                <option value="COI">COI</option>
                <option value="CEDD">CEDD</option>
                <option value="CEFP">CEFP</option>
                <option value="CFA">CFA</option>

            </select>
            @error('account1')
                <span class="text-red-500">{{ $message }}</span>
            @enderror


        </div>

        <div class="mt-4">
            <select name="" id="" wire:model="account2" required
                class="block mt-1 w-full rounded-md border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="" disabled>Choisissez le compte de reception</option>
                <option value="COC">COC</option>
                <option value="COI">COI</option>
                <option value="CEDD">CEDD</option>
                <option value="CEFP">CEFP</option>
                <option value="CFA">CFA</option>

            </select>
            @error('account2')
                <span class="text-red-500">{{ $message }}</span>
            @enderror


        </div>

        <div class="relative mt-6">
            <button type="submit"
                class="py-3 w-full font-medium text-white bg-purple-600 rounded-md transition-colors hover:bg-purple-700"
                wire:loading.attr="disabled">
                Soumettre
            </button>

            <!-- Texte de chargement -->
            <div wire:loading wire:target="submitDeposit"
                class="flex absolute inset-0 justify-center items-center bg-gray-100 bg-opacity-75 rounded-md">
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
