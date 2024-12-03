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
                            <button class="w-full mt-3 bg-green-500 text-white py-2 mr- rounded-xl"
                                data-hs-overlay="#hs-offre-{{ $produit->id }}">faire une offre
                            </button>
                        </div>
                        <div class="flex items-center">
                            <button class="w-full mt-3 bg-yellow-300 text-white py-2 mr- rounded-xl"
                                data-hs-overlay="#hs-offreNeg-{{ $produit->id }}">faire une offre
                                negocié
                            </button>
                        </div>
                        <div class="flex items-center">
                            <button class="w-full mt-3 bg-blue-600 text-white py-2 mr- rounded-xl"
                                data-hs-overlay="#hs-offreGrp-{{ $produit->id }}">faire une offre
                                Groupé
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div id="hs-offre-{{ $produit->id }}"
            class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
            <div
                class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                <div
                    class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900 transition duration-300 transform hover:scale-105">
                    <!-- Bouton pour fermer -->
                    <div class="absolute top-3 right-3">
                        <button type="button"
                            class="flex justify-center items-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:text-white"
                            data-hs-overlay="#hs-offre-{{ $produit->id }}">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6L6 18" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Contenu principal -->
                    <div class="p-6 sm:p-10 text-center">
                        <h3 class="mb-4 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                            Offre Simple
                        </h3>
                        <p class="text-gray-600 dark:text-neutral-400">
                            Le nombre de clients potentiels est <span
                                class="font-semibold text-blue-600 dark:text-blue-400">({{ $nombreProprietaires }})</span>
                        </p>

                        <!-- Formulaire -->
                        <div class="mt-6">
                            <form action="{{ route('biicf.sendoffre', $produit->id) }}" method="POST">
                                @csrf

                                <!-- Champ caché pour l'ID du produit -->
                                <input type="hidden" name="produit_id" value="{{ $produit->id }}" required>

                                <!-- Sélection de la zone économique -->
                                <div class="mb-4">
                                    <label for="zone_economique"
                                        class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Zone économique
                                    </label>
                                    <select name="zone_economique" id="zone_economique" required
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
                                    <button type="submit" @if ($nombreProprietaires == 0) disabled @endif
                                        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Soumettre
                                    </button>
                                    <button type="button"
                                        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700"
                                        data-hs-overlay="#hs-offre-{{ $produit->id }}">
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
</div>
