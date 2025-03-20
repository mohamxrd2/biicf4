<div>
    <!-- Inclure les fichiers CSS et JavaScript -->
    <link rel="stylesheet" href="{{ asset('css/produit-details.css') }}">
    <script src="{{ asset('js/produit-details.js') }}" defer></script>


    <div class="bg-white rounded-lg p-6 shadow-md border-b">
        <div class="flex items-center justify-between">
            <!-- Stepper -->
            <div class="w-full flex items-center">
                <!-- Step 1 -->
                <div class="relative flex flex-col items-center flex-1">
                    <div
                        class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow-md transform transition duration-300 step-active hover:scale-110">
                        1
                    </div>
                    <p class="text-sm font-medium text-gray-800 mt-2">Détails du produit</p>
                </div>

                <!-- Line -->
                <div class="flex-1 h-1 bg-blue-300 transition duration-300 step-line"></div>

                <!-- Step 2 -->
                <div class="relative flex flex-col items-center flex-1">
                    <div
                        class="w-12 h-12 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold shadow-md transform transition duration-300 hover:scale-110">
                        2
                    </div>
                    <p class="text-sm font-medium text-gray-500 mt-2">Commande</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Section -->
    <section class="bg-white md:py-16 dark:bg-gray-800 antialiased rounded-lg shadow-lg ">
        <div class="px-6 md:px-12 lg:px-20">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                Détails du produit
            </h2>

            <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 ">
                <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">
                    <!-- Images Section -->
                    <div class="flex flex-col items-center space-y-4">
                        <!-- Main Image -->
                        <div class="shrink-0 max-w-md lg:max-w-lg mx-auto">
                            <img id="mainImage" class="w-full dark:hidden rounded-lg shadow-md"
                                src="{{ asset('post/all/' . $produit->photoProd1) }}" alt="Main Product Image" />
                        </div>

                        <!-- Thumbnail Images -->
                        <div class="flex justify-center space-x-4 xs:space-x-1">
                            @php
                                $productPhotos = [
                                    1 => $produit->photoProd1 ?? null,
                                    2 => $produit->photoProd2 ?? null,
                                    3 => $produit->photoProd3 ?? null,
                                    4 => $produit->photoProd4 ?? null,
                                ];
                            @endphp

                            <div class="product-thumbnails flex gap-2">
                                @foreach ($productPhotos as $index => $photo)
                                    @if ($photo)
                                        <img onclick="changeImage('{{ asset('post/all/' . $photo) }}')"
                                            class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-300 rounded-lg hover:shadow-lg transition-transform transform hover:scale-105"
                                            src="{{ asset('post/all/' . $photo) }}" alt="Thumbnail {{ $index }}">
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Product Info Section -->
                    <div class="mt-6 sm:mt-8 lg:mt-0 sm:p-6">
                        <p class="text-xl font-extrabold text-gray-900 sm:text-3xl dark:text-white mr-4">
                            {{ $produit->name }} "
                            @if ($produit->type == 'Produit')
                                {{ $produit->Particularite }}
                            @else
                                {{ $produit->specialite }}
                            @endif

                        </p>

                        <!-- Price and Rating -->
                        <div class="mt-4 sm:items-center sm:gap-4 sm:flex">
                            <p class="text-xl font-bold text-gray-900 sm:text-3xl dark:text-white mr-4"
                                data-price="{{ $produit->prix }}">
                                {{ number_format($produit->prix, 0, ',', ' ') }} FCFA
                            </p>
                            <div class="flex items-center gap-2 mt-2 sm:mt-0">

                                <a href="#"
                                    class="text-sm font-medium leading-none text-gray-900 underline hover:no-underline dark:text-white">
                                    345 Reviews
                                </a>
                            </div>
                        </div>

                        <!-- Bouton pour afficher la section -->
                        <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-4">
                            <button wire:click="achat" wire:loading.attr="disabled"
                                class="flex w-full items-center justify-center py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:ring-4 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                <span wire:loading.remove>Procédez à l'achat.</span>
                                <span wire:loading>Chargement...</span>
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                </svg>
                            </button>
                        </div>

                        <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />

                        <div class="w-full p-3 bg-gray-200 rounded-2xl flex justify-between items-center cursor-pointer"
                            onclick="toggleVisibility()">
                            <p class="font-medium text-sm text-gray-700">Caracteristique</p>
                            <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>

                        <!-- Product Description -->
                        <div id="toggleContent" class="w-full p-3 gap-y-2 hidden mb-4 transition duration-300">
                            <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                <p class="text-sm font-semibold">Localisation</p>
                                <p class="ml-5 text-sm font-medium text-gray-600"> {{ auth()->user()->continent }},
                                    {{ auth()->user()->sous_region }}
                                    {{ auth()->user()->country }}, {{ auth()->user()->departe }},
                                    {{ auth()->user()->ville }},
                                    {{ auth()->user()->commune }}</p>
                            </div>
                            @if ($produit->type == 'Produit')
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Reference</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->reference }}</p>
                                </div>
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Conditionnement</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->condProd }}</p>
                                </div>
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Format</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->formatProd }}</p>
                                </div>
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Quantité traité</p>
                                    <p class="text-sm font-medium text-gray-600">[{{ $produit->qteProd_min }} -
                                        {{ $produit->qteProd_max }}]</p>
                                </div>
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Quantité traité</p>
                                    <p class="text-sm font-medium text-gray-600">
                                        {{ $produit->poids }}</p>
                                </div>

                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Origine</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->origine }}</p>
                                </div>

                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Particularite</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->Particularite }}</p>
                                </div>
                            @endif
                            @if ($produit->type == 'Service')
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Année d'experiance</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->experience }}</p>
                                </div>

                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Specialité</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->specialite }}</p>
                                </div>

                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Description</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->description }}</p>
                                </div>
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Duree minimal</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->duree }}</p>
                                </div>
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Disponibilité</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->disponible }}</p>
                                </div>
                                <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                    <p class="text-sm font-semibold">Lieu du service</p>
                                    <p class="text-sm font-medium text-gray-600">{{ $produit->lieu }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </section>


</div>
