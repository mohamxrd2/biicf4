@extends('biicf.layout.navside')

@section('title', 'Details')

@section('content')

    @if (session('success'))
        <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- Afficher les messages d'erreur -->
    @if (session('error'))
        <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
            {{ session('error') }}
        </div>
    @endif

    {{-- <div class="max-w-7xl mx-auto p-4 grid lg:grid-cols-5 gap-4 "> --}}
    <!-- Left Side: Image -->
    {{-- <div class="lg:h-screen fixed-image lg:col-span-3 col-span-5">
            <div data-hs-carousel='{
                "loadingClasses": "opacity-0",
                "isAutoPlay": true
               }'
                class="relative">
                @php
                    $photoCount = 0;
                    if ($produit->photoProd1) {
                        $photoCount++;
                    }
                    if ($produit->photoProd2) {
                        $photoCount++;
                    }
                    if ($produit->photoProd3) {
                        $photoCount++;
                    }
                    if ($produit->photoProd4) {
                        $photoCount++;
                    }
                @endphp
                @if ($photoCount > 0)
                    <div class="hs-carousel relative overflow-hidden w-full  lg:h-screen h-96 rounded-lg">
                        <div
                            class="hs-carousel-body absolute top-0 bottom-0 start-0  flex flex-nowrap transition-transform duration-700 opacity-100">
                            @foreach ([$produit->photoProd1, $produit->photoProd2, $produit->photoProd3, $produit->photoProd4] as $photo)
                                @if ($photo)
                                    <div class="hs-carousel-slide w-full flex-shrink-0">
                                        <div class="flex justify-center bg-gray-100 dark:bg-neutral-900">
                                            <img class="max-w-full h-auto max-h-[500px] rounded-md object-contain"
                                                src="{{ asset('post/all/' . $photo) }}" alt="Image">
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="flex justify-center h-full bg-gray-100 dark:bg-neutral-900">
                        <img class="max-w-50 h-auto rounded-md" src="{{ asset('img/noimg.jpeg') }}" alt="Image">
                    </div>
                @endif
                @if ($photoCount > 1)
                    <button type="button"
                        class="hs-carousel-prev hs-carousel:disabled:opacity-50 disabled:pointer-events-none absolute inset-y-0 start-0 inline-flex justify-center items-center w-[46px] h-full text-gray-800 hover:bg-gray-800/10 rounded-s-lg dark:text-white dark:hover:bg-white/10">
                        <span class="text-2xl" aria-hidden="true">
                            <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"></path>
                            </svg>
                        </span>
                        <span class="sr-only">retour</span>
                    </button>
                    <button type="button"
                        class="hs-carousel-next hs-carousel:disabled:opacity-50 disabled:pointer-events-none absolute inset-y-0 end-0 inline-flex justify-center items-center w-[46px] h-full text-gray-800 hover:bg-gray-800/10 rounded-e-lg dark:text-white dark:hover:bg-white/10">
                        <span class="sr-only">suivant</span>
                        <span class="text-2xl" aria-hidden="true">
                            <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </span>
                    </button>
                    <div class="hs-carousel-pagination flex justify-center absolute bottom-3 start-0 end-0 space-x-2">
                        @foreach ([$produit->photoProd1, $produit->photoProd2, $produit->photoProd3, $produit->photoProd4] as $photo)
                            @if ($photo)
                                <span
                                    class="hs-carousel-active:bg-purple-700 hs-carousel-active:border-purple-700 size-3 border border-gray-400 rounded-full cursor-pointer dark:border-neutral-600 dark:hs-carousel-active:bg-blue-500 dark:hs-carousel-active:border-blue-500"></span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div> --}}

    <!-- Right Side: Product Details -->
    {{-- <div class="lg:h-500px h-auto overflow-y-auto p-4 lg:col-span-2 col-span-5">
            <h2 class="text-3xl font-semibold mb-2">{{ $produit->name }}</h2>
            <p class="text-sm font-medium text-gray-600 mb-7">{{ $produit->villeServ }}, {{ $produit->comnServ }}</p>
            <p class="text-gray-500 mb-8">
                {{ $produit->desrip }}
            </p>
            <p class="text-4xl font-medium text-purple-600 mb-8" data-price="{{ $produit->prix }}">
                {{ number_format($produit->prix, 0, ',', ' ') }} FCFA
                <span class="text-sm text-gray-600 font-medium uppercase">Prix unitaire</span>
            </p>

            <div class="w-full p-3 bg-gray-200 rounded-2xl flex justify-between items-center cursor-pointer mb-4"
                onclick="toggleVisibility()">
                <p class="font-medium text-sm text-gray-700">Caracteristique</p>
                <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <div id="toggleContent" class="w-full p-3 gap-y-2 hidden mb-4">
                @if ($produit->condProd)
                    <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                        <p class="text-sm font-semibold">Conditionnement</p>
                        <p class="text-sm font-medium text-gray-600">{{ $produit->condProd }}</p>
                    </div>
                @endif
                @if ($produit->formatProd)
                    <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                        <p class="text-sm font-semibold">Format</p>
                        <p class="text-sm font-medium text-gray-600">{{ $produit->formatProd }}</p>
                    </div>
                @endif
                @if ($produit->qteProd_min || $produit->qteProd_max)
                    <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                        <p class="text-sm font-semibold">Quantité traité</p>
                        <p class="text-sm font-medium text-gray-600">[{{ $produit->qteProd_min }} -
                            {{ $produit->qteProd_max }}]</p>
                    </div>
                @endif
                @if ($produit->LivreCapProd)
                    <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                        <p class="text-sm font-semibold">Capacité de livré</p>
                        <p class="text-sm font-medium text-gray-600">{{ $produit->LivreCapProd }}</p>
                    </div>
                @endif
                @if ($produit->qalifServ)
                    <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                        <p class="text-sm font-semibold">Année d'experiance</p>
                        <p class="text-sm font-medium text-gray-600">{{ $produit->qalifServ }}</p>
                    </div>
                @endif
                @if ($produit->sepServ)
                    <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                        <p class="text-sm font-semibold">Specialité</p>
                        <p class="text-sm font-medium text-gray-600">{{ $produit->sepServ }}</p>
                    </div>
                @endif
                @if ($produit->qteServ)
                    <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                        <p class="text-sm font-semibold">Nombre de personnel</p>
                        <p class="text-sm font-medium text-gray-600">{{ $produit->qteServ }}</p>
                    </div>
                @endif
            </div>
            @if ($produit->user_id != $user->id)
                <div class="w-full flex">
                    <button class="w-full bg-purple-500 text-white py-2 mr- rounded-xl" id="btnAchatDirect">Achat
                        direct</button>

                </div>
            @else
                <button class="w-full bg-red-500 text-white py-2 mr- rounded-xl"
                    data-hs-overlay="#hs-delete-{{ $produit->id }}">Supprimé produit</button>

                <p class="text-md mt-4 text-gray-600 font-medium">Fonctionalitées</p>

                <button class="w-full mt-3 bg-green-500 text-white py-2 mr- rounded-xl"
                    data-hs-overlay="#hs-offre-{{ $produit->id }}">faire une offre </button>

                <button class="w-full mt-3 bg-yellow-300 text-white py-2 mr- rounded-xl"
                    data-hs-overlay="#hs-offreNeg-{{ $produit->id }}">faire une offre negocié </button>

                <button class="w-full mt-3 bg-blue-600 text-white py-2 mr- rounded-xl"
                    data-hs-overlay="#hs-offreGrp-{{ $produit->id }}">faire une offre Groupé </button> --}}

    {{-- <button class="w-full mt-3 bg-purple-600 text-white py-2 mr- rounded-xl"
                    data-hs-overlay="#hs-offreGrpNeg-{{ $produit->id }}">faire une offre Groupé negocié</button> --}}

    {{-- <div id="hs-offreGrpNeg-{{ $produit->id }}"
                    class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                    <div
                        class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                        <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                            <div class="absolute top-2 end-2">
                                <button type="button"
                                    class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                    data-hs-overlay="#hs-offreGrpNeg-{{ $produit->id }}">
                                    <span class="sr-only">Close</span>
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                <h3 class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                    Offre groupé negocié
                                </h3>
                                <p class="text-gray-500 dark:text-neutral-500">
                                    le nombre de fornisseur potentiels est ({{ $nomFournisseurCount }})
                                </p>

                                <div class="mt-6 flex justify-center gap-x-4">
                                    <form action="{{ route('biicf.sendoffreneg', $produit->id) }}" method="POST">
                                        @csrf
                                        @method('POST')

                                        <input type="hidden" name="differance" value="groupe">

                                        <!-- Champ caché pour l'ID du produit -->
                                        <input type="hidden" name="produit_id" value="{{ $produit->id }}">

                                        <button type="submit" @if ($nomFournisseurCount == 0) disabled @endif
                                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                            soumettre
                                        </button>
                                    </form>

                                    <button type="button"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                        data-hs-overlay="#hs-offreGrpNeg-{{ $produit->id }}">
                                        Annuler
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div> --}}


    {{-- <div id="hs-delete-{{ $produit->id }}"
                    class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                    <div
                        class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                        <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                            <div class="absolute top-2 end-2">
                                <button type="button"
                                    class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                    data-hs-overlay="#hs-delete-{{ $produit->id }}">
                                    <span class="sr-only">Close</span>
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                <!-- Icon -->
                                <span
                                    class="mb-4 inline-flex justify-center items-center size-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500 dark:bg-yellow-700 dark:border-yellow-600 dark:text-yellow-100">
                                    <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                    </svg>
                                </span>
                                <!-- End Icon -->

                                <h3 class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                    Supprimé
                                </h3>
                                <p class="text-gray-500 dark:text-neutral-500">
                                    Vous etes sur de supprimé le produit ?
                                </p>

                                <div class="mt-6 flex justify-center gap-x-4">
                                    <form action="{{ route('biicf.pubdeleteBiicf', $produit->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                            Supprimer
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                        data-hs-overlay="#hs-delete-{{ $produit->id }}">
                                        Annuler
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
                        <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                            <div class="absolute top-2 end-2">
                                <button type="button"
                                    class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                    data-hs-overlay="#hs-offre-{{ $produit->id }}">
                                    <span class="sr-only">Close</span>
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                <h3 class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                    Offre Simple
                                </h3>
                                <p class="text-gray-500 dark:text-neutral-500">
                                    le nombre de client potentiel est ({{ $nombreProprietaires }})
                                </p>

                                <div class="mt-6 flex justify-center gap-x-4">
                                    <form action="{{ route('biicf.sendoffre', $produit->id) }}" method="POST">
                                        @csrf
                                        @method('POST')

                                        <select name="zone_economique"
                                            class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500 dark:border-neutral-800 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            required>
                                            <option disabled selected>Zone économique</option>
                                            <option value="proximite">Proximité</option>
                                            <option value="locale">Locale</option>
                                            <option value="departementale">Départementale</option>
                                            <option value="nationale">Nationale</option>
                                            <option value="sous_regionale">Sous Régionale</option>
                                            <option value="continentale">Continentale</option>
                                        </select>

                                        <!-- Champ caché pour l'ID du produit -->
                                        <input type="hidden" name="produit_id" value="{{ $produit->id }}">

                                        <button type="submit" @if ($nombreProprietaires == 0) disabled @endif
                                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                            soumettre
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                        data-hs-overlay="#hs-offre-{{ $produit->id }}">
                                        Annuler
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div id="hs-offreGrp-{{ $produit->id }}"
                    class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                    <div
                        class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                        <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                            <div class="absolute top-2 end-2">
                                <button type="button"
                                    class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                    data-hs-overlay="#hs-offreGrp-{{ $produit->id }}">
                                    <span class="sr-only">Close</span>
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                <h3 class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                    Offre Groupé
                                </h3>
                                <p class="text-gray-500 dark:text-neutral-500">
                                    le nombre de fournisseur potentiels est ({{ $nomFournisseurCount }})
                                </p>

                                <div class="mt-6 flex justify-center gap-x-4">
                                    <form action="{{ route('biicf.sendoffreneg', $produit->id) }}" method="POST">
                                        @csrf
                                        @method('POST')

                                        <div class="flex flex-col">

                                            <select name="zone_economique"
                                                class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500 dark:border-neutral-800 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                required>
                                                <option disabled selected>Zone économique</option>
                                                <option value="proximite">Proximité</option>
                                                <option value="locale">Locale</option>
                                                <option value="departementale">Départementale</option>
                                                <option value="nationale">Nationale</option>
                                                <option value="sous_regionale">Sous Régionale</option>
                                                <option value="continentale">Continentale</option>
                                            </select>

                                            <input type="number" name="quantite" class="rounded-md"
                                                placeholder="Entrez la quantité">

                                            <!-- Champ caché pour l'ID du produit -->
                                            <input type="hidden" name="produit_id" value="{{ $produit->id }}">

                                            <div class="flex my-3">
                                                <button type="submit" @if ($nomFournisseurCount == 0) disabled @endif
                                                    class="py-2 px-3 mr-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                                    soumettre
                                                </button>

                                                <button type="button"
                                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                                    data-hs-overlay="#hs-offreGrp-{{ $produit->id }}">
                                                    Annuler
                                                </button>

                                            </div>


                                        </div>







                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div id="hs-offreNeg-{{ $produit->id }}"
                    class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                    <div
                        class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                        <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                            <div class="absolute top-2 end-2">
                                <button type="button"
                                    class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                    data-hs-overlay="#hs-offreNeg-{{ $produit->id }}">
                                    <span class="sr-only">Close</span>
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                <h3 class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                    Offre Negocié
                                </h3>
                                <p class="text-gray-500 dark:text-neutral-500">
                                    le nombre de clients totals potentiels est ({{ $nombreProprietaires }})
                                </p>
                                <p class="text-gray-500 dark:text-neutral-500">
                                    Selectionnez la zone que voulez ciblée
                                </p>

                                <div class="mt-6 flex justify-center gap-x-4">
                                    <form action="{{ route('biicf.sendoffregrp', $produit->id) }}" method="POST"
                                        class="flex flex-col gap-4">
                                        @csrf
                                        @method('POST')

                                        <select name="zone_economique"
                                            class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500 dark:border-neutral-800 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            required>
                                            <option disabled selected>Zone économique</option>
                                            <option value="proximite">Proximité</option>
                                            <option value="locale">Locale</option>
                                            <option value="departementale">Départementale</option>
                                            <option value="nationale">Nationale</option>
                                            <option value="sous_regionale">Sous Régionale</option>
                                            <option value="continentale">Continentale</option>
                                        </select>

                                        <!-- Champ caché pour l'ID du produit -->
                                        <input type="hidden" name="produit_id" value="{{ $produit->id }}">

                                        <button type="submit" @if ($nombreProprietaires == 0) disabled @endif
                                            class="py-2 px-3 flex items-center justify-center text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-800 shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                            Soumettre
                                        </button>
                                    </form>

                                    <button type="button"
                                        class="py-2 px-3 flex items-center justify-center text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                        data-hs-overlay="#hs-offreNeg-{{ $produit->id }}">
                                        Annuler
                                    </button>
                                </div>


                            </div>
                        </div>
                    </div>
                </div> --}}
    {{-- @endif --}}


    {{-- @livewire('achat-direct-groupe ', ['id' => $id]) --}}


    {{-- </div>
    </div> --}}

    {{-- <style>
        #toggleContent {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.5s ease-out;
        }

        #toggleContent.show {
            max-height: 500px;
            /* Vous pouvez ajuster cette valeur selon la hauteur de votre contenu */
        }

        .hs-carousel-body {
            display: flex;
            overflow: hidden;
            max-height: 100%;
        }

        .hs-carousel-slide {
            flex-shrink: 0;
            width: 100%;
        }

        .hs-carousel-body img {
            display: block;
            max-height: 100%;
            max-width: 100%;
        }
    </style> --}}

    {{-- <div class="w-96 h-96">
        <div class="grid gap-4">
            <div>
                <img class="h-auto max-w-full rounded-lg" src="https://flowbite.s3.amazonaws.com/docs/gallery/featured/image.jpg"
                    alt="">
            </div>
            <div class="grid grid-cols-5 gap-4">
                <div>
                    <img class="h-auto max-w-full rounded-lg"
                        src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg"
                        src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-2.jpg" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg"
                        src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-3.jpg" alt="">
                </div>

            </div>
        </div>
    </div> --}}


    <!-- Carousel wrapper -->


    {{-- <div class="relative">
        <div class="">
            <!-- Carousel wrapper -->
            <div class="relative h-54 overflow-hidden rounded-lg md:h-96">
                <!-- Item 1 -->
                <div>
                    <img src="https://flowbite.s3.amazonaws.com/docs/gallery/featured/image.jpg"
                        class="absolute block max-w-full h-auto -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                        alt="">
                </div>
            </div>

            <div class="grid gap-4 mt-4">
                <div class="grid grid-cols-7 md:grid-cols-7 gap-4">
                    <div>
                        <img class="h-auto max-w-full rounded-lg"
                            src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
                    </div>
                    <div>
                        <img class="h-auto max-w-full rounded-lg"
                            src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-2.jpg" alt="">
                    </div>
                    <div>
                        <img class="h-auto max-w-full rounded-lg"
                            src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-3.jpg" alt="">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <!-- Description produit -->
                <div class="bg-white p-4 rounded-lg">
                    <h2 class="text-3xl font-semibold mb-2">Montre</h2>
                    <p class="text-sm font-medium text-gray-600 mb-7">localisation1, localisation2</p>
                    <p class="text-4xl font-medium text-purple-600 mb-8" data-price="{{ $produit->prix }}">
                        100.000 FCFA
                        <span class="text-sm text-gray-600 font-medium uppercase">Prix unitaire</span>
                    </p>
                    <p class="text-gray-500 mb-4">
                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aperiam, accusantium. Dolore rerum fugit
                        temporibus eveniet tempora iste distinctio laborum et.
                    </p>
                    <p class="text-gray-500 mb-4">
                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aperiam, accusantium. Dolore rerum fugit
                        temporibus eveniet tempora iste distinctio laborum et.
                    </p>
                </div>
                {{-- <div class="absolute bottom-0 right-0 z-40"> --}}
    {{-- @livewire('achat-direct-groupe ', ['id' => $id]) --}}
    {{-- <div class="relative md:static p-4 bg-white rounded-lg shadow-lg">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Champ quantité -->
                            <input type="text" placeholder="quantité"
                                class="col-span-1 border border-gray-300 rounded-lg p-2">
                            <!-- Champ type de livraison -->
                            <input type="text" placeholder="type de livraison"
                                class="col-span-1 border border-gray-300 rounded-lg p-2">

                            <!-- Champ lieu de livraison -->
                            <input type="text" placeholder="Lieu de livraison"
                                class="col-span-2 border border-gray-300 rounded-lg p-2">

                            <!-- Champ spécificité -->
                            <input type="text" placeholder="specificité"
                                class="col-span-2 border border-gray-300 rounded-lg p-2">

                            <!-- Date et heure - Au plus tôt -->
                            <div class="col-span-1">
                                <input type="date" placeholder="Au plus tôt"
                                    class="w-full border border-gray-300 rounded-lg p-2 mb-2">
                                <input type="time" placeholder="Heure début"
                                    class="w-full border border-gray-300 rounded-lg p-2">
                            </div>

                            <!-- Date et heure - Au plus tard -->
                            <div class="col-span-1">
                                <input type="date" placeholder="Au plus tard"
                                    class="w-full border border-gray-300 rounded-lg p-2 mb-2">
                                <input type="time" placeholder="Heure fin"
                                    class="w-full border border-gray-300 rounded-lg p-2">
                            </div>

                            <!-- Nouvelle ligne d'input -->
                            <div class="col-span-1">
                                <input type="date" placeholder="Nouvelle date"
                                    class="w-full border border-gray-300 rounded-lg p-2 mb-2">
                            </div>

                            <!-- Nouvelle heure -->
                            <div class="col-span-1">
                                <input type="time" placeholder="Nouvelle heure"
                                    class="w-full border border-gray-300 rounded-lg p-2 mb-2">
                            </div>
                        </div>

                        <!-- Prix total -->
                        <div class="mt-4 flex justify-between items-center">
                            <p class="text-lg font-bold text-purple-600">Prix total :</p>
                            <p class="text-xl font-semibold text-purple-600">10.000 FCFA</p>
                        </div>

                        <!-- Boutons Annuler et Accepter -->
                        <div class="mt-6 flex justify-between">
                            <button class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                                Annuler
                            </button>
                            <button class="bg-purple-500 text-white px-6 py-2 rounded-lg hover:bg-purple-600">
                                Accepter
                            </button>
                        </div>
                    </div> --}}
    {{-- </div> --}}

    {{-- </div>

        </div>

    </div>  --}}


    {{-- <div class="max-w-5xl mx-auto">

        <!-- Barre du haut avec timer -->
        <div class="flex justify-between items-center bg-gray-200 p-4 rounded-lg mb-6">
            <h1 class="text-lg font-bold">NEGOCIATION ...</h1>
            <div class="bg-red-200 text-red-600 font-bold px-4 py-2 rounded-lg">
                10:00:00
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <div class="grid gap-4">
                <div class="grid grid-rows-2 gap-4">

                    <!-- Section images à gauche -->

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <img class="h-auto max-w-full rounded-lg"
                                src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-1.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-lg"
                                src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-2.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-lg"
                                src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-3.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-lg"
                                src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-4.jpg" alt="">
                        </div>
                    </div>
                    <!-- Section offre en bas -->
                    <div class="flex items-center flex-col lg:space-y-4 lg:pb-8 max-lg:w-full  sm:grid-cols-2 max-lg:gap-6 sm:mt-2"
                        uk-sticky="media: 1024; end: #js-oversized; offset: 80">

                        <div class="bg-white rounded-xl shadow-sm text-sm font-medium border1 dark:bg-dark2 w-full">

                            <!-- comments -->
                            <div
                                class="h-[400px] overflow-y-auto sm:p-4 p-4 border-t border-gray-100 font-normal space-y-3 relative dark:border-slate-700/40">

                                <div class="flex items-center gap-3 relative">
                                    <img src="https://flowbite.s3.amazonaws.com/docs/gallery/square/image-4.jpg"
                                        alt="" class="w-8 h-8  mt-1 rounded-full overflow-hidden object-cover">
                                    <div class="flex-1">
                                        <p class=" text-base text-black font-medium inline-block dark:text-white">
                                            chance </p>
                                        <p class="text-sm mt-0.5">
                                            111111 FCFA</p>

                                    </div>
                                </div>
                            </div>


                            <form wire:submit.prevent="commentFormLivr">

                                <div
                                    class="sm:px-4 sm:py-3 p-2.5 border-t border-gray-100 flex items-center justify-between gap-1 dark:border-slate-700/40">
                                    <input type="hidden" name="code_livr" wire:model="code_livr">
                                    <input type="hidden" name="quantiteC" wire:model="quantite">
                                    <input type="hidden" name="idProd" wire:model="idProd">
                                    <input type="hidden" name="nameSender" wire:model="user_id">
                                    <input type="hidden" name="id_trader" wire:model="id_trader">
                                    <input type="hidden" name="prixProd" id="prixProd" wire:model="prixProd">
                                    <input type="number" name="prixTrade" id="prixTrade" wire:model="prixTrade"
                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        placeholder="Faire une offre..." required>

                                    <button type="submit" id="submitBtnAppel"
                                        class=" justify-center p-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-800 dark:text-blue-500 dark:hover:bg-gray-600">
                                        <!-- Button Text and Icon -->
                                        <span wire:loading.remove>
                                            <svg class="w-5 h-5 rotate-90 rtl:-rotate-90 inline-block" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                                <path
                                                    d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z" />
                                            </svg>
                                        </span>
                                        <!-- Loading Spinner -->
                                        <span wire:loading>
                                            <svg class="w-5 h-5 animate-spin inline-block"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                                            </svg>
                                            </svg>
                                    </button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
            <div class="grid gap-4">
                <!-- Section détails du produit à droite -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Banane</h2>

                    <div class="mb-4">
                        <p class="font-bold">Quantité:</p>
                        <p>100</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">Conditionnement du colis:</p>
                        <p>Emballage: ..., Dimension: ..., Poids: ..., Autre: ...</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">Localisation du produit:</p>
                        <p>...</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">Localisation du client:</p>
                        <p>...</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">Lieu de livraison:</p>
                        <p>...</p>
                    </div>
                </div>

            </div>

        </div>
    </div> --}}
@endsection
