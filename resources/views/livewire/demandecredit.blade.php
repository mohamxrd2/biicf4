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
                        Procedure
                    </span>
                </li>
            </ol>

            <h2 class="mb-4 text-xl text-center font-bold text-gray-900 dark:text-white">Formulaire De Demande Crédit
            </h2>
            <form wire:submit.prevent="submit">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 rounded-lg shadow-md dark:bg-gray-800">
                    <!-- Titre -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block mb-3 text-lg font-semibold text-gray-900 dark:text-white">
                            Demande ID (Demande): {{ $referenceCode }}</label>
                        <label for="brand" class="block mb-3 text-lg font-semibold text-gray-900 dark:text-white">
                            Objet du financement: Demande de crédit</label>
                    </div>

                    <!-- Montant recherché -->
                    <div class="w-full">
                        <label for="price"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Montant
                            recherché</label>
                        <input type="number" wire:model="price" id="price"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="10.000 XOF" required>
                    </div>

                    <!-- Durée du crédit -->
                    <div>
                        <label for="duration" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Durée
                            du crédit (mois)</label>
                        <input type="number" wire:model="duration" id="duration"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="12" required>
                    </div>

                    <!-- Type de financement -->
                    <div x-data="{ typeFinancement: '' }" class="flex flex-col space-y-4">
                        <label for="financement"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type
                            de financement</label>
                        <select wire:model="financementType" id="financement" x-model="typeFinancement"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected>Choisir un type</option>
                            <option value="demande-directe">Demande Directe</option>
                            <option value="offre-composite">Offre composite (groupée)</option>
                        </select>

                        <!-- Conteneur pour l'alignement horizontal -->
                        <div class="flex space-x-4 mt-4" x-show="typeFinancement">
                            <!-- Champ de saisie pour Demande Directe -->
                            <div x-show="typeFinancement === 'demande-directe'" class="flex flex-col flex-1">
                                <label for="username"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Entrez le
                                    username</label>
                                <input type="text" wire:model="username" id="username"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Username">
                            </div>

                            <!-- Ciblage du bailleur -->
                            <div x-show="typeFinancement === 'offre-composite'" class="flex flex-col flex-1">
                                <label for="bailleur"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ciblez un
                                    bailleur
                                    ou entrez son username</label>
                                <select wire:model="bailleur" id="bailleur"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option selected>Choisir un bailleur</option>
                                    <option value="bank">Bank/IFD</option>
                                    <option value="pgm">Pgm Public/Para-Public</option>
                                    <option value="fonds">Fonds d’investissement</option>
                                    <option value="particulier">Particulier</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dates et Heures alignées -->
                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="start-date"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date de
                                début</label>
                            <input type="date" wire:model="startDate" id="start-date"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                required>
                        </div>

                        <div>
                            <label for="start-time"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Heure de
                                début</label>
                            <input type="time" wire:model="startTime" id="start-time"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                required>
                        </div>

                        <div>
                            <label for="end-date"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date de
                                fin</label>
                            <input type="date" wire:model="endDate" id="end-date"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                required>
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

                    <!-- Retour sur investissement -->
                    <div class="sm:col-span-2">
                        <label for="roi"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Retour
                            sur investissement</label>
                        <input type="number" wire:model="roi" id="roi"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="12%" required>
                    </div>
                    <!-- Message de succès -->
                    @if (session()->has('message'))
                        <div class="mt-4 text-green-600">
                            {{ session('message') }}
                        </div>
                    @endif

                    <button type="submit"
                        class="mt-6 w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Soumettre</button>
                </div>

            </form>

        </div>
    @endif
</div>
