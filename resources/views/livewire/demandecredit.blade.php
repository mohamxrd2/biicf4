<div>
    @if ($showSection)
        <div class="relative md:static  bg-white rounded-lg shadow-lg">
            <ol
                class="items-center flex w-full max-w-2xl text-center text-sm font-medium text-gray-500 dark:text-gray-400 sm:text-base  p-5">
                <li
                    class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                    <span
                        class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Details
                    </span>
                </li>

                <li
                    class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                    <span
                        class="flex items-center after:mx-2  after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Achat
                    </span>
                </li>

                <li>
                    <span
                        class="flex items-center after:mx-2 text-blue-600 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                        <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Crédit
                    </span>
                </li>
            </ol>

            {{-- <h2 class="mb-4 text-xl text-center font-bold text-gray-900 dark:text-white">Formulaire De Demande Crédit
            </h2> --}}
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
                        @if ($showSection)
                            <label for="name"
                                class="block mb-3 text-lg font-semibold text-gray-900 dark:text-white">
                                ID Demande : {{ $referenceCode }}</label>
                            <label for="brand"
                                class="block mb-3 text-lg font-semibold text-gray-900 dark:text-white">
                                Objet du financement: Demande de crédit pour Achat du produit
                                {{ $nameProd }}</label>
                            <div class="hidden">
                                <p>Montant Plafond pour ce produit : {{ $montantmax }} FCFA</p>
                                <p>Prix Plafond pour ce produit : {{ $sommedemnd }} FCFA</p>
                                <p>Quantité Plafond pour ce produit : {{ $quantiteMax }} </p>
                                <p>Quantité Minimale pour ce produit : {{ $quantiteMin }} </p>
                            </div>
                        @endif
                    </div>

                    <!-- Montant recherché -->
                    <div class="w-full">
                        <label for="quantitInput" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Entrez la quantité que vous voulez acheté?
                        </label>
                        <input type="number" id="quantitInput" placeholder="Déterminera la somme dont vous avez besoin"
                            wire:model="quantite"
                            class="bg-gray-50 lg:col-span-2 sm:col-span-2 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            data-min="{{ $quantiteMin }}" data-max="{{ $quantiteMax }}"
                            data-price="{{ $montantmax }}" oninput="updateMontantTotalCredit()" required>
                        @error('quantite')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="roi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Retour sur investissement (%)
                        </label>
                        <input type="number" id="roi" wire:model="roi"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="12%" oninput="updateMontantTotalCredit()" required>
                        @error('roi')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <p id="error_Message" class="text-sm text-center text-red-500 hidden"></p>

                    <div class="sm:col-span-2">
                        <div class="-my-3 divide-y divide-gray-200 dark:divide-gray-800">
                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Montant recherché
                                </dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white" id="montantMax">0 FCFA
                                </dd>
                                <input type="hidden" name="montantMax" id="montant_total">
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Retour sur
                                    investissement/ Taux d'intérêt</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white" id="tauxInteret">0 FCFA
                                </dd>
                                <input type="hidden" name="tauxInteret" id="taux_interet" required>
                            </dl>

                            <dl class="flex items-center justify-between gap-4 py-3">
                                <dt class="text-base font-bold text-gray-900 dark:text-white">Crédit Total</dt>
                                <dd class="text-base font-bold text-purple-600 dark:text-white" id="creditotal">0 FCFA
                                </dd>
                                <input type="hidden" name="creditotal" id="credi_total">
                            </dl>
                            <dl class="flex items-center justify-between gap-4 py-3">
                            </dl>
                        </div>
                    </div>

                    <!-- Type de financement -->
                    <div x-data="{ typeFinancement: '' }" class="flex flex-col space-y-4 sm:col-span-2">
                        <label for="financement"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type de
                            financement</label>
                        <select wire:model="financementType" id="financement" x-model="typeFinancement"
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
                        <div class="flex space-x-4 mt-4" x-show="typeFinancement">
                            <!-- Champ de saisie pour Demande Directe -->
                            <div x-show="typeFinancement === 'demande-directe'" class="flex flex-col flex-1">
                                <label for="username"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Entrez le
                                    username</label>
                                <input wire:model.live="search"
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
                                                <img class="w-5 h-5 mr-2 rounded-full"
                                                    src="{{ asset($user->photo) }}" alt="">
                                                <div class="flex justify-between items-center w-full">
                                                    <span>{{ $user->username }} ({{ $user->name }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Ciblage du bailleur -->
                            <div x-show="typeFinancement === 'offre-composite' || typeFinancement === 'négocié'" class="flex flex-col flex-1">
                                <label for="bailleur"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ciblez un
                                    bailleur ou entrez son username</label>
                                <select wire:model="bailleur" id="bailleur"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option selected>Choisir un bailleur</option>
                                    <option value="Bank/IFD">Bank/IFD</option>
                                    <option value="Pgm Public/Para-Public">Pgm Public/Para-Public</option>
                                    <option value="Fonds d’investissement">Fonds d’investissement</option>
                                    <option value="Particulier">Particulier</option>
                                </select>
                                @error('bailleur')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="duration"
                            class="block mb-2 text-xl font-extrabold underline text-gray-900 dark:text-white">Date
                            limite d'attente</label>
                    </div>
                    <!-- Dates et Heures alignées -->
                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">


                        <div>
                            <label for="end-date"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date de
                                fin</label>
                            <input type="date" wire:model="endDate" id="Datefin"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                oninput="updateDate()" required>
                        </div>

                        <div>
                            <label for="end-time"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Heure de
                                fin</label>
                            <input type="time" wire:model="endTime" id="end-time"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                required>

                        </div>
                    </div>

                    <!-- Durée du crédit -->

                    <div class="sm:col-span-2">
                        <label for="duration"
                            class="block mb-2 text-xl font-extrabold underline text-gray-900 dark:text-white">Durée
                            du crédit (Délais de remboursement)</label>
                        <input type="date" wire:model="duration" id="Periode"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            oninput="updateDate()" required>
                    </div>


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
    @endif





    <script>
        function updateDate() {
            // Sélectionner les éléments de date
            const durationInput = document.getElementById("Periode");
            const Datefin = document.getElementById("Datefin");

            // Récupérer les valeurs de l'input (les dates sélectionnées)
            const selectedDate = new Date(durationInput.value);
            const selectedDatefin = new Date(Datefin.value);

            // Afficher les dates sélectionnées dans la console
            console.log("Date de durée sélectionnée :", durationInput.value);
            console.log("Date de fin sélectionnée :", Datefin.value);

            // Vérification si la date de fin est supérieure à la durée
            if (selectedDatefin && selectedDate && selectedDatefin >= selectedDate) {
                alert('La date de fin ne doit pas dépasser la durée.');
                Datefin.value = ''; // Réinitialiser la date de fin si la condition est remplie
                console.log("Condition échouée : la date de fin est supérieure à la durée.");
            } else {
                console.log("Condition réussie : la date de fin est inférieure ou égale à la durée.");
            }
        }

        // Fonction pour mettre à jour le montant total en fonction de la quantité
        function updateMontantTotalCredit() {
            const quantitInput = document.getElementById('quantitInput');
            const price = parseFloat(quantitInput.getAttribute('data-price'));
            const minQuantity = parseInt(quantitInput.getAttribute('data-min'));
            const maxQuantity = parseInt(quantitInput.getAttribute('data-max'));
            const roiInput = document.getElementById('roi');


            const quantity = parseInt(quantitInput.value);
            const roi = parseFloat(roiInput.value);

            const montantTotalElement = document.getElementById('montantMax');
            const interestElement = document.getElementById('tauxInteret');
            const creditotal = document.getElementById('creditotal');
            const montantTotalInput = document.getElementById('montant_total');
            const tauxInteret = document.getElementById('taux_interet');
            const crediTotal = document.getElementById('credi_total');
            const error_Message = document.getElementById('error_Message');
            // const submitButton = document.getElementById('submitCredit');



            let montantMax = price * (isNaN(quantity) ? 0 : quantity);
            let interet = montantMax * (isNaN(roi) ? 0 : roi / 100);
            let creditTotal = montantMax + interet;


            // Vérifier si la quantité est dans les limites et afficher un message d'erreur si nécessaire
            if (isNaN(quantity) || quantity < minQuantity || quantity > maxQuantity) {
                error_Message.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
                error_Message.classList.remove('hidden');
                montantTotalElement.innerText = '0 FCFA';
                interestElement.innerText = '0 FCFA';
                creditotal.innerText = '0 FCFA';
                // submitButton.disabled = true;
                montantTotalInput.value = 0;
                tauxInteret.value = 0;
                crediTotal.value = 0;

            } else {
                error_Message.classList.add('hidden');
                montantTotalElement.innerText = `${montantMax.toLocaleString()} FCFA`;
                interestElement.innerText = `${interet.toLocaleString()} FCFA`;
                creditotal.innerText = `${creditTotal.toLocaleString()} FCFA`;
                montantTotalInput.value = montantMax;
                tauxInteret.value = interet;
                crediTotal.value = creditTotal;
                // submitButton.disabled = false;
            }
        }
    </script>



</div>
