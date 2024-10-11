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



    <!-- Carousel wrapper -->
    <section id="checkoutSection" class="bg-white md:py-16 dark:bg-gray-900 antialiased rounded-lg shadow-lg">

        <ol
            class="items-center flex w-full max-w-2xl text-center text-sm font-medium text-gray-500 dark:text-gray-400 sm:text-base  p-5">
            <li
                class="after:border-1 flex items-center text-primary-700  after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                <span
                    class="flex items-center text-blue-600 after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                    <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Details
                </span>
            </li>

            <li
                class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                <span
                    class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                    <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Achat
                </span>
            </li>

            <li>
                <span
                    class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                    <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Crédit
                </span>
            </li>
        </ol>

        <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0 ">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-16">
                <!-- Images Section -->
                <div class="flex flex-col space-y-4">
                    <!-- Main Image -->
                    <div class="shrink-0 max-w-md lg:max-w-lg mx-auto">
                        <img id="mainImage" class="w-full dark:hidden rounded-lg"
                            src="{{ asset('post/all/' . $produit->photoProd1) }}" alt="Main Product Image" />
                        <img id="mainImage" class="w-full hidden dark:block"
                            src="{{ asset('post/all/' . $produit->photoProd1) }}" alt="Main Product Image" />
                    </div>

                    <!-- Thumbnail Images -->
                    <div class="flex space-x-4">
                        <img onclick="changeImage('{{ asset('post/all/' . $produit->photoProd2) }}')"
                            class="w-20 h-20 object-cover cursor-pointer border rounded-lg"
                            src="{{ asset('post/all/' . $produit->photoProd2) }}" alt="Thumbnail 2">
                        <img onclick="changeImage('{{ asset('post/all/' . $produit->photoProd3) }}')"
                            class="w-20 h-20 object-cover cursor-pointer border rounded-lg"
                            src="{{ asset('post/all/' . $produit->photoProd3) }}" alt="Thumbnail 3">
                        <img onclick="changeImage('{{ asset('post/all/' . $produit->photoProd4) }}')"
                            class="w-20 h-20 object-cover cursor-pointer border rounded-lg"
                            src="{{ asset('post/all/' . $produit->photoProd4) }}" alt="Thumbnail 4">
                    </div>
                </div>

                <!-- Product Info Section -->
                <div class="mt-6 sm:mt-8 lg:mt-0">
                    <p class="text-xl font-extrabold text-gray-900 sm:text-3xl dark:text-white mr-4">
                        {{ $produit->name }}
                    </p>

                    <!-- Price and Rating -->
                    <div class="mt-4 sm:items-center sm:gap-4 sm:flex">
                        <p class="text-xl font-bold text-gray-900 sm:text-3xl dark:text-white mr-4"
                            data-price="{{ $produit->prix }}">
                            {{ number_format($produit->prix, 0, ',', ' ') }} FCFA
                        </p>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <div class="flex items-center gap-1">
                                <!-- SVGs for rating stars (repeated for all stars) -->
                                <svg class="w-4 h-4 text-yellow-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d=" M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397
                                                                            8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067
                                                                            2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39
                                                                            3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                </svg>
                                <!-- Repeat for additional stars -->
                            </div>
                            <p class="text-sm font-medium leading-none text-gray-500 dark:text-gray-400  mr-4">(5.0)</p>
                            <a href="#"
                                class="text-sm font-medium leading-none text-gray-900 underline hover:no-underline dark:text-white">
                                345 Reviews
                            </a>
                        </div>
                    </div>

                    <!-- Bouton pour afficher la section -->
                    @if ($produit->user_id != $user->id)
                        <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-8">
                            <a href="javascript:void(0)" id="toggleForm"
                                class="flex w-full items-center justify-center py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:ring-4 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                role="button">
                                Procédez à l'achat.
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                </svg>
                            </a>
                        </div>

                        <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />

                        <div class="w-full p-3 bg-gray-200 rounded-2xl flex justify-between items-center cursor-pointer "
                            onclick="toggleVisibility()">
                            <p class="font-medium text-sm text-gray-700">Caracteristique</p>
                            <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                    @else
                        <div class="mt-6 sm:gap-4 sm:items-center sm:flex sm:mt-8">
                            <div class="relative inline-block w-full">
                                <div>
                                    <button type="button"
                                        class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        id="options-menu" aria-haspopup="true" aria-expanded="true"
                                        onclick="toggleDropdown()">
                                        Filter Options
                                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <div id="dropdown-menu"
                                    class="absolute right-0 z-10 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden">
                                    <div class="py-1" role="menu" aria-orientation="vertical"
                                        aria-labelledby="options-menu">
                                        <div class="px-4 py-2">
                                            <span class="font-bold">Filter by Properties</span>
                                        </div>
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
                                            {{-- <div class="flex items-center">
                                        <button class="w-full mt-3 bg-purple-600 text-white py-2 mr- rounded-xl"
                                            data-hs-overlay="#hs-offreGrpNeg-{{ $produit->id }}">faire une offre Groupé negocié</button>
                                    </div> --}}
                                        </div>
                                        <div class="border-t border-gray-200"></div>
                                        <div class="px-4 py-2">
                                            <button
                                                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none">
                                                Apply Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />

                        <button class="w-full bg-red-500 text-white py-2 mb-3 rounded-xl"
                            data-hs-overlay="#hs-delete-{{ $produit->id }}">Supprimé produit</button>
                    @endif


                    <!-- Product Description -->
                    <div id="toggleContent" class="w-full p-3 gap-y-2 hidden mb-4">
                        @if ($produit->continent)
                            <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                <p class="text-sm font-semibold">Localisation</p>
                                <p class="text-sm font-medium text-gray-600"> {{ $produit->continent }},
                                    {{ $produit->sous_region }},
                                    {{ $produit->pays }}, {{ $produit->zoneecoServ }}, {{ $produit->villeServ }},
                                    {{ $produit->comnServ }}</p>
                            </div>
                        @endif
                        @if ($produit->reference)
                            <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                <p class="text-sm font-semibold">Reference</p>
                                <p class="text-sm font-medium text-gray-600">{{ $produit->reference }}</p>
                            </div>
                        @endif
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
                        @if ($produit->specification)
                            <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                <p class="text-sm font-semibold">Capacité de livré</p>
                                <p class="text-sm font-medium text-gray-600">{{ $produit->specification }}</p>
                            </div>
                        @endif
                        @if ($produit->origine)
                            <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                <p class="text-sm font-semibold">Capacité de livré</p>
                                <p class="text-sm font-medium text-gray-600">{{ $produit->origine }}</p>
                            </div>
                        @endif
                        @if ($produit->Particularite)
                            <div class="w-full flex justify-between items-center py-4 px-3 border-b-2">
                                <p class="text-sm font-semibold">Particularite</p>
                                <p class="text-sm font-medium text-gray-600">{{ $produit->Particularite }}</p>
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

                </div>
            </div>
        </div>
    </section>

    <!-- Section à afficher ou cacher -->
    <section id="hiddenSection" class="mt-5" style="display: none;">
        @livewire('achat-direct-groupe', ['id' => $id])
    </section>


    <section class="mt-5">
        @livewire('demandecredit')
    </section>

    {{-- les pop pour les fonctionnalite des fournissuers --}}

    <div id="hs-offreGrpNeg-{{ $produit->id }}"
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
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
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
    </div>


    <div id="hs-delete-{{ $produit->id }}"
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
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
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
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
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
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
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
                        le nombre de fornisseur potentiels est ({{ $nomFournisseurCount }})
                    </p>

                    <div class="mt-6 flex justify-center gap-x-4">
                        <form action="{{ route('biicf.sendoffreneg', $produit->id) }}" method="POST">
                            @csrf
                            @method('POST')

                            <!-- Champ caché pour l'ID du produit -->
                            <input type="hidden" name="produit_id" value="{{ $produit->id }}">

                            <button type="submit" @if ($nomFournisseurCount == 0) disabled @endif
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                soumettre
                            </button>
                        </form>
                        <button type="button"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                            data-hs-overlay="#hs-offreGrp-{{ $produit->id }}">
                            Annuler
                        </button>
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
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
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
                        le nombre de client potentiels est ({{ $nombreProprietaires }})
                    </p>

                    <div class="mt-6 flex justify-center gap-x-4">
                        <form action="{{ route('biicf.sendoffregrp', $produit->id) }}" method="POST">
                            @csrf
                            @method('POST')

                            <!-- Champ caché pour l'ID du produit -->
                            <input type="hidden" name="produit_id" value="{{ $produit->id }}">

                            <button type="submit" @if ($nombreProprietaires == 0) disabled @endif
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                soumettre
                            </button>
                        </form>
                        <button type="button"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                            data-hs-overlay="#hs-offreNeg-{{ $produit->id }}">
                            Annuler
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- JavaScript function to change main image -->
    <script>
        function toggleDropdown() {
            const dropdownMenu = document.getElementById('dropdown-menu');
            dropdownMenu.classList.toggle('hidden');
        }

        // Close dropdown if clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('#options-menu')) {
                const dropdowns = document.getElementsByClassName("absolute");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (!openDropdown.classList.contains('hidden')) {
                        openDropdown.classList.add('hidden');
                    }
                }
            }
        }

        document.getElementById('toggleForm').addEventListener('click', function() {
            var section = document.getElementById('hiddenSection');
            // Vérifie si la section est visible
            if (section.style.display === 'none' || section.style.display === '') {
                // Affiche la section
                section.style.display = 'block';
            } else {
                // Cache la section
                section.style.display = 'none';
            }
        });

        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }

        function toggleVisibility() {
            const contentDiv = document.getElementById('toggleContent');

            if (contentDiv.classList.contains('hidden')) {
                contentDiv.classList.remove('hidden');
                // Forcing reflow to enable transition
                contentDiv.offsetHeight;
                contentDiv.classList.add('show');
            } else {
                contentDiv.classList.remove('show');
                contentDiv.addEventListener('transitionend', () => {
                    contentDiv.classList.add('hidden');
                }, {
                    once: true
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const section = document.querySelector('#checkoutSection');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        section.classList.add(
                            'bg-active'); // Appliquer la classe qui change le background
                    } else {
                        section.classList.remove(
                            'bg-active'); // Retirer la classe si l'utilisateur défile
                    }
                });
            }, {
                threshold: 0.5
            }); // Le seuil définit combien de l'élément doit être visible avant d'appliquer la classe

            observer.observe(section);
        });
    </script>
    <style>
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
    </style>






@endsection
