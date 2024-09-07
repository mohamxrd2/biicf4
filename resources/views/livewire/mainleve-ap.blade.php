<div>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-3">

        <h2 class="text-xl font-semibold mb-2">Information sur le produit à enlevé et livré</h2>

        <div class="bg-gray-100 p-4 rounded-lg">
            <p class="mb-2">Nom du produit: <span class="font-semibold">{{ $produitfat->name }}</span></p>
            <p class="mb-2">Quantité: <span class="font-semibold">{{ $notification->data['quantite'] }}</span>
            </p>
            <p class="mb-2">Code de livraison: <span
                    class="font-semibold">{{ $notification->data['code_unique'] }}</span></p>
            <p class="mb-2">Téléphone founisseur: <span class="font-semibold">{{ $namefourlivr->user->phone }}</span>
            </p>
            <p class="mb-2">Email founisseur: <span class="font-semibold">{{ $namefourlivr->user->email }}</span>
            </p>
            @php
                $produits = \App\Models\ProduitService::find($idProd);
                $address = $produits->comnServ;
                $clients = \App\Models\User::find($notification->data['id_client']);
                $clientsadress = $clients->address;
            @endphp
            <p class="mb-2">Lieu d'enlevement: <span class="font-semibold">{{ $address }}</span>
            </p>
            <p class="mb-2">Lieu de livraison: <span class="font-semibold">{{ $clientsadress }}</span></p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">

        <h2 class="text-xl font-semibold mb-2">Avis de conformité</h2>

        <div class="space-y-3">
            <!-- Quantité -->
            <div class="flex items-center mb-3">
                <label class="mr-2 text-gray-600 dark:text-neutral-400">Quantité :</label>
                <input type="radio" id="quantite-oui" name="quantite" value="oui"
                    class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="quantite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                <input type="radio" id="quantite-non" name="quantite" value="non"
                    class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="quantite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
            </div>

            <!-- Qualité Apparente -->
            <div class="flex items-center mb-3">
                <label class="mr-2 text-gray-600 dark:text-neutral-400">Qualité Apparente :</label>
                <input type="radio" id="qualite-oui" name="qualite" value="oui"
                    class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="qualite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                <input type="radio" id="qualite-non" name="qualite" value="non"
                    class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="qualite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
            </div>

            <!-- Diversité -->
            <div class="flex items-center mb-3">
                <label class="mr-2 text-gray-600 dark:text-neutral-400">Diversité :</label>
                <input type="radio" id="diversite-oui" name="diversite" value="oui"
                    class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="diversite-oui" class="mr-4 text-gray-600 dark:text-neutral-400">OUI</label>
                <input type="radio" id="diversite-non" name="diversite" value="non"
                    class="shrink-0 mr-2 border-gray-200 rounded text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                <label for="diversite-non" class="text-gray-600 dark:text-neutral-400">NON</label>
            </div>
        </div>

    </div>

    <form wire:submit.prevent="departlivr" method="POST">
        @csrf

        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mb-4">
            <h2 class="text-xl font-semibold mb-2">Estimation de date de livraison <span class="text-red-700">*</span>
                <span class="font-medium">Date prévue de récupération du client:</span>
                <span>
                    @if (isset($notification->data['date_tot']) && isset($notification->data['date_tard']))
                        {{ $notification->data['date_tot'] }} - {{ $notification->data['date_tard'] }}
                    @else
                        Non spécifiée
                    @endif
                </span>
            </h2>

            <div class="lg:w-1/2 w-full mr-2 relative">
                <input type="date" id="datePickerStart" name="dateLivr" wire:model.defer="dateLivr" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Ajouter une date de livraison">
                @error('dateLivr')
                    <span class="text-red-500 mt-4">{{ $message }}</span>
                @enderror
            </div>


            <!-- Select -->
            <div class="lg:w-1/2 w-full mr-2 relative mt-4">
                <select id="select" wire:model.defer="matine" name="matine"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" disabled selected>Choisir la période de la journée</option>
                    <option value="Matin">Matin</option>
                    <option value="Apres-midi">Après-midi</option>
                    <option value="Soir">Soir</option>
                </select>


                @error('matine')
                    <span class="text-red-500 mt-4">{{ $message }}</span>
                @enderror
            </div>

            <!-- End Select -->
        </div>

        <div class="max-w-4xl mx-auto flex rounded-lg mb-4">
            @if ($notification->reponse)
                <div class="bg-gray-300 border p-2 rounded-md">
                    <p class="text-md font-medium text-center">Réponse envoyée</p>
                </div>
            @else
                <button type="submit" class="p-2 flex text-white font-medium bg-green-700 rounded-md mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>

                    <span wire:loading.remove>
                        Livré
                    </span>
                    <span wire:loading>
                        Chargement...
                        <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                        </svg>
                    </span>
                </button>

                <button wire:click='refuseVerifLivreur'
                    class="p-2 text-white flex font-medium bg-red-700 rounded-md"><svg
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                    </svg>
                    <span wire:loading.remove>
                        Refuser
                    </span>
                    <span wire:loading>
                        Chargement...
                        <svg class="w-5 h-5 animate-spin inline-block ml-2" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                        </svg>
                    </span>

                </button>
            @endif
        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateLivrInput = document.querySelector('input[name="dateLivr"]');
            const startDate = new Date("{{ $notification->data['date_tot'] }}");
            const endDate = new Date("{{ $notification->data['date_tard'] }}");

            dateLivrInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);

                if (selectedDate < startDate || selectedDate > endDate) {
                    alert('La date de livraison doit être dans l\'intervalle spécifié.');
                    this.value = ''; // Réinitialiser le champ si la date est invalide
                }
            });
        });
    </script>
</div>
