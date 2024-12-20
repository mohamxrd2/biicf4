<div>

    <form wire:submit.prevent="AchatDirectForm" id="formAchatDirect">
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-800 dark:bg-gray-800 dark:text-red-400">
                <span class="font-medium text-white">{{ session('error') }}</span>
            </div>
        @endif
        <div x-data="{ selectedOption: @entangle('selectedOption'), quantité: 1, localite: '' }">

            <div class="relative md:static p-4 bg-white rounded-lg shadow-lg">
                <ol
                    class="items-center flex w-full max-w-2xl text-center text-sm font-medium text-gray-500 dark:text-gray-400 sm:text-base mb-5 p-5">
                    <li
                        class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                        <span
                            class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                            <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Details
                        </span>
                    </li>

                    <li
                        class="after:border-1 flex items-center text-blue-600 text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                        <span
                            class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                            <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Achat
                        </span>
                    </li>
                </ol>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Champ de quantité -->
                    <div class="mb-4">
                        <h2 class="text-lg font-bold mb-2">Quantité du produit</h2>
                        <input id="quantityInput" type="number" wire:model="quantité" x-model="quantité"
                            name="quantité"
                            class="w-full p-2 text-center border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            data-min="{{ $produit->qteProd_min }}" data-max="{{ $produit->qteProd_max }}"
                            oninput="updateMontantTotalDirect()" required />
                    </div>

                    <!-- Champ de localisation -->
                    <div>
                        <h2 class="text-lg font-bold mb-2">Adresse de livraison</h2>
                        <input id="location" type="text" wire:model="localite" x-model="localite" oninput="updateMontantTotalDirect()"
                            placeholder="Entrez votre localisation"
                            class="w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required />
                    </div>
                </div>


                <!-- Résumé -->
                <div class="my-4 p-4 bg-blue-50 rounded-lg border">
                    <h4 class="font-semibold text-gray-800">Résumé :</h4>
                    <p class="text-gray-700">
                        Vous avez sélectionné <span class="font-bold" x-text="quantité"></span>
                        {{ $produit->condProd }}(s).
                    </p>
                    <p class="text-gray-700">
                        Localisation : <span class="font-bold" x-text="localite || 'Non renseignée'"></span>
                    </p>
                </div>

                <!-- Mode de réception -->
                <div class="mb-4" oninput="updateMontantTotalDirect()">
                    <h2 class="text-lg font-bold mb-2">Mode de réception</h2>

                    <!-- Option: Livraison à domicile -->
                    <input type="radio" name="selectedOption" value="Delivery"
                        @click="selectedOption = 'Delivery'"
                        id="deliveryOption" class="hidden">
                    <label for="deliveryOption" class="flex items-center p-4 rounded-lg border-2 transition-all w-full mb-4"
                        :class="{
                            'border-blue-500 bg-blue-50': selectedOption === 'Delivery',
                            'border-gray-200 hover:border-blue-200': selectedOption !== 'Delivery'
                        }">
                        <div class="p-3 rounded-full mr-4"
                            :class="{
                                'bg-blue-500 text-white': selectedOption === 'Delivery',
                                'bg-gray-100 text-gray-600': selectedOption !== 'Delivery'
                            }">
                            <!-- Icône de livraison -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="1" y="3" width="15" height="13" rx="2" ry="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></rect>
                                <path d="M16 8h5l3 5v3h-8V8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <circle cx="5.5" cy="18.5" r="2.5" stroke-width="2"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5" stroke-width="2"></circle>
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h3 class="font-semibold text-gray-800">Livraison à domicile</h3>
                            <p class="text-sm text-gray-500">Livré chez vous après négociation des livreurs</p>
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-blue-800">Prix apres confirmation</span>
                        </div>
                    </label>

                    <!-- Option: Retrait en magasin -->
                    <input type="radio" name="selectedOption" value="Take Away"
                        @click="selectedOption = 'Take Away'"
                        id="takeAwayOption" class="hidden">
                    <label for="takeAwayOption" class="flex items-center p-4 rounded-lg border-2 transition-all w-full"
                        :class="{
                            'border-blue-500 bg-blue-50': selectedOption === 'Take Away',
                            'border-gray-200 hover:border-blue-200': selectedOption !== 'Take Away'
                        }">
                        <div class="p-3 rounded-full mr-4"
                            :class="{
                                'bg-blue-500 text-white': selectedOption === 'Take Away',
                                'bg-gray-100 text-gray-600': selectedOption !== 'Take Away'
                            }">
                            <!-- Icône de retrait -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h3 class="font-semibold text-gray-800">Retrait en magasin</h3>
                            <p class="text-sm text-gray-500">Disponible après réception de confirmation du fournisseur</p>
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-blue-800">Gratuit</span>
                        </div>
                    </label>
                </div>

                <div class="flex flex-col space-y-4">
                    <div class="grid grid-cols-2 gap-4">

                        @if ($type == 'Service')
                            <div x-show="selectedOption === 'Take Away'"class="col-span-2 grid grid-cols-2 gap-6 mt-4">
                                <div class="p-6 bg-gray-100 border rounded-lg shadow-sm max-w-md">
                                    <h2 class="text-lg font-bold mb-4">Choisir l'horaire de debut</h2>

                                    <!-- Date de retrait -->
                                    <div class="mb-4">
                                        <label for="pickup-date"
                                            class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                        <div class="relative">
                                            <input type="date" id="datePickerStart" name="dateTot"
                                                wire:model="dateTot"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <!-- Heure de retrait -->
                                    <div class="mb-4">
                                        <label for="pickup-time"
                                            class="block text-sm font-medium text-gray-700 mb-2">Heure de
                                            retrait</label>
                                        <div class="relative">
                                            <input type="time" id="timePickerStart" name="timeStart"
                                                wire:model="timeStart"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">

                                        </div>
                                    </div>

                                    <!-- Période -->
                                    <div>
                                        <label for="pickup-period"
                                            class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                                        <select id="dayPeriod" name="dayPeriod" wire:model="dayPeriod"
                                            class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                            <option value="" selected>Choisir la période</option>
                                            <option value="Matin">Matin</option>
                                            <option value="Après-midi">Après-midi</option>
                                            <option value="Soir">Soir</option>
                                            <option value="Nuit">Nuit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="p-6 bg-gray-100 border rounded-lg shadow-sm max-w-md">
                                    <h2 class="text-lg font-bold mb-4">Choisir l'horaire de fin</h2>

                                    <!-- Date de retrait -->
                                    <div class="mb-4">
                                        <label for="pickup-date"
                                            class="block text-sm font-medium text-gray-700 mb-2">Date de
                                            retrait</label>
                                        <div class="relative">
                                            <input type="date" id="datePickerEnd" name="dateTard"
                                                wire:model="dateTard"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <!-- Heure de retrait -->
                                    <div class="mb-4">
                                        <label for="pickup-time"
                                            class="block text-sm font-medium text-gray-700 mb-2">Heure de
                                            retrait</label>
                                        <div class="relative">
                                            <input type="time" id="timePickerEnd" name="timeEnd"
                                                wire:model="timeEnd"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <!-- Période -->
                                    <div>
                                        <label for="pickup-period"
                                            class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                                        <select id="dayPeriod" name="dayPeriod" wire:model="dayPeriod"
                                            class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                            <option value="" selected>Choisir la période</option>
                                            <option value="Matin">Matin</option>
                                            <option value="Après-midi">Après-midi</option>
                                            <option value="Soir">Soir</option>
                                            <option value="Nuit">Nuit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div x-show="selectedOption === 'Take Away'"class="col-span-2 grid grid-cols-2 gap-6 mt-4">
                                <div class="p-6 bg-gray-100 border rounded-lg shadow-sm max-w-md">
                                    <h2 class="text-lg font-bold mb-4">Choisir l'horaire de debut</h2>

                                    <!-- Date de retrait -->
                                    <div class="mb-4">
                                        <label for="pickup-date"
                                            class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                        <div class="relative">
                                            <input type="date" id="datePickerStart" name="dateTot"
                                                min="{{ now()->addDay()->toDateString() }}" wire:model="dateTot"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <!-- Heure de retrait -->
                                    <div class="mb-4">
                                        <label for="pickup-time"
                                            class="block text-sm font-medium text-gray-700 mb-2">Heure de
                                            retrait</label>
                                        <div class="relative">
                                            <input type="time" id="timePickerStart" name="timeStart"
                                                wire:model="timeStart"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                oninput="togglePeriodSelect()">
                                        </div>
                                    </div>

                                    <!-- Période -->
                                    <div>
                                        <label for="pickup-period"
                                            class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                                        <select id="dayPeriod" name="dayPeriod" wire:model="dayPeriod"
                                            class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none"
                                            onchange="toggleTimeInput()">
                                            <option value="" selected>Choisir la période</option>
                                            <option value="Matin">Matin</option>
                                            <option value="Après-midi">Après-midi</option>
                                            <option value="Soir">Soir</option>
                                            <option value="Nuit">Nuit</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="p-6 bg-gray-100 border rounded-lg shadow-sm max-w-md">
                                    <h2 class="text-lg font-bold mb-4">Choisir l'horaire de fin</h2>

                                    <!-- Date de retrait -->
                                    <div class="mb-4">
                                        <label for="pickup-date"
                                            class="block text-sm font-medium text-gray-700 mb-2">Date de
                                            retrait</label>
                                        <div class="relative">
                                            <input type="date" id="datePickerEnd" name="dateTard"
                                                wire:model="dateTard" min="{{ now()->addDay()->toDateString() }}"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <!-- Heure de retrait -->
                                    <div class="mb-4">
                                        <label for="timePickerEnd"
                                            class="block text-sm font-medium text-gray-700 mb-2">Heure de
                                            retrait</label>
                                        <div class="relative">
                                            <input type="time" id="timePickerEnd" name="timeEnd"
                                                wire:model="timeEnd"
                                                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                oninput="togglePeriodSelect2()">
                                        </div>
                                    </div>

                                    <!-- Période -->
                                    <div>
                                        <label for="dayPeriodFin"
                                            class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                                        <select id="dayPeriodFin" name="dayPeriodFin" wire:model="dayPeriodFin"
                                            class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none"
                                            onchange="toggleTimeInput2()">
                                            <option value="" selected>Choisir la période</option>
                                            <option value="Matin">Matin</option>
                                            <option value="Après-midi">Après-midi</option>
                                            <option value="Soir">Soir</option>
                                            <option value="Nuit">Nuit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    <input type="hidden" name="userTrader" wire:model.defer="userTrader">
                    <input type="hidden" name="nameProd" wire:model.defer="nameProd">
                    <input type="hidden" name="userSender" wire:model.defer="userSender">
                    <input type="hidden" name="photoProd" wire:model.defer="photoProd">
                    <input type="hidden" name="idProd" wire:model.defer="idProd">
                    <input type="hidden" name="prix" wire:model.defer="prix">

                    <p id="errorMessage" class="text-sm text-center text-red-500 hidden"></p>


                    <div class="text-center mt-3">
                        <button id="requestCreditButton" wire:click="requestCredit" wire:loading.attr="disabled"
                            class="hidden py-2 px-3 w-full inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                            <span wire:loading.remove>Demander un crédit</span>
                            <span wire:loading>Envoi en cours...</span>
                        </button>
                    </div>

                    <div class="flow-root">
                        <div class="-my-3 divide-y divide-gray-200 dark:divide-gray-800">
                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Montant de l'achat
                                </dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">0</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">TVA</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">0%</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-bold text-gray-900 dark:text-white">Montant Total</dt>
                                <dd class="text-base font-bold text-purple-600 dark:text-white" id="montantTotal">0
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

                        <button type="submit" id="submitButton"
                            class="py-2 px-3 w-full inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                            wire:loading.attr="disabled" disabled>
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
    const price = parseFloat(document.querySelector('[data-price]').getAttribute('data-price'));
    const minQuantity = parseInt(quantityInput.getAttribute('data-min'));
    const maxQuantity = parseInt(quantityInput.getAttribute('data-max'));
    const quantity = parseInt(quantityInput.value);
    const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
    const montantTotalElement = document.getElementById('montantTotal');
    const errorMessageElement = document.getElementById('errorMessage');
    const submitButton = document.getElementById('submitButton');
    const montantTotalInput = document.getElementById('montant_total_input');
    const requestCreditButton = document.getElementById('requestCreditButton');
    const location = document.getElementById('location');

    const userBalance = {{ $userWallet->balance }};
    let hasError = false; // Variable pour suivre l'état des erreurs
    const selectedOption = document.querySelector('input[name="selectedOption"]:checked'); // Récupère l'option sélectionnée

    // Vérification si aucune option n'est sélectionnée
    if (!selectedOption) {
        errorMessageElement.innerText = `Veuillez sélectionner un mode de réception.`;
        errorMessageElement.classList.remove('hidden');
        submitButton.disabled = true;
        hasError = true;
    }

    // Vérification si le champ "location" est vide
    if (!hasError && !location.value.trim()) {
        errorMessageElement.innerText = `Veuillez remplir le champ de localisation.`;
        errorMessageElement.classList.remove('hidden');
        montantTotalElement.innerText = '0 FCFA'; // Réinitialise le montant affiché
        submitButton.disabled = true;
        montantTotalInput.value = 0; // Met à jour l'input montant_total_input
        requestCreditButton.classList.add('hidden'); // Masque le bouton de crédit
        hasError = true;
    }

    // Vérification de la quantité
    if (!hasError && (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity)) {
        errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
        errorMessageElement.classList.remove('hidden');
        montantTotalElement.innerText = '0 FCFA';
        submitButton.disabled = true;
        montantTotalInput.value = 0; // Réinitialise le montant total
        requestCreditButton.classList.add('hidden'); // Masquer le bouton de crédit
        hasError = true;
    }

    // Vérification du solde utilisateur
    if (!hasError && montantTotal > userBalance) {
        errorMessageElement.innerText = `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
        errorMessageElement.classList.remove('hidden');
        montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
        submitButton.disabled = true;
        requestCreditButton.classList.remove('hidden'); // Affiche le bouton de demande de crédit
    }

    // Si aucune erreur n'a été détectée, afficher le montant total et activer le bouton de soumission
    if (!hasError && montantTotal <= userBalance) {
        errorMessageElement.classList.add('hidden');
        montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
        submitButton.disabled = false;
        montantTotalInput.value = montantTotal;
        requestCreditButton.classList.add('hidden');
    }
}


</script>
