<div>

    <form wire:submit.prevent="AchatDirectForm" id="formAchatDirect">
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-800 dark:bg-gray-800 dark:text-red-400">
                <span class="font-medium text-white">{{ session('error') }}</span>
            </div>
        @endif
        <div x-data="{ selectedOption: @entangle('selectedOption'), quantity: 1, location: '' }">

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

                    <li>
                        <span
                            class="flex items-center after:mx-2  after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                            <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Crédit
                        </span>
                    </li>
                </ol>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Champ de quantité -->
                    <div class="mb-4">
                        <h2 class="text-lg font-bold mb-2">Quantité du produit</h2>
                        <input id="quantity" type="number" x-model="quantity" min="1"
                            class="w-64 p-2 text-center border rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    <!-- Champ de localisation -->
                    <div>
                        <h2 class="text-lg font-bold mb-2">Adresse de livraison</h2>
                        <input id="location" type="text" x-model="location"
                            placeholder="Entrez votre localisation"
                            class="w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                </div>

                <!-- Résumé -->
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border">
                    <h4 class="font-semibold text-gray-800">Résumé :</h4>
                    <p class="text-gray-700">
                        Vous avez sélectionné <span class="font-bold" x-text="quantity"></span> article(s).
                    </p>
                    <p class="text-gray-700">
                        Localisation : <span class="font-bold" x-text="location || 'Non renseignée'"></span>
                    </p>
                </div>

                <!-- Mode de réception -->
                <div class="mb-4">
                    <h2 class="text-lg font-bold mb-2">Mode de réception</h2>
                    <!-- Option: Livraison à domicile -->
                    <button type="button" @click="selectedOption = 'Delivery'"
                        class="flex items-center p-4 rounded-lg border-2 transition-all w-full mb-4"
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3l18 18M9 17l6-6 4 4V6H5v11l4-4 5 5z" />
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h3 class="font-semibold text-gray-800">Livraison à domicile</h3>
                            <p class="text-sm text-gray-500">Livré chez vous après négociation</p>
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-blue-800">Pas Disponible</span>
                        </div>
                    </button>

                    <!-- Option: Retrait en magasin -->
                    <button type="button" @click="selectedOption = 'Take Away'"
                        class="flex items-center p-4 rounded-lg border-2 transition-all w-full"
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3l18 18M9 17l6-6 4 4V6H5v11l4-4 5 5z" />
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h3 class="font-semibold text-gray-800">Retrait en magasin</h3>
                            <p class="text-sm text-gray-500">Disponible après réception de confirmation du fournisseur
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-blue-800">Gratuit</span>
                        </div>
                    </button>
                </div>




                <div class="flex flex-col space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        {{-- <input type="number" placeholder="quantité" id="quantityInput" name="quantité"
                            wire:model.defer="quantité" class="col-span-1 border border-gray-300 rounded-lg p-2"
                            data-min="{{ $produit->qteProd_min }}" data-max="{{ $produit->qteProd_max }}"
                            oninput="updateMontantTotalDirect()" required>
                        <input type="text" id="locationInput" name="localite" wire:model.defer="localite"
                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                            placeholder="Lieu de livraison" required> --}}


                        @if ($type == 'Service')
                            {{-- <select wire:model="selectedOption" name="type"
                                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                <option selected>Type de livraison</option>
                                @foreach ($optionsC as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select> --}}
                            <div class="flex items-center mb-3">
                                <!-- Date de début -->
                                <div class="w-1/2 mr-2 relative">
                                    <label for="datePickerStart" class="block text-sm font-medium text-gray-700">Au
                                        plus
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
                            {{-- <select wire:model="selectedOption" name="type" x-model="selectedOption"
                                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Type de livraison</option>
                                <option value="Achat avec livraison">Achat avec livraison</option>
                                <option value="Take Away">Take Away</option>
                            </select> --}}

                            <div x-show="selectedOption === 'Take Away'"class="col-span-2 grid grid-cols-2 gap-6 mt-4">
                                <div class="p-6 bg-white border rounded-lg shadow-sm max-w-md">
                                    <h2 class="text-lg font-bold mb-4">Choisir l'horaire de retrait</h2>

                                    <!-- Date de retrait -->
                                    <div class="mb-4">
                                      <label for="pickup-date" class="block text-sm font-medium text-gray-700 mb-2">Date de retrait</label>
                                      <div class="relative">
                                        <input
                                          id="pickup-date"
                                          type="date"
                                          class="w-full p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="jj/mm/aaaa"
                                        />
                                        <div class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-400">
                                          <!-- Icône du calendrier -->
                                          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m2 0a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v2a2 2 0 002 2m14 0v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7m0 0h16" />
                                          </svg>
                                        </div>
                                      </div>
                                    </div>

                                    <!-- Heure de retrait -->
                                    <div class="mb-4">
                                      <label for="pickup-time" class="block text-sm font-medium text-gray-700 mb-2">Heure de retrait</label>
                                      <div class="relative">
                                        <input
                                          id="pickup-time"
                                          type="time"
                                          class="w-full p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="--:--"
                                        />
                                        <div class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-400">
                                          <!-- Icône de l'horloge -->
                                          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-10a9 9 0 11-18 0 9 9 0 0118 0z" />
                                          </svg>
                                        </div>
                                      </div>
                                    </div>

                                    <!-- Période -->
                                    <div>
                                      <label for="pickup-period" class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                                      <select
                                        id="pickup-period"
                                        class="w-full p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                      >
                                        <option>Choisir la période</option>
                                        <option>Matin</option>
                                        <option>Après-midi</option>
                                        <option>Soir</option>
                                      </select>
                                    </div>
                                  </div>

                                <div class="col-span-1">

                                    <label for="datePickerStart" class="block text-sm font-medium text-gray-700">Date
                                        au
                                        plus
                                        tôt</label>
                                    <input type="datetime-local" id="datePickerStart" name="dateTot"
                                        wire:model="dateTot"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">

                                </div>

                                <div class="col-span-1">
                                    <!-- Date de fin -->
                                    <label for="datePickerEnd" class="block text-sm font-medium text-gray-700">Date au
                                        plus
                                        tard</label>
                                    <input type="datetime-local" id="datePickerEnd" name="dateTard"
                                        wire:model="dateTard"
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div class="col-span-1">
                                    <label for="dayPeriod"
                                        class="block text-sm text-gray-700 dark:text-gray-300">Période
                                    </label>
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
                                    </label>
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
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Montant de l'achat</dt>
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
