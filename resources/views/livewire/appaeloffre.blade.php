<div>
    <!-- Écran de chargement Livewire -->
    <div wire:loading wire:target="submitEnvoie, submitGroupe, requestCredit"
        class="fixed   inset-0 flex flex-col items-center justify-center w-full h-full bg-white bg-opacity-75 z-50 transition-opacity duration-300">
        <div class="text-center">

            <div style="width: 30px; height: 300px"></div>
            <svg class="animate-spin h-16 w-16 sm:h-12 sm:w-12 text-blue-600 mx-auto" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>

            <p class="mt-4 text-lg sm:text-base text-gray-700">
                Chargement en cours...
            </p>
        </div>
    </div>



    {{--  --}}

    <form wire:submit.prevent>

        <div x-data="{ selectedOption: @entangle('selectedOption'), quantité: 0, localite: '' }">

            <div x-data="productPurchase()" class="relative md:static p-4 bg-white rounded-lg shadow-lg">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <div class="mb-4">
                        <h2 class="text-lg font-bold mb-2">Quantité du produit</h2>
                        <input id="quantityInput" type="number" wire:model= "quantité" x-model="quantity"
                            min="1"
                            class="w-full p-2 text-center border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            @input="updateMontantTotalDirect()" required />
                    </div>

                    <!-- Champ de localisation -->
                    <div>
                        <h2 class="text-lg font-bold mb-2">Adresse de livraison</h2>
                        <input id="location" type="text" wire:model='localite' x-model="localite"
                            placeholder="Entrez votre localisation"
                            class="w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required />
                    </div>
                </div>
                <p x-text="errorMessage" class="text-sm text-center text-red-500" x-show="errorMessage"></p>


                <!-- Résumé -->
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border">
                    <h4 class="font-semibold text-gray-800">Résumé :</h4>
                    <p class="text-gray-700">
                        Vous avez sélectionné <span class="font-bold" x-text="quantity"></span>
                        ({{ $distinctCondProds }}).
                    </p>
                    <p class="text-gray-700">
                        Localisation : <span class="font-bold" x-text="localite || 'Non renseignée'"></span>
                    </p>
                    <p class="text-gray-700" data-price="{{ $lowestPricedProduct }}">
                        Nom du Produit : <span class="font-bold">{{ $name }}</span>
                    </p>
                    <p class="text-gray-700" data-price="{{ $lowestPricedProduct }}">
                        Prix Max : <span class="font-bold">{{ $lowestPricedProduct }} FCFA</span>
                    </p>
                    <p class="text-gray-700">
                        reference : <span class="font-bold">{{ $reference }}</span>
                    </p>
                    <p class="text-gray-700">
                        Type : <span class="font-bold">{{ $type }}</span>
                    </p>
                    <p class="text-gray-700">
                        Zone ciblée : <span class="font-bold">{{ $appliedZoneValue }}</span>
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
                                <x-time-picker-form title="Choisir l'horaire de debut" dateId="datePickerStart"
                                    min="{{ now()->addDay()->toDateString() }}" timeId="timePickerStart"
                                    periodId="dayPeriod" dateModel="dateTot" timeModel="timeStart"
                                    periodModel="dayPeriod" dateLabel="Date" />
                            </div>
                            <div x-show="selectedOption === 'Take Away'">

                                <x-time-picker-form title="Choisir l'horaire de fin" dateId="datePickerEnd"
                                    timeId="timePickerEnd" periodId="dayPeriodFin" dateModel="dateTard"
                                    timeModel="timeEnd" periodModel="dayPeriodFin" dateLabel="Date" />
                            </div>
                        @endif

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
                            <div x-show="selectedOption === 'Delivery'"class="col-span-2 grid grid-cols-2 gap-6 mt-4">
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
                                </div>
                            </div>
                        @endif

                    </div>

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
                                <dd class="text-base font-medium text-gray-900 dark:text-white"
                                    x-text="montantTotal + ' FCFA'"></dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-bold text-gray-900 dark:text-white">Montant Total</dt>
                                <dd class="text-base font-bold text-purple-600 dark:text-white"
                                    x-text="montantTotal + ' FCFA'"></dd>
                                <input type="hidden" name="montantTotal" x-model="montantTotal">
                            </dl>
                        </div>
                    </div>

                    <!-- Afficher les messages d'erreur -->
                    @if (session()->has('success'))
                        <div class="bg-red-500 text-white p-2 mt-2 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <p class="bg-gray-100 sm:col-span-2 w-full text-gray-700 p-4 rounded-md shadow-md">
                        En soumettant ce formulaire, je certifie que les informations fournies sont exactes et
                        complètes.
                        J'autorise la plateforme à effectuer toutes les vérifications nécessaires concernant ces
                        informations.
                    </p>
                    <div class="text-center mt-3">

                        <!-- Bouton pour l'envoi direct -->
                        <button type="button" id="submitEnvoie" :disabled="disableSubmit"
                            class="py-2 px-3 w-full inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                            wire:click="submitEnvoie" wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                            <span wire:loading.remove>cliquez ici, Pour vous envoyer directement aux fournisseurs de
                                votre zone</span>
                            <span wire:loading>Payement en cours...</span>
                        </button>


                        <!-- Message pour les types de services -->
                        @if ($type == 'Service')
                            <p class="text-center text-gray-600">
                                Le type est un service, il n'est pas possible de grouper avec les clients. Passez à une
                                offre
                                directe.
                            </p>
                        @elseif ($appliedZoneValue)
                            <!-- Bouton pour le regroupement -->
                            <button x-show="selectedOption !== 'Take Away'" type="button" id="submitGroupe"
                                :disabled="disableSubmit"
                                class="py-2 px-3 mt-4 w-full inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-green-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                                wire:click="submitGroupe" wire:loading.attr="disabled">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>

                                <span wire:loading.remove>cliquez ici, Pour vous grouper avec d'autres acheteurs de
                                    votre zone</span>
                                <span wire:loading>Payement en cours...</span>
                            </button>
                        @else
                            <!-- Message si la zone n'est pas sélectionnée -->
                            <p class="text-center text-red-600">
                                Veuillez sélectionner une zone économique pour pouvoir vous grouper avec d'autres
                                acheteurs.
                            </p>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productPurchase', () => ({
                quantity: 0,
                price: parseFloat(document.querySelector('[data-price]').getAttribute('data-price')),
                minQuantity: parseInt(document.getElementById('quantityInput').getAttribute(
                    'data-min')),
                maxQuantity: parseInt(document.getElementById('quantityInput').getAttribute(
                    'data-max')),
                userBalance: {{ $wallet->balance }},
                montantTotal: 0,
                errorMessage: '',
                disableSubmit: false,

                updateMontantTotalDirect() {
                    this.montantTotal = this.quantity * this.price;

                    if (this.quantity < this.minQuantity || this.quantity > this.maxQuantity) {
                        this.errorMessage =
                            `La quantité doit être comprise entre ${this.minQuantity} et ${this.maxQuantity}.`;
                        this.montantTotal = 0;
                        this.disableSubmit = true;
                        return;
                    }

                    if (this.montantTotal > this.userBalance) {
                        this.errorMessage =
                            `Fonds insuffisants ! Solde: ${this.userBalance.toLocaleString()} FCFA.`;
                        this.disableSubmit = true;
                        return;
                    }

                    this.errorMessage = '';
                    this.disableSubmit = false;
                }
            }));
        });
    </script>

</div>
