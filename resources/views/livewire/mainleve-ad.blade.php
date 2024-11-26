<div>
    <!-- Main livreur -->

    @if ($this->notification->data['livreur'])
        <div class="flex justify-center items-center min-h-screen bg-blue-50">
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
                <div
                    class="mt-6 p-4 bg-blue-100 border border-blue-300 rounded-lg dark:bg-blue-900 dark:border-blue-700">
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
            <div class="max-w-4xl p-8 mx-auto mt-6 bg-white rounded-lg shadow-lg">
                <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">Information sur le Client</h2>
                <div class="flex items-center mb-6">
                    <div class="w-20 h-20 overflow-hidden bg-gray-200 rounded-full">
                        {{-- <img src="{{ asset($client->photo) }}" alt="Photo du client" class="object-cover w-full h-full"> --}}
                    </div>
                    <div class="ml-6">
                        <p class="text-lg font-medium text-gray-800">Nom du client: <span class="font-semibold">Jean
                                Dupont</span></p>
                        {{-- <p class="text-lg font-medium text-gray-800">Adresse du client: <span class="font-semibold">{{ $client->address }}</span></p> --}}
                        <p class="text-lg font-medium text-gray-800">Contact du client: <span class="font-semibold">06
                                00 00
                                00 00</span></p>
                        {{-- <p class="text-lg font-medium text-gray-800">Engin du client: <span class="font-semibold">Moto</span></p> --}}
                        <p class="text-lg font-medium text-gray-800">Produit à récupérer: <span
                                class="font-semibold">Colis
                                A123</span></p>
                    </div>
                </div>
            </div>
        @endif
    @endif


    @if ($this->notification->data['fournisseur'])
        <section>
            <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl w-full bg-white shadow-lg rounded-xl p-8">
                    <!-- En-tête -->
                    <div class="flex items-center justify-between border-b pb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">Commande a livrer</h1>
                            <p class="mt-1 text-sm text-gray-500">Commande #{{ $notification->data['code_unique'] }}</p>
                        </div>
                        <span class="px-4 py-1 text-sm font-medium bg-red-100 text-red-700 rounded-full">
                            Statut : en cours
                        </span>
                    </div>

                    <!-- Informations principales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <!-- Informations sur le fournisseur -->
                        <div class="p-4 bg-gray-50 rounded-lg shadow-inner">
                            <h2 class="text-lg font-semibold text-gray-800">Fournisseur</h2>
                            <div class="mt-2 text-sm text-gray-600 space-y-1">
                                <p><span class="font-medium">Nom : </span>{{ $achatdirect->userTraderI->name }}</p>
                                <p><span class="font-medium">Email : </span>{{ $achatdirect->userTraderI->email }}</p>
                                <p><span class="font-medium">Téléphone : </span>{{ $achatdirect->userTraderI->phone }}
                                </p>
                                <p class="bg-green-100 text-green-700 text-sm rounded-md p-3">
                                    <span class="font-medium">lieu de recuperation :
                                    </span>{{ $achatdirect->userTraderI->commune }}
                                </p>
                            </div>
                        </div>

                        <!-- Informations sur le client -->
                        <div class="p-4 bg-gray-50 rounded-lg shadow-inner">
                            <h2 class="text-lg font-semibold text-gray-800">Client</h2>
                            <div class="mt-2 text-sm text-gray-600 space-y-1">
                                <p><span class="font-medium">Nom : </span>{{ $achatdirect->userSenderI->name }}</p>
                                <p><span class="font-medium">Email : </span>{{ $achatdirect->userSenderI->email }}</p>
                                <p><span class="font-medium">Téléphone : </span>{{ $achatdirect->userSenderI->phone }}
                                </p>
                                <p class="bg-green-100 text-green-700 text-sm rounded-md p-3">
                                    <span class="font-medium">lieu a livrer :
                                    </span>{{ $achatdirect->localite }}
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Informations sur le produit/service -->
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-800">Produit / Service commandé</h2>
                        <div class="mt-4 flex items-center bg-gray-50 p-4 rounded-lg shadow-inner">
                            <img src="{{ $achatdirect->photoProd ? asset('post/all/' . $achatdirect->photoProd) : asset('img/noimg.jpeg') }}"
                                alt="Image produit" class="w-20 h-20 object-cover rounded-lg">
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900">Produit : {{ $produit->name }}</h3>
                                <p class="mt-1 text-sm text-gray-600">Quantité commandée:
                                    {{ $achatdirect->quantité }}({{ $produit->condProd }})</p>
                                <p class="mt-1 text-sm text-gray-600">Prix unitaire : {{ $produit->prix }} FCFA</p>
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($achatdirect->montantTotal, 2, ',', '.') }} FCFA
                            </div>
                        </div>
                    </div>

                    <!-- Résumé de la commande -->
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-800">Résumé de la commande du
                            {{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d F Y') }}</h2>
                        <div class="mt-4 bg-gray-50 p-4 rounded-lg shadow-inner space-y-3">

                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Frais de livraison :
                                </span>
                                <span>{{ number_format($achatdirect->montantTotal, 2, ',', '.') }}
                                    FCFA</span>
                            </div>
                            <div class="flex justify-between font-medium text-gray-900">
                                <span>Total :</span>
                                <span>
                                    FCFA</span>
                            </div>
                        </div>
                    </div>


                    <!-- Boutons -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <div class="flex justify-center items-center mt-4">
                            <button data-modal-target="static-modal" data-modal-toggle="static-modal"
                                class="flex items-center space-x-2 px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                                </svg>
                                <span>Procéder à la main levée</span>
                            </button>
                        </div>
                        <!-- Main modal -->
                        <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
                            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-2xl max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow-lg dark:bg-gray-800">
                                    <!-- Modal header -->
                                    <div
                                        class="flex items-center justify-between p-6 bg-blue-600 rounded-t-lg text-white dark:bg-blue-700">
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
                                                    <input type="radio" id="quantite-oui" name="quantite"
                                                        value="oui" wire:model="quantite"
                                                        class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                                                    <label for="quantite-oui"
                                                        class="mr-4 text-gray-700 dark:text-gray-300">OUI</label>
                                                    <input type="radio" id="quantite-non" name="quantite"
                                                        value="non" wire:model="quantite"
                                                        class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                                                    <label for="quantite-non"
                                                        class="text-gray-700 dark:text-gray-300">NON</label>
                                                </div>
                                                <!-- Qualité apparante -->
                                                <div class="flex items-center">
                                                    <label class="mr-3 text-gray-700 dark:text-gray-300">Qualité
                                                        apparante
                                                        :</label>
                                                    <input type="radio" id="qualite-oui" name="qualite"
                                                        value="oui" wire:model="qualite"
                                                        class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                                                    <label for="qualite-oui"
                                                        class="mr-4 text-gray-700 dark:text-gray-300">OUI</label>
                                                    <input type="radio" id="qualite-non" name="qualite"
                                                        value="non" wire:model="qualite"
                                                        class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                                                    <label for="qualite-non"
                                                        class="text-gray-700 dark:text-gray-300">NON</label>
                                                </div>
                                                <!-- diversite apparante -->
                                                <div class="flex items-center">
                                                    <label class="mr-3 text-gray-700 dark:text-gray-300">Diversite
                                                        :</label>
                                                    <input type="radio" id="diversite-oui" name="diversite"
                                                        value="oui" wire:model="diversite"
                                                        class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                                                    <label for="diversite-oui"
                                                        class="mr-4 text-gray-700 dark:text-gray-300">OUI</label>
                                                    <input type="radio" id="diversite-non" name="diversite"
                                                        value="non" wire:model="diversite"
                                                        class="mr-2 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-500 dark:focus:ring-offset-gray-800">
                                                    <label for="diversite-non"
                                                        class="text-gray-700 dark:text-gray-300">NON</label>
                                                </div>

                                                <!-- Code de vérification -->
                                                <div
                                                    class="mt-6 p-4 bg-blue-100 border border-blue-300 rounded-lg dark:bg-blue-900 dark:border-blue-700">
                                                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-400">
                                                        Code de vérification</h3>
                                                    <p class="text-xl font-bold text-blue-900 dark:text-white">
                                                        {{ $codeVerification ?? 'N/A' }}
                                                    </p>
                                                </div>
                                                <p class="text-sm text-red-500 mt-4">Veuillez sélectionner au moins
                                                    deux réponses "OUI" pour effectuer l'action.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div
                                        class="flex justify-end p-6 border-t border-gray-200 rounded-b dark:border-gray-600">
                                        <button data-modal-hide="static-modal" type="button" wire:click='mainleve'
                                            class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                                            J'accepte
                                        </button>
                                        <button data-modal-hide="static-modal" type="button"
                                            wire:click='refuseVerif'
                                            class="ml-4 px-6 py-2.5 bg-gray-200 text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-300 focus:ring-4 focus:ring-gray-400 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                            Je refuse
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </section>
        <div class="space-y-8 max-w-4xl mx-auto p-6  min-h-screen">

            <!-- Section Programmation -->
            <section class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="h-6 w-6 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10m-4 9a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v12z" />
                    </svg>
                    Programmer la livraison
                </h2>

                <form wire:submit.prevent="departlivr" class="space-y-4">
                    <div class="space-y-2">
                        <label for="date" class="block text-sm font-medium text-gray-700">Date de
                            livraison</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10m-4 9a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v12z" />
                            </svg>
                            <input type="date" id="date" name="date" wire:model.defer="dateLivr"
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="time" class="block text-sm font-medium text-gray-700">Heure de
                            livraison</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m4 0H8m16 4a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v9z" />
                            </svg>
                            <input type="time" id="time" name="time" wire:model.defer="time"
                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-purple-500 text-white py-3 px-4 rounded-lg flex items-center justify-center gap-2 hover:bg-purple-600 transition-colors">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Confirmer la programmation
                    </button>
                </form>
            </section>

        </div>
    @endif
    {{-- <script>
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
    </script> --}}
</div>
