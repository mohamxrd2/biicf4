<div>
    <form wire:submit.prevent="submitForm" id="formAchatDirect"
        class="mt-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md" style="display: none;" method="POST">
        @csrf
        @method('POST')
        <h1 class="text-xl text-center mb-3">Achat direct</h1>

        <div class="space-y-3 mb-3 w-full">
            <input type="number" id="quantityInput" name="quantité" wire:model.defer="quantité"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Quantité" data-min="{{ $produit->qteProd_min }}" data-max="{{ $produit->qteProd_max }}"
                oninput="updateMontantTotalDirect()" required>
        </div>

        <div class="space-y-3 mb-3 w-full">
            <input type="text" id="locationInput" name="localite" wire:model.defer="localite"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Lieu de livraison" required>
        </div>

        <div class="space-y-3 mb-3 w-full">
            <input type="text" id="specificite" name="specificite" wire:model.defer="specificite"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Specificité (Facultatif)">
        </div>

        <input type="hidden" name="userTrader" wire:model.defer="userTrader" value="{{ $produit->user->id }}">
        <input type="hidden" name="nameProd" wire:model.defer="nameProd" value="{{ $produit->name }}">
        <input type="hidden" name="userSender" wire:model.defer="userSender" value="{{ $userId }}">
        <input type="hidden" name="message" wire:model.defer="message" value="Un utilisateur veut acheter ce produit">
        <input type="hidden" name="photoProd" wire:model.defer="photoProd" value="{{ $produit->photoProd1 }}">
        <input type="hidden" name="idProd" wire:model.defer="idProd" value="{{ $produit->id }}">
        <input type="hidden" name="prix" wire:model.defer="prix" value="{{ $produit->prix }}">

        <div class="flex justify-between px-4 mb-3 w-full">
            <p class="font-semibold text-sm text-gray-500">Prix total:</p>
            <p class="text-sm text-purple-600" id="montantTotal">0 FCFA</p>
            <input type="hidden" name="montantTotal" id="montant_total_input">
        </div>

        <p id="errorMessage" class="text-sm text-center text-red-500 hidden">Erreur</p>

        <div class="w-full text-center mt-3">
            <button type="reset"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-200 text-black hover:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none">Annulé</button>
            <button type="submit" id="submitButton"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                wire:loading.attr="disabled" disabled>

                <span wire:loading.remove>Envoyer</span>
                <span wire:loading>Envoi en cours...</span>
            </button>
        </div>
    </form>
</div>
