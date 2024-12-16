<div>
    <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-8">
        <div class="relative inline-block w-full">
            <div>
                <button type="button"
                    class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    id="options-menu" aria-haspopup="true" aria-expanded="true" onclick="toggleDropdown()">
                    Fonctionnalitées
                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div id="dropdown-menu"
                class="absolute  z-10 mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden">
                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">

                    <div class="px-4 py-2">
                        <div class="flex items-center">
                            <button data-modal-target="medium-offre{{ $produit->id }}"
                                data-modal-toggle="medium-offre{{ $produit->id }}"
                                class="w-full mt-3 bg-green-500 text-white py-2 mr- rounded-xl" type="button">
                                Faire une Offre Simple
                            </button>
                        </div>
                        <div class="flex items-center">
                            <button data-modal-target="medium-offreneg{{ $produit->id }}"
                                data-modal-toggle="medium-offreneg{{ $produit->id }}"
                                class="w-full mt-3 bg-yellow-300 text-white py-2 mr- rounded-xl" type="button">
                                Faire une Offre Negociée
                            </button>
                        </div>
                        <div class="flex items-center">
                            <button data-modal-target="medium-offreGrp{{ $produit->id }}"
                                data-modal-toggle="medium-offreGrp{{ $produit->id }}"
                                class="w-full mt-3 bg-blue-600 text-white py-2 mr- rounded-xl" type="button">
                                Faire une Offre Groupée
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Offre Simple -->
        <div id="medium-offre{{ $produit->id }}" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-lg max-h-full">

                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                            Offre Simple
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="medium-offre{{ $produit->id }}">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Contenu principal -->
                    <div class="p-6 sm:p-10 text-center">

                        <p class="text-gray-600 dark:text-neutral-400">
                            Le nombre de clients potentiels est <span
                                class="font-semibold text-blue-600 dark:text-blue-400">({{ $nombreProprietaires }})</span>
                        </p>
                        <form wire:submit.prevent="sendOffre">
                            @csrf

                            <!-- Sélection de la zone économique -->
                            <div class="mb-4">
                                <label for="zone_economique"
                                    class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Zone économique
                                </label>
                                <select wire:model="zoneEconomique" id="zone_economique" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                                    <option value="proximite">Proximité</option>
                                    <option value="locale">Locale</option>
                                    <option value="departementale">Départementale</option>
                                    <option value="nationale">Nationale</option>
                                    <option value="sous_regionale">Sous-régionale</option>
                                    <option value="continentale">Continentale</option>
                                </select>
                            </div>

                            <!-- Boutons -->
                            <div class="flex justify-center gap-4">

                                <button data-modal-hide="medium-offre{{ $produit->id }}" type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I
                                    soumettre</button>
                                <button data-modal-hide="medium-offre{{ $produit->id }}" type="button"
                                    class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!--  Offre Negocier-->
        <div id="medium-offreneg{{ $produit->id }}" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-lg max-h-full">

                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                            Offre Negocier
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="medium-offreneg{{ $produit->id }}">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Contenu principal -->
                    <div class="p-6 sm:p-10 text-center">

                        <p class="text-gray-600 dark:text-neutral-400">
                            Le nombre de clients potentiels est <span
                                class="font-semibold text-blue-600 dark:text-blue-400">({{ $nombreProprietaires }})</span>
                        </p>
                        <form wire:submit.prevent="sendoffneg">
                            @csrf

                            <!-- Sélection de la zone économique -->
                            <div class="mb-4">
                                <label for="zone_economique"
                                    class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Zone économique
                                </label>
                                <select wire:model="zoneEconomique" id="zone_economique" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                                    <option value="proximite">Proximité</option>
                                    <option value="locale">Locale</option>
                                    <option value="departementale">Départementale</option>
                                    <option value="nationale">Nationale</option>
                                    <option value="sous_regionale">Sous-régionale</option>
                                    <option value="continentale">Continentale</option>
                                </select>
                            </div>

                            <!-- Boutons -->
                            <div class="flex justify-center gap-4">

                                <button data-modal-hide="medium-offreneg{{ $produit->id }}" type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I
                                    soumettre</button>
                                <button data-modal-hide="medium-offreneg{{ $produit->id }}" type="button"
                                    class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!--  Offre Grouper-->
        <div id="medium-offreGrp{{ $produit->id }}" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-lg max-h-full">

                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                            Offre Grouper
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="medium-offreGrp{{ $produit->id }}">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Contenu principal -->
                    <div class="p-6 sm:p-10 text-center">

                        <p class="text-gray-600 dark:text-neutral-400">
                            Le nombre de fournisseurs potentiels est <span
                                class="font-semibold text-blue-600 dark:text-blue-400">({{ $nomFournisseurCount }})</span>
                        </p>
                        <form wire:submit.prevent="sendoffreGrp">
                            <!-- Quantité -->
                            <div>
                                <label for="quantite"
                                    class="block text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    Ajoutez votre quantité
                                </label>
                                <input wire:model="quantite" name="quantite" id="quantite" type="number" min="1" required
                                    placeholder="Entrez une quantité"
                                    class="mt-2 block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-500 placeholder-gray-400">
                            </div>

                            <!-- Nom d'utilisateur -->
                            <div>
                                <label for="username"
                                    class="block text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    Entrez le username du client ciblé
                                </label>
                                <input wire:model="username" name="username" id="username" type="text" required
                                    placeholder="Username du client ciblé"
                                    class="mt-2 block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-500 placeholder-gray-400">
                            </div>

                            <!-- Zone économique -->
                            <div>
                                <label for="zone_economique"
                                    class="block text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    Zone économique
                                </label>
                                <select wire:model="zoneEconomique" name="zone_economique" id="zone_economique" required
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-500">
                                    <option value="" disabled selected>Sélectionnez une zone</option>
                                    <option value="proximite">Proximité</option>
                                    <option value="locale">Locale</option>
                                    <option value="departementale">Départementale</option>
                                    <option value="nationale">Nationale</option>
                                    <option value="sous_regionale">Sous-régionale</option>
                                    <option value="continentale">Continentale</option>
                                </select>
                            </div>

                            <!-- Boutons -->
                            <div class="flex justify-center gap-4">
                                <button type="submit" data-modal-hide="medium-offreGrp{{ $produit->id }}"
                                    @if ($nombreProprietaires == 0) disabled @endif
                                    class="inline-flex items-center px-6 py-3 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Soumettre
                                </button>
                                <button type="button"
                                    class="inline-flex items-center px-6 py-3 rounded-md text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700"
                                    data-modal-hide="medium-offreGrp{{ $produit->id }}">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>


    </div>
</div>
