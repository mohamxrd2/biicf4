<div>
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
                    <p class="mb-3 text-md">Nom du client: <span
                            class="font-semibold ">{{ $achatdirect->userSenderI->name }}</span>
                    </p>
                    <p class="mb-3 text-md">Adress du client: <span
                            class="font-semibold ">{{ $achatdirect->userSenderI->commune }}</span></p>
                    <p class="mb-3 text-md">Contact du client: <span
                            class="font-semibold ">{{ $achatdirect->userSenderI->phone }}</span></p>
                    <p class="mb-3 text-md">Produit à recuperer: <span
                            class= "font-semibold ">{{ $achatdirect->nameProd }}</span></p>
                </div>


            </div>
        </div>
    @endif
</div>
