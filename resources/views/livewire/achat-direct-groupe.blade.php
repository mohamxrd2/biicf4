<div>
    @if (session('success'))
        <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mt-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- Afficher les messages d'erreur -->
    @if (session('error'))
        <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mt-3">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="AchatDirectForm" id="formAchatDirect"
        class="mt-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md" style="display: none;" method="POST">
        @csrf
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
            

            @if (!empty($produit->specification))
                <div class="block">
                    <input type="radio" id="specificite_1" name="specificite" value="{{ $produit->specification }}"
                        wire:model.defer="selectedSpec"
                        class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500">
                    <label for="specificite_1" class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $produit->specification }}
                    </label>
                </div>
            @endif

            @if (!empty($produit->specification2))
                <div class="block">
                    <input type="radio" id="specificite_2" name="specificite" value="{{ $produit->specification2 }}"
                        wire:model.defer="selectedSpec"
                        class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500">
                    <label for="specificite_2" class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $produit->specification2 }}
                    </label>
                </div>
            @endif

            @if (!empty($produit->specification3))
                <div class="block">
                    <input type="radio" id="specificite_3" name="specificite" value="{{ $produit->specification3 }}"
                        wire:model.defer="selectedSpec"
                        class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500">
                    <label for="specificite_3" class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $produit->specification3 }}
                    </label>
                </div>
            @endif



            <select wire:model="selectedOption" name="type"
                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                <option selected>Type de livraison</option>
                @foreach ($options as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>



        <input type="hidden" name="userTrader" wire:model.defer="userTrader">
        <input type="hidden" name="nameProd" wire:model.defer="nameProd">
        <input type="hidden" name="userSender" wire:model.defer="userSender">
        <input type="hidden" name="message" wire:model.defer="message">
        <input type="hidden" name="photoProd" wire:model.defer="photoProd">
        <input type="hidden" name="idProd" wire:model.defer="idProd">
        <input type="hidden" name="prix" wire:model.defer="prix">

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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAchatDirect = document.getElementById('btnAchatDirect');
        const formAchatDirect = document.getElementById('formAchatDirect');

        btnAchatDirect.addEventListener('click', function() {
            if (formAchatDirect.style.display === 'none' || formAchatDirect.style.display === '') {
                formAchatDirect.style.display = 'block';
            } else {
                formAchatDirect.style.display = 'none';
            }
        });
    });

    function toggleVisibility() {
        const contentDiv = document.getElementById('toggleContent');

        if (contentDiv.classList.contains('hidden')) {
            contentDiv.classList.remove('hidden');
            // Forcing reflow to enable transition
            contentDiv.offsetHeight;
            contentDiv.classList.add('show');
        } else {
            contentDiv.classList.remove('show');
            contentDiv.addEventListener('transitionend', () => {
                contentDiv.classList.add('hidden');
            }, {
                once: true
            });
        }
    }

    // Fonction pour mettre à jour le montant total pour l'achat direct
    function updateMontantTotalDirect() {
        const quantityInput = document.getElementById('quantityInput');
        const price = parseFloat(document.querySelector('[data-price]').getAttribute('data-price'));
        const minQuantity = parseInt(quantityInput.getAttribute('data-min'));
        const maxQuantity = parseInt(quantityInput.getAttribute('data-max'));
        const quantity = parseInt(quantityInput.value);
        const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
        const montantTotalElement = document.getElementById('montantTotal');
        const errorMessageElement = document.getElementById('errorMessage');
        const submitButton = document.getElementById('submitButton');
        const montantTotalInput = document.getElementById('montant_total_input');

        const userBalance = {{ $userWallet->balance }};

        if (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity) {
            errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = '0 FCFA';
            submitButton.disabled = true;
        } else if (montantTotal > userBalance) {
            errorMessageElement.innerText =
                `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            submitButton.disabled = true;
        } else {
            errorMessageElement.classList.add('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input
            submitButton.disabled = false;
        }
    }
</script>
