<div x-data="{
    quantite: 0,
    roi: 0,
    endDate: '',
    duration: '',
    financementType: '',
    search: '',
    bailleur: '',
    montantMax: 0,
    tauxInteret: 0,
    creditTotal: 0,
    errorMessage: '',
    showError: false,
    quantiteMin: {{ $quantiteMin }},
    quantiteMax: {{ $quantiteMax }},
    prixUnitaire: {{ $sommedemnd }},

    updateMontantTotalCredit() {
        // Convert inputs to numbers and handle NaN cases
        const quantity = parseInt(this.quantite) || 0;
        const roiValue = parseFloat(this.roi) || 0;

        // Calculate values
        this.montantMax = this.prixUnitaire * quantity;
        this.tauxInteret = this.montantMax * (roiValue / 100);
        this.creditTotal = this.montantMax + this.tauxInteret;

        // Validate quantity
        if (quantity < this.quantiteMin || quantity > this.quantiteMax) {
            this.errorMessage = `La quantité doit être comprise entre ${this.quantiteMin} et ${this.quantiteMax}.`;
            this.showError = true;
        } else {
            this.showError = false;
        }
    },

    updateDate() {
        if (this.endDate && this.duration) {
            const endDateObj = new Date(this.endDate);
            const durationObj = new Date(this.duration);

            if (endDateObj >= durationObj) {
                alert('La date de fin ne doit pas dépasser la durée.');
                this.endDate = '';
            }
        }
    },

    formatPrice(amount) {
        return amount.toLocaleString() + ' FCFA';
    }
}" x-init="$watch('quantite', () => { updateMontantTotalCredit() });
$watch('roi', () => { updateMontantTotalCredit() })">

    <div class="relative md:static bg-white rounded-lg shadow-lg">
        <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
            <h1 class="text-lg font-bold"><i class="fas fa-file-alt"></i> Demande de Crédit</h1>
            <span class="text-sm">Ref: 2024-11-21-{{ $referenceCode }}</span>
        </div>
        <form wire:submit.prevent="submit">
            @if ($messages && count($messages) > 0)
                <div class="m-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                    <strong class="font-bold">Attention!</strong>
                    <ul class="mt-2">
                        @foreach ($messages as $message)
                            <li class="list-disc list-inside">{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 rounded-lg shadow-md dark:bg-gray-800">
                <!-- Titre -->
                <div class="sm:col-span-2">
                    <label for="brand" class="block mb-3 text-lg font-extrabold text-gray-900 dark:text-white">
                        Objet Du Financement: Achat du produit
                        {{ $nameProd }}</label>
                </div>

                <!-- Prix Unitaire -->
                <div class=" flex flex-rows p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                        Prix Unitaire
                    </div>
                    <div class="mt-4 text-2xl font-bold text-gray-800 dark:text-white">
                        {{ $sommedemnd }} FCFA
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-300 mt-1">par unité</div>
                </div>


                <!-- Type de financement -->
                <div class="flex flex-col space-y-4 sm:col-span-2">
                    <label for="financement" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type
                        de financement</label>
                    <select wire:model="financementType" id="financement" x-model="financementType"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option selected>Choisir un type</option>
                        <option value="demande-directe">Demande Directe</option>
                        <option value="offre-composite">Offre composite (groupée)</option>
                        <option value="négocié">Offre négocié</option>
                    </select>
                    @error('financementType')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror

                    <!-- Conteneur pour l'alignement horizontal -->
                    <div class="flex space-x-4 mt-4" x-show="financementType">
                        <!-- Champ de saisie pour Demande Directe -->
                        <div x-show="financementType === 'demande-directe'" class="flex flex-col flex-1">
                            <label for="username"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Entrez le
                                username</label>
                            <input wire:model.live="search" x-model="search"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                type="text" placeholder="Entrez le nom de l'user">
                            @error('search')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror

                            @if (!empty($search))
                                @foreach ($users as $user)
                                    <div class="cursor-pointer py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg"
                                        wire:click="selectUser('{{ $user->id }}', '{{ $user->username }}')">
                                        <div class="flex">
                                            <img class="w-5 h-5 mr-2 rounded-full" src="{{ asset($user->photo) }}"
                                                alt="">
                                            <div class="flex justify-between items-center w-full">
                                                <span>{{ $user->username }} ({{ $user->name }})</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Ciblage du bailleur -->
                        <div x-show="financementType === 'offre-composite' || financementType === 'négocié'"
                            class="flex flex-col flex-1">
                            <label for="bailleur"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ciblez un
                                bailleur ou entrez son username</label>
                            <select wire:model="bailleur" id="bailleur" x-model="bailleur"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected>Choisir un bailleur</option>
                                <option value="Bank/IFD">Bank/IFD</option>
                                <option value="Pgm Public/Para-Public">Pgm Public/Para-Public</option>
                                <option value="Fonds d'investissement">Fonds d'investissement</option>
                                <option value="Particulier">Particulier</option>
                            </select>
                            @error('bailleur')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Montant recherché -->
                <div class="w-full">
                    <label for="quantitInput" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Quelle quantitée voulez-vous acheter?
                    </label>
                    <input type="number" id="quantitInput" placeholder="" wire:model="quantite" x-model="quantite"
                        class="bg-gray-50 lg:col-span-2 sm:col-span-2 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        required>
                    @error('quantite')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="roi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Retour sur investissement (%)
                    </label>
                    <input type="number" id="roi" wire:model="roi" x-model="roi"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="12%" required>
                </div>

                <p x-show="showError" x-text="errorMessage" class="text-sm text-center text-red-500 sm:col-span-2">
                </p>

                <div class="sm:col-span-2"></div>

                <!-- Dates et Heures alignées -->
                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="Datefin"
                            class="block mb-2 text-xl font-semibold text-gray-900 dark:text-white">Date
                            limite d'attente</label>
                        <input type="datetime-local" wire:model="endDate" x-model="endDate" id="Datefin"
                            x-on:change="updateDate()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            required>
                    </div>

                    <div>
                        <label for="Periode"
                            class="block mb-2 text-lg font-semibold text-gray-900 dark:text-white">Date limite
                            de remboursement
                        </label>
                        <input type="datetime-local" wire:model="duration" x-model="duration" id="Periode"
                            x-on:change="updateDate()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            required>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <div class="-my-3 divide-y divide-gray-200 dark:divide-gray-800">
                        <dl class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Montant recherché
                            </dt>
                            <dd class="text-base font-medium text-gray-900 dark:text-white"
                                x-text="formatPrice(montantMax)">0 FCFA
                            </dd>
                            <input type="hidden" name="montantMax" x-model="montantMax">
                        </dl>

                        <dl class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Retour sur
                                investissement/ Taux d'intérêt</dt>
                            <dd class="text-base font-medium text-gray-900 dark:text-white"
                                x-text="formatPrice(tauxInteret)">0 FCFA
                            </dd>
                            <input type="hidden" name="tauxInteret" x-model="tauxInteret" required>
                        </dl>

                        <dl class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-base font-bold text-gray-900 dark:text-white">Crédit Total</dt>
                            <dd class="text-base font-bold text-purple-600 dark:text-white"
                                x-text="formatPrice(creditTotal)">0 FCFA
                            </dd>
                            <input type="hidden" name="creditotal" x-model="creditTotal">
                        </dl>
                        <dl class="flex items-center justify-between gap-4 py-3">
                        </dl>
                    </div>
                </div>
                <p class="bg-gray-100 sm:col-span-2 w-full text-gray-700 p-4 rounded-md shadow-md">
                    En soumettant ce formulaire, je certifie que les informations fournies sont exactes et
                    complètes.
                    J'autorise la plateforme à effectuer toutes les vérifications nécessaires concernant ces
                    informations.
                </p>

                <!-- Soumission du formulaire -->
                <div class="sm:col-span-2">
                    <button type="submit"
                        class="inline-flex justify-center w-full rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Soumettre
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
