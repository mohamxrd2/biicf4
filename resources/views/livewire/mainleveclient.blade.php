<div>
    <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
        <h2 class="mb-4 text-xl font-semibold">Estimation de reception du colis</h2>

        <p class="text-md">Date : <span
                class="font-semibold">{{ \Carbon\Carbon::parse($notification->data['date_livr'] )->translatedFormat('d F Y') }} (Heure:
                {{ $notification->data['time'] }} )</span>
        </p>

    </div>
    <div class="flex justify-center items-center min-h-screen ">
        <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">
            <!-- Titre -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Vérification Code Livreur</h1>
                <p class="text-gray-600">Entrez le code du livreur pour vérifier sa validité</p>
            </div>

            <!-- Formulaire -->
            <form wire:submit.prevent="verifyCode">
                <!-- Champ Code Livre -->
                <div class="mb-4">
                    <input type="text" name="code_verif" wire:model.defer="code_verif"
                        placeholder="Entrez le code livreur"
                        class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>

                <!-- Bouton Vérifier -->
                <div class="mb-6">
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span wire:loading.remove>Vérifier le code</span>
                        <span wire:loading>
                            <svg class="inline-block w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                            </svg>
                        </span>
                    </button>
                </div>
                @error('code_verif')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </form>

            <!-- Instructions -->
            <div class="text-sm text-gray-600">
                <h3 class="font-semibold mb-2">Instructions</h3>
                <ul class="list-disc pl-5">
                    <li>Le code livreur doit contenir 4 chiffres</li>
                    <li>Vérifiez que le code correspond à votre bon de livraison</li>
                    <li>En cas de problème, contactez le support</li>
                </ul>
            </div>
            <div class="mt-6 p-4 bg-blue-100 border border-blue-300 rounded-lg dark:bg-blue-900 dark:border-blue-700">
                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-400">
                    Code de vérification</h3>
                <p class="text-xl font-bold text-blue-900 dark:text-white">
                    {{ $this->notification->data['fournisseurCode'] ?? 'N/A' }}
                </p>
            </div>
            @if (session()->has('succes'))
                <div class="p-4 mt-4 text-green-700 bg-green-100 rounded-lg">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 mt-4 text-red-700 bg-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>


    @if (session()->has('succes'))
        <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Information sur le client</h2>

            <div class="flex-col w-full ">
                <div class="w-20 h-20 mb-6 mr-4 overflow-hidden bg-gray-100 rounded-full">

                    <img src="{{ asset($achatdirect->userSenderI->photo) }}" alt="photot" class="">

                </div>

                <div class="flex flex-col">
                    <p class="mb-3 text-md">Nom du livreur: <span
                            class="font-semibold ">{{ $achatdirect->userSenderI->name }}</span>
                    </p>
                    <p class="mb-3 text-md">Adress du livreur: <span
                            class="font-semibold ">{{ $achatdirect->userSenderI->commune }}</span></p>
                    <p class="mb-3 text-md">Contact du client: <span
                            class="font-semibold ">{{ $achatdirect->userSenderI->phone }}</span></p>
                    <p class="mb-3 text-md">Produit à recuperer: <span
                            class= "font-semibold ">{{ $achatdirect->nameProd }}</span></p>
                </div>


            </div>
        </div>
    @endif

    @if (session()->has('succes'))
        <div class="relative bg-white rounded-lg shadow-lg dark:bg-gray-800">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-6 bg-blue-600 rounded-t-lg text-white dark:bg-blue-700">
                <h3 class="text-xl font-semibold">Main Levée</h3>

            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <div class="max-w-4xl p-4 bg-gray-50 rounded-lg shadow-md dark:bg-gray-700">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Avis
                        de
                        conformité</h2>

                    <div class="space-y-4">
                        <!-- Quantité -->
                        <div class="flex items-center">
                            <label class="mr-3 text-gray-700 dark:text-gray-300">Quantité
                                :</label>
                            <input type="radio" id="quantite-oui" name="quantite" value="oui" wire:model="quantite"
                                class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                            <label for="quantite-oui" class="mr-4 text-gray-700 dark:text-gray-300">OUI</label>
                            <input type="radio" id="quantite-non" name="quantite" value="non" wire:model="quantite"
                                class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                            <label for="quantite-non" class="text-gray-700 dark:text-gray-300">NON</label>
                        </div>
                        <!-- Qualité apparante -->
                        <div class="flex items-center">
                            <label class="mr-3 text-gray-700 dark:text-gray-300">Qualité
                                apparante
                                :</label>
                            <input type="radio" id="qualite-oui" name="qualite" value="oui" wire:model="qualite"
                                class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                            <label for="qualite-oui" class="mr-4 text-gray-700 dark:text-gray-300">OUI</label>
                            <input type="radio" id="qualite-non" name="qualite" value="non" wire:model="qualite"
                                class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                            <label for="qualite-non" class="text-gray-700 dark:text-gray-300">NON</label>
                        </div>
                        <!-- diversite apparante -->
                        <div class="flex items-center">
                            <label class="mr-3 text-gray-700 dark:text-gray-300">Diversite
                                :</label>
                            <input type="radio" id="diversite-oui" name="diversite" value="oui"
                                wire:model="diversite"
                                class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                            <label for="diversite-oui" class="mr-4 text-gray-700 dark:text-gray-300">OUI</label>
                            <input type="radio" id="diversite-non" name="diversite" value="non"
                                wire:model="diversite"
                                class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                            <label for="diversite-non" class="text-gray-700 dark:text-gray-300">NON</label>
                        </div>

                        <!-- Code de vérification -->
                        <div
                            class="mt-6 p-4 bg-blue-100 border border-blue-300 rounded-lg dark:bg-blue-900 dark:border-blue-700">
                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-400">
                                Code de vérification</h3>
                            <p class="text-xl font-bold text-blue-900 dark:text-white">
                                {{ $notification->data['livreurCode'] ?? 'N/A' }}
                            </p>
                        </div>
                        <p class="text-sm text-red-500 mt-4">Veuillez sélectionner au moins
                            deux réponses "OUI" pour effectuer l'action.</p>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex justify-end p-6 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="static-modal" type="button" wire:click='acceptColis'
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                    J'accepte
                </button>
                <button data-modal-hide="static-modal" type="button" wire:click='refuseColis'
                    class="ml-4 px-6 py-2.5 bg-gray-200 text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-400 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                    Je refuse
                </button>
            </div>
        </div>
    @endif
</div>
