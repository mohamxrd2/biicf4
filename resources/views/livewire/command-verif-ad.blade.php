<div class="max-w-4xl p-6 mx-auto bg-white rounded-lg shadow-lg">
    <div class="max-w-4xl p-6 mx-auto bg-white rounded-lg shadow-lg">
        <h2 class="mb-2 text-xl font-semibold">Informations Sur Le Fournisseur</h2>
        <div class="p-4 bg-gray-100 rounded-lg">
            <p class="mb-2">Nom du fournisseur: <span
                    class="font-semibold">{{ $namefourlivr->user->name }}</span>
            </p>
            <p class="mb-2">Adresse du fournisseur: <span
                    class="font-semibold">{{ $namefourlivr->user->address }}</span>
            </p>
            <p class="mb-2">Email du founisseur: <span
                    class="font-semibold">{{ $namefourlivr->user->email }}</span>
            </p>
            <p class="mb-2">Téléphone founisseur: <span
                    class="font-semibold">{{ $namefourlivr->user->phone }}</span>
            </p>
            <p class="mb-2">Code de Vérification : <span
                    class="font-semibold">{{ $notification->data['code_unique'] }}</span>
            </p>
        </div>
    </div>
    <div class="max-w-4xl p-6 mx-auto mt-3 bg-white rounded-lg shadow-lg">
        <h2 class="my-2 text-xl font-semibold">Avis de conformité</h2>

        <div class="space-y-3">
            <!-- Quantité -->
            <div class="flex items-center mb-3">
                <label class="mr-2 text-gray-600 dark:text-neutral-400">Quantité :</label>
                <input type="radio" id="quantite-oui" name="quantite" value="oui"
                    class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="quantite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                <input type="radio" id="quantite-non" name="quantite" value="non"
                    class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="quantite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
            </div>

            <!-- Qualité Apparente -->
            <div class="flex items-center mb-3">
                <label class="mr-2 text-gray-600 dark:text-neutral-400">Qualité Apparente :</label>
                <input type="radio" id="qualite-oui" name="qualite" value="oui"
                    class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="qualite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                <input type="radio" id="qualite-non" name="qualite" value="non"
                    class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="qualite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
            </div>

            <!-- Diversité -->
            <div class="flex items-center mb-3">
                <label class="mr-2 text-gray-600 dark:text-neutral-400">Diversité :</label>
                <input type="radio" id="diversite-oui" name="diversite" value="oui"
                    class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="diversite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                <input type="radio" id="diversite-non" name="diversite" value="non"
                    class="mr-2 text-blue-600 border-gray-200 rounded shrink-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="diversite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
            </div>
        </div>




    </div>

    <div class="flex max-w-4xl mt-6">
        @if ($notification->reponse)
            <div class="p-2 bg-gray-300 border rounded-md ">
                <p class="font-medium text-center text-md">Réponse envoyée</p>
            </div>
        @else
            <button wire:click='mainleve'
                class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md"><svg
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="mr-2 size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                </svg>

                <span wire:loading.remove>
                    Léver la main
                </span>
                <span wire:loading>
                    Chargement...
                    <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                    </svg>
                </span>
            </button>
            <button wire:click='refuseVerif' class="flex p-2 font-medium text-white bg-red-700 rounded-md"><svg
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="mr-2 size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                </svg>
                <span wire:loading.remove>
                    Refuser
                </span>
                <span wire:loading>
                    Chargement...
                    <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                    </svg>
                </span>

            </button>
        @endif
    </div></div>
