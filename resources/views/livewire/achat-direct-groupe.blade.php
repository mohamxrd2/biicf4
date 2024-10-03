<div>

    <form wire:submit.prevent="AchatDirectForm" id="formAchatDirect">
        <div x-data="{ selectedOption: @entangle('selectedOption') }">

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
                        class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                        <span
                            class="flex items-center after:mx-2 text-blue-600 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                            <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            achat
                        </span>
                    </li>
                </ol>
                {{-- <h1 class="text-xl text-center mb-3">Achat direct</h1> --}}
                <div class="flex flex-col space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" placeholder="quantité" id="quantityInput" name="quantité"
                            wire:model.defer="quantité" class="col-span-1 border border-gray-300 rounded-lg p-2"
                            data-min="{{ $produit->qteProd_min }}" data-max="{{ $produit->qteProd_max }}"
                            oninput="updateMontantTotalDirect()" required>
                        <input type="text" id="locationInput" name="localite" wire:model.defer="localite"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            placeholder="Lieu de livraison" required>

                        @if (!empty($produit->specification))
                            <div class="block">
                                <input type="radio" id="specificite_1" name="specificite"
                                    value="{{ $produit->specification }}" wire:model.defer="selectedSpec"
                                    class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500">
                                <label for="specificite_1" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $produit->specification }}
                                </label>
                            </div>
                        @endif

                        @if (!empty($produit->specification2))
                            <div class="block">
                                <input type="radio" id="specificite_2" name="specificite"
                                    value="{{ $produit->specification2 }}" wire:model.defer="selectedSpec"
                                    class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500">
                                <label for="specificite_2" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $produit->specification2 }}
                                </label>
                            </div>
                        @endif

                        @if (!empty($produit->specification3))
                            <div class="block">
                                <input type="radio" id="specificite_3" name="specificite"
                                    value="{{ $produit->specification3 }}" wire:model.defer="selectedSpec"
                                    class="form-radio h-5 w-5 text-blue-600 transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-500">
                                <label for="specificite_3" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $produit->specification3 }}
                                </label>
                            </div>
                        @endif


                        @if ($type == 'Service')
                            <select wire:model="selectedOption" name="type"
                                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                <option selected>Type de livraison</option>
                                @foreach ($optionsC as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            <div class="flex items-center mb-3">
                                <!-- Date de début -->
                                <div class="w-1/2 mr-2 relative">
                                    <label for="datePickerStart" class="block text-sm font-medium text-gray-700">Au plus
                                        tôt</label>
                                    <input type="date" id="datePickerStart" name="dateTot" required
                                        wire:model="dateTot"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <!-- Date de fin -->
                                <div class="w-1/2 mr-2 relative">
                                    <label for="datePickerEnd" class="block text-sm font-medium text-gray-700">Au plus
                                        tard</label>
                                    <input type="date" id="datePickerEnd" name="dateTard" required
                                        wire:model="dateTard"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="flex items-center mb-3">
                                <!-- Heure de début -->
                                <div class="w-1/2 mr-2 relative">
                                    <label for="timePickerStart" class="block text-sm font-medium text-gray-700">Heure
                                        de
                                        début</label>
                                    <input type="time" id="timePickerStart" name="timeStart"
                                        wire:model="timeStart"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <!-- Heure de fin -->
                                <div class="w-1/2 mr-2 relative">
                                    <label for="timePickerEnd" class="block text-sm font-medium text-gray-700">Heure
                                        de
                                        fin</label>
                                    <input type="time" id="timePickerEnd" name="timeEnd" wire:model="timeEnd"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <p class="text-center">OU</p>

                            <!-- Sélecteur de période de la journée -->
                            <div class="mb-3 w-full">
                                <label for="dayPeriod" class="block text-sm text-gray-700 dark:text-gray-300">Période
                                    de
                                    la
                                    journée</label>
                                <select id="dayPeriod" name="dayPeriod" wire:model="dayPeriod"
                                    class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                    <option selected>Choisir la période</option>
                                    <option value="Matin">Matin</option>
                                    <option value="Après-midi">Après-midi</option>
                                    <option value="Soir">Soir</option>
                                    <option value="Soir">nuit</option>
                                </select>
                            </div>
                        @else
                            <select wire:model="selectedOption" name="type" x-model="selectedOption"
                                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Type de livraison</option>
                                <option value="Achat avec livraison">Achat avec livraison</option>
                                <option value="Take Away">Take Away</option>
                                {{-- <option value="Reservation">Reservation</option> --}}
                            </select>
                            <div x-show="selectedOption === 'Take Away'"class="col-span-2 grid grid-cols-2 gap-6 mt-4">

                                <div class="col-span-1">

                                    <label for="datePickerStart" class="block text-sm font-medium text-gray-700">Date
                                        au
                                        plus
                                        tôt</label>
                                    <input type="date" id="datePickerStart" name="dateTot" wire:model="dateTot"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <label for="timePickerStart" class="block text-sm font-medium text-gray-700">Heure
                                        de
                                        début</label>
                                    <input type="time" id="timePickerStart" name="timeStart"
                                        wire:model="timeStart"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">


                                </div>

                                <div class="col-span-1">
                                    <!-- Date de fin -->
                                    <label for="datePickerEnd" class="block text-sm font-medium text-gray-700">Date au
                                        plus
                                        tard</label>
                                    <input type="date" id="datePickerEnd" name="dateTard" wire:model="dateTard"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">

                                    <!-- Heure de fin -->
                                    <label for="timePickerEnd" class="block text-sm font-medium text-gray-700">Heure
                                        de
                                        fin</label>
                                    <input type="time" id="timePickerEnd" name="timeEnd" wire:model="timeEnd"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                {{-- <p class="text-center">OU</p> --}}
                                <!-- Nouvelle ligne d'input -->
                                <div class="col-span-1">
                                    <label for="dayPeriod"
                                        class="block text-sm text-gray-700 dark:text-gray-300">Période
                                        de
                                        la
                                        journée</label>
                                    <select id="dayPeriod" name="dayPeriod" wire:model="dayPeriod"
                                        class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                        <option value="" selected>Choisir la période</option>
                                        <option value="Matin">Matin</option>
                                        <option value="Après-midi">Après-midi</option>
                                        <option value="Soir">Soir</option>
                                        <option value="Nuit">Nuit</option>
                                    </select>
                                </div>

                                <!-- Nouvelle heure -->
                                <div class="col-span-1">
                                    <label for="dayPeriod"
                                        class="block text-sm text-gray-700 dark:text-gray-300">Période
                                        de
                                        la
                                        fin</label>
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
                        @endif

                    </div>





                    <input type="hidden" name="userTrader" wire:model.defer="userTrader">
                    <input type="hidden" name="nameProd" wire:model.defer="nameProd">
                    <input type="hidden" name="userSender" wire:model.defer="userSender">
                    <input type="hidden" name="message" wire:model.defer="message">
                    <input type="hidden" name="photoProd" wire:model.defer="photoProd">
                    <input type="hidden" name="idProd" wire:model.defer="idProd">
                    <input type="hidden" name="prix" wire:model.defer="prix">

                    {{-- <div class="flex justify-between px-4 mb-3 w-full">
                        <p class="font-semibold text-sm text-gray-500">Prix total:</p>
                        <p class="text-sm text-purple-600" id="montantTotal">0 FCFA</p>
                        <input type="hidden" name="montantTotal" id="montant_total_input">
                    </div> --}}

                    <p id="errorMessage" class="text-sm text-center text-red-500 hidden"></p>


                    <div class="text-center mt-3">
                        <button id="requestCreditButton" wire:click="requestCredit" wire:loading.attr="disabled"
                            class="hidden py-2 px-3 w-full inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-gray-200">
                            <span wire:loading.remove>Demander un crédit</span>
                            <span wire:loading>Envoi en cours...</span>
                        </button>
                    </div>

                    <div class="flow-root">
                        <div class="-my-3 divide-y divide-gray-200 dark:divide-gray-800">
                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Subtotal</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">0</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Tax</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">0</dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                <dd class="text-base font-bold text-gray-900 dark:text-white" id="montantTotal">0
                                </dd>
                                <input type="hidden" name="montantTotal" id="montant_total_input">

                            </dl>
                        </div>
                    </div>
                    <div class="text-center mt-3">

                        <button type="submit" id="submitButton"
                            class="py-2 px-3 w-full inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                            wire:loading.attr="disabled" disabled>
                            <span wire:loading.remove>Payement</span>
                            <span wire:loading>Envoi en cours...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
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
        const requestCreditButton = document.getElementById('requestCreditButton');

        const userBalance = {{ $userWallet->balance }};

        if (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity) {
            errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = '0 FCFA';
            submitButton.disabled = true;
            requestCreditButton.classList.add('hidden'); // Masquer le bouton de crédit si autre erreur

        } else if (montantTotal > userBalance) {
            errorMessageElement.innerText =
                `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            submitButton.disabled = true;
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
