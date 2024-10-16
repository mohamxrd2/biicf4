<div>
    <!-- Image du projet -->
    <div class="flex flex-col justify-center items-center text-center bg-gray-200 p-4 rounded-lg mb-6">
        <h1 class="text-lg font-bold">FINANCEMENT D'UN PROJET</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Section Projet Images -->
            <div class="flex flex-col w-full md:space-x-8 items-center">
                <!-- Main Image -->
                <div class="relative max-w-md lg:max-w-lg mx-auto shadow-lg rounded-lg overflow-hidden">
                    <img id="mainImage"
                        class="w-full object-cover transition duration-300 ease-in-out transform hover:scale-105"
                        src="{{ asset($images[0]) }}" alt="Main Product Image" />
                </div>


                <!-- Thumbnail Images -->
                <div class="flex justify-center space-x-4">
                    @foreach ($images as $image)
                        @if ($image)
                            <!-- Vérifie si l'image existe -->
                            <img onclick="changeImage('{{ asset($image) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-200 rounded-lg transition-transform duration-200 ease-in-out transform hover:scale-105 hover:border-gray-400"
                                src="{{ asset($image) }}" alt="Thumbnail">
                        @endif
                    @endforeach
                </div>
            </div>
            <!-- Détails du projet -->
            <div class="space-y-4">
                <div class="mt-4">
                    <!-- Catégorie du projet -->
                    <div class="flex items-center mb-2">

                        <span class="ml-2 text-xl capitalize font-semibold text-slate-700">{{ $projet->name }}</span>
                    </div>

                    <div
                        class="grid grid-cols-2  gap-4 text-sm border border-gray-300 rounded-lg p-6 shadow-md text-gray-600 mt-4 w-full justify-between">
                        <div class="flex flex-col text-center">
                            <span
                                class="font-semibold text-lg">{{ number_format($notification->data['montant'], 0, ',', ' ') }}
                                FCFA</span>
                            <span class="text-gray-500 text-sm">Montant</span>
                        </div>

                        <!-- Jours Restants -->
                        <div class="flex flex-col text-center">
                            <span
                                class="font-semibold text-lg">{{ $demandeCredit->taux ?? ($projet->taux ?? 'Rate not available') }}%</span>
                            <span class="text-gray-500 text-sm">Taux</span>
                        </div>
                    </div>

                    <div class="flex flex-col py-2 mt-2 items-center">
                        <div class="flex ">

                            <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                                <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                                    src="{{ asset($userDetails->photo) }}" alt="">
                            </div>
                            <div class="ml-2 text-sm font-semibold">
                                <span class="text-gray-500 font-medium mr-2">De</span>{{ $userDetails->name }}
                            </div>

                        </div>
                       
                        <!-- Bouton de déclenchement du modal -->
                        <button data-modal-target="static-modal" data-modal-toggle="static-modal"
                            class="block bg-gray-200 mt-2 hover:bg-blue-800 text-blue-800 hover:text-white font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            type="button">
                            Plus d'informations
                        </button>


                        <!-- Main modal -->
                        <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
                            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-2xl max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <!-- Modal header -->
                                    <div
                                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Projet Details
                                        </h3>
                                        <button type="button"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                            data-modal-hide="static-modal">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="p-4 md:p-5 space-y-4">
                                        <!-- Client Information -->
                                        <div class="bg-white rounded-lg shadow-lg p-8">
                                            <h2 class="text-2xl font-bold mb-6 text-gray-800">Client Information</h2>
                                            <div class="grid grid-cols-3 gap-6">
                                                <div>
                                                    <p class="text-gray-600 font-medium">Client Name:</p>
                                                    <p class="text-gray-800">{{ $userDetails->name }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 font-medium">Email:</p>
                                                    <p class="text-gray-800">{{ $userDetails->email }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 font-medium">Phone Number:</p>
                                                    <p class="text-gray-800">{{ $userDetails->phone }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 font-medium">Credit Score:</p>
                                                    <p class="text-gray-800">{{ $crediScore->ccc }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 font-medium">Address:</p>
                                                    <p class="text-gray-800">
                                                        {{ $userDetails->country }}, {{ $userDetails->ville }},
                                                        {{ $userDetails->departe }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Credit Request Information -->
                                        <div class="bg-white rounded-lg shadow-lg p-8">
                                            <h2 class="text-2xl font-bold mb-6 text-gray-800">Credit Request Information
                                            </h2>
                                            <div class="grid grid-cols-3 gap-6">
                                                <div>
                                                    <p class="text-gray-600 font-medium">Requested Amount:</p>
                                                    <p class="text-gray-800">{{ $notification->data['montant'] }} FCFA
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 font-medium">Credit Duration:</p>
                                                    <p class="text-gray-800">{{ $joursRestants }} months</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 font-medium">Credit Rate:</p>
                                                    <p class="text-gray-800">
                                                        {{ $projet->taux ?? 'Rate not available' }} %
                                                    </p>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="mt-4">
                        @if (session()->has('success'))
                            <p class="bg-green-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('success') }}</p>
                        @endif
                        @if (session()->has('error'))
                            <p class="bg-red-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('error') }}</p>
                        @endif
                    </div>
                    <div class="border border-gray-300 rounded-lg p-6 shadow-md">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">
                            Approuver la demande crédit
                        </h3>
                        <p class="text-gray-600 text-md mb-6">
                            Contribuez a la finalisation de l'achat d'un produit.
                        </p>
                        <!-- Afficher un message si l'objet du financement est 'demande-directe' -->
                        <div class="flex space-x-4">
                            @if ($notification->reponse == 'approved')
                                <div class="text-green-600 font-bold">
                                    Demande de crédit approuvée.
                                </div>
                            @else
                                <!-- Bouton Approuver -->
                                <button id="approveButton" wire:click="approuver({{ $notification->data['montant'] }})"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 transition-colors rounded-md text-white font-medium"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Approuver</span>
                                    <span wire:loading>
                                        <svg class="animate-spin h-5 w-5 text-white inline-block"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v8h8a8 8 0 11-8 8v-8H4z"></path>
                                        </svg>
                                    </span>
                                </button>

                                <!-- Bouton Refuser -->
                                <button id="rejectButton" wire:click="refuser"
                                    class="w-full py-3 bg-red-600 hover:bg-red-700 transition-colors rounded-md text-white font-medium">
                                    Refuser
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
        <!-- Images Section -->

        <div class="container mx-auto py-8 space-y-12">

            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Description</h2>
                <p class="text-gray-800">
                    {{ $projet->description }}
                </p>

            </div>
        </div>
    </div>

</div>

<script>
    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }
</script>
</div>
