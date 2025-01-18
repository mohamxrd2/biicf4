<div>
    <div class="bg-white rounded-lg p-6 shadow-md border-b">
        <div class="flex items-center justify-between">
            <!-- Stepper -->
            <div class="w-full flex items-center">
                <!-- Step 1 -->
                <div class="relative flex flex-col items-center flex-1">
                    <div
                        class="w-12 h-12 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold shadow-md transform transition duration-300 step-active hover:scale-110">
                        1
                    </div>
                    <p class="text-sm font-medium text-gray-500 mt-2">Détails du produit</p>
                </div>

                <!-- Line -->
                <div class="flex-1 h-1 bg-blue-300 transition duration-300 step-line"></div>

                <!-- Step 2 -->
                <div class="relative flex flex-col items-center flex-1">
                    <div
                        class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow-md transform transition duration-300 hover:scale-110">
                        2
                    </div>
                    <p class="text-sm font-medium text-gray-800 mt-2">Commande</p>
                </div>
            </div>
        </div>
    </div>
    <form wire:submit.prevent="AchatDirectForm" id="formAchatDirect">
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-800 dark:bg-gray-800 dark:text-red-400">
                <span class="font-medium text-white">{{ session('error') }}</span>
            </div>
        @endif
        <div x-data="{ selectedOption: @entangle('selectedOption'), quantité: 1, localite: '' }">

            <div class="relative md:static p-4 bg-white rounded-lg shadow-lg">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Champ de quantité -->
                    <div class="mb-4">
                        <h2 class="text-lg font-bold mb-2">Quantité du produit/service</h2>
                        <input type="number" wire:model.live.500ms="quantité" x-model="quantité" name="quantité"
                            class="w-full p-2 text-center border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required />

                    </div>

                    <!-- Champ de localisation -->
                    <div>
                        <h2 class="text-lg font-bold mb-2">Adresse de reception</h2>
                        <input id="location" type="text" wire:model="localite" x-model="localite"
                            placeholder="Entrez votre localisation"
                            class="w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required />
                    </div>
                </div>

                @if ($errorMessage)
                    <span class="text-sm text-red-500">{{ $errorMessage }}</span>
                @endif

                <!-- Résumé -->
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border">
                    <h4 class="font-semibold text-gray-800">Résumé :</h4>
                    <p class="text-gray-700">
                        Prix unitaire(apres négociation) : <span
                            class="font-bold">{{ $notification->data['prixTrade'] }} FCFA / {{ $produit->duree }}</span>
                        </span>
                    </p>
                    @if ($type == 'Service')
                        Vous avez commander <span class="font-bold" x-text="quantité"></span> fois
                        {{ $produit->duree }} de {{ $produit->name }} de {{ $produit->specialite }}.
                    @else
                        <p class="text-gray-700">
                            Vous avez sélectionné <span class="font-bold" x-text="quantité"></span>
                            {{ $produit->condProd }}(s) de {{ $produit->name }}.
                        </p>
                    @endif
                    <p class="text-gray-700">
                        Localisation : <span class="font-bold" x-text="localite || 'Non renseignée'"></span>
                    </p>

                </div>



                <!-- Mode de réception -->
                <div class="mb-4">
                    <h2 class="text-lg font-bold mb-2">Mode de réception</h2>
                    @if ($type == 'Produit')
                        <x-option-selector label="Livraison à domicile" value="Delivery"
                            description="Livré chez vous après négociation des livreurs" cost="Prix apres confirmation"
                            :selectedOption="$selectedOption" />
                        <x-option-selector label="Retrait en magasin" value="Take Away"
                            description="Disponible après réception de confirmation du fournisseur" cost="Gratuit"
                            :selectedOption="$selectedOption" />
                    @else
                        <x-option-selector label="Retrait en magasin" value="Take Away"
                            description="Disponible après réception de confirmation du fournisseur" cost="Gratuit"
                            :selectedOption="$selectedOption" />
                    @endif
                </div>

                <div class="flex flex-col space-y-4">
                    <div class="grid grid-cols-2 gap-4">

                        @if ($type == 'Service')
                            <div x-show="selectedOption === 'Take Away'"class="col-span-2 grid grid-cols-2 gap-6 mt-4">
                                <x-time-picker-form title="Choisir l'horaire de debut" dateId="datePickerStart"
                                    timeId="timePickerStart" periodId="dayPeriod" dateModel="dateTot"
                                    timeModel="timeStart" periodModel="dayPeriod" dateLabel="Date" />

                                <x-time-picker-form title="Choisir l'horaire de fin" dateId="datePickerEnd"
                                    timeId="timePickerEnd" periodId="dayPeriodFin" dateModel="dateTard"
                                    timeModel="timeEnd" periodModel="dayPeriodFin" dateLabel="Date de retrait" />
                            </div>
                        @else
                            <div x-show="selectedOption === 'Take Away'">
                                <x-time-picker-form title="Choisir la période de retrait" dateId="datePickerStart"
                                    timeId="timePickerStart" periodId="dayPeriod" dateModel="dateTot"
                                    timeModel="timeStart" periodModel="dayPeriod" dateLabel="Date" />
                            </div>
                        @endif
                        @error('time')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror

                        @error('selectedOption')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror

                    </div>


                    <div class="text-center mt-3">
                        <button wire:click="credit" wire:loading.attr="disabled"
                            class="py-2 px-3 w-full inline-flex items-center
                            justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent
                            bg-blue-600 text-white hover:bg-blue-700"
                            :class="{ 'hidden': !$wire.isButtonHidden }">
                            <span wire:loading.remove>Demander un crédit</span>
                            <span wire:loading>Envoi en cours...</span>
                        </button>
                    </div>

                    <div class="flow-root">
                        <div class="-my-3 divide-y divide-gray-200 dark:divide-gray-800">
                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Montant de
                                    l'achat
                                </dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ $totalCost ?? 0 }}
                                    FCFA</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">TVA</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">0%</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-bold text-gray-900 dark:text-white">Montant Total</dt>
                                <dd class="text-base font-bold text-purple-600 dark:text-white" id="montantTotal">
                                    {{ $totalCost ?? 0 }} FCFA
                                </dd>
                                <input type="hidden" name="montantTotal" id="montant_total_input">

                            </dl>
                        </div>
                    </div>
                    <p class="bg-gray-100 sm:col-span-2 w-full text-gray-700 p-4 rounded-md shadow-md">
                        En soumettant ce formulaire, je certifie que les informations fournies sont exactes et
                        complètes.
                        J'autorise la plateforme à effectuer toutes les vérifications nécessaires concernant ces
                        informations.
                    </p>
                    <div class="text-center mt-3">

                        <button type="submit"
                            class="py-2 px-3 w-full inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700"
                            wire:loading.attr="disabled" :disabled="$wire.isButtonDisabled">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>

                            <span wire:loading.remove>Procéder au payement</span>
                            <span wire:loading>Payement en cours...</span>
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function togglePeriodSelect() {
        const timeInput = document.getElementById('timePickerStart');
        const periodSelect = document.getElementById('dayPeriod');
        const periodSelect2 = document.getElementById('dayPeriodFin');
        periodSelect.disabled = timeInput.value !== ""; // Désactiver la période si l'heure est remplie
        periodSelect2.disabled = timeInput.value !== ""; // Désactiver la période si l'heure est remplie
    }

    function togglePeriodSelect2() {
        const timeInput = document.getElementById('timePickerEnd');
        const periodSelect = document.getElementById('dayPeriodFin');
        const periodSelect2 = document.getElementById('dayPeriod');
        periodSelect.disabled = timeInput.value !== ""; // Désactiver la période si l'heure est remplie
        periodSelect2.disabled = timeInput.value !== ""; // Désactiver la période si l'heure est remplie
    }

    function toggleTimeInput2() {
        const timeInput = document.getElementById('timePickerEnd');
        const timeInput2 = document.getElementById('timePickerStart');
        const periodSelect = document.getElementById('dayPeriodFin');
        timeInput.disabled = periodSelect.value !== ""; // Désactiver l'heure si la période est sélectionnée
        timeInput2.disabled = periodSelect.value !== ""; // Désactiver l'heure si la période est sélectionnée
    }

    function toggleTimeInput() {
        const timeInput = document.getElementById('timePickerStart');
        const timeInput2 = document.getElementById('timePickerEnd');
        const periodSelect = document.getElementById('dayPeriod');
        timeInput.disabled = periodSelect.value !== ""; // Désactiver l'heure si la période est sélectionnée
        timeInput2.disabled = periodSelect.value !== ""; // Désactiver l'heure si la période est sélectionnée
    }
</script>
<script>
    function toggleVisibility() {
        const contentDiv = document.getElementById('toggleContent');

        if (contentDiv.classList.contains('hidden')) {
            contentDiv.classList.remove('hidden');
            // Forcing reflow to enable   transition
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
        const price = parseFloat({{ $notification->data['prixTrade'] }}); // Récupérer le prix depuis PHP
        const minQuantity = parseInt(quantityInput.getAttribute('data-min'));
        const maxQuantity = parseInt(quantityInput.getAttribute('data-max'));
        const quantity = parseInt(quantityInput.value);
        const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
        const montantTotalElement = document.getElementById('montantTotal');
        const errorMessageElement = document.getElementById('errorMessage');
        const submitButton = document.getElementById('submitButton');
        const montantTotalInput = document.getElementById('montant_total_input');
        const requestCreditButton = document.getElementById('requestCreditButton');



        const userBalance = {{ $userWallet->balance }};

        if (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity) {
            errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = '0 FCFA';
            submitButton.disabled = true;
            montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input

            requestCreditButton.classList.add('hidden'); // Masquer le bouton de crédit si autre erreur

        } else if (montantTotal > userBalance) {
            errorMessageElement.innerText =
                `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            submitButton.disabled = true;
            montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input
            requestCreditButton.classList.remove('hidden'); // Afficher le bouton pour demander un crédit

        } else {
            errorMessageElement.classList.add('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input
            submitButton.disabled = false;
            requestCreditButton.classList.add('hidden'); // Masquer le bouton si tout est correct

        }



    }
</script>
