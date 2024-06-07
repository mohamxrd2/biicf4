@extends('admin.layout.navside')

@section('title', 'Details client')

@section('content')

    <div
        class="w-full p-6 flex justify-between items-center bg-gray-50 border border-gray-200 rounded-lg  dark:bg-neutral-800">


        <div class="flex md:gap-8 gap-4 items-center md:p-8 p-6 md:pb-4">

            <div class="relative md:w-20 md:h-20 w-12 h-12 shrink-0">
                <label for="file" class="cursor-pointer">
                    <img id="img" src="{{ asset($user->photo) }}" class="object-cover w-full h-full rounded-full"
                        alt="" />
                </label>

            </div>



            <div class="flex-1">
                <h3 class="md:text-xl text-base font-semibold text-black dark:text-white">{{ $user->name }}</h3>
                <p class="text-sm text-blue-600 mt-1 font-normal">{{ '@' . $user->username }}</p>
            </div>


        </div>
        <div>
            <div class=" p-3 border border-gray-200 bg-white rounded-md">
                <p class="text-sm text-gray-600">Solde</p>
                <p class="text-2xl font-bold">{{ $wallet->balance }} FCFA</p>

            </div>

        </div>
    </div>


    <div class="grid grid-cols-3 gap-4 my-4">
        <div class="lg:col-span-2 col-span-3 gap-y-4">
            @if (session('success'))
                <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-200 text-red-800 px-4 py-2 rounded-md mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-4 bg-white border border-gray-200 rounded-md">
                <div class="relative overflow-x-auto sm:rounded-lg">
                    <div
                        class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div>
                            <h1 class="font-bold text-md">Liste de publications</h1>
                        </div>
                        <div class="flex items-center">
                            <label for="table-search" class="sr-only">Search</label>
                            <div class="relative mr-2">
                                <div
                                    class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" onkeyup="searchTable()"
                                    class="block w-80 p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Rechercher...">
                            </div>
                            {{-- bouton des publication --}}
                            <button type="button"
                                class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                data-hs-overlay="#hs-static-backdrop-modal">
                                Ajouter

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                            </button>
                            <div id="hs-static-backdrop-modal"
                                class="hs-overlay size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]   hidden  bg-black bg-opacity-50"
                                data-hs-overlay-keyboard="false" aria-overlay="true" tabindex="-1">

                                <div
                                    class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                                    {{-- form des publication --}}

                                    <form action="{{ route('admin.client.storePub', ['username' => $user->username]) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div
                                            class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto form1">

                                            <div class="flex justify-between items-center py-3 px-4 border-b">
                                                <h3 class="font-bold text-gray-800">
                                                    Ajouter des publications
                                                </h3>

                                                <button type="button"
                                                    class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none"
                                                    data-hs-overlay="#hs-static-backdrop-modal">
                                                    <span class="sr-only">Close</span>
                                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M18 6 6 18"></path>
                                                        <path d="m6 6 12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- publication --}}
                                            <div class="p-4 overflow-y-auto">


                                                <div class="max-w-md mx-auto">
                                                    <div class="gap-y-6">
                                                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                                                        <div class="relative z-0 w-full mb-5 group">
                                                            <select name="type" id="choose"
                                                                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                                                <option value="" disabled selected>Choisir le
                                                                    type</option>
                                                                <option value="produits">Produit</option>
                                                                <option value="services">Service</option>
                                                            </select>

                                                        </div>
                                                        {{-- les inputs suivants sont pour les produits --}}
                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="text" name="name" id="floating_first_name"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder=" Nom du produit ou service " />
                                                        </div>
                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="text" name="conditionnement"
                                                                id="floating_cond"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder="conditionnement" />
                                                        </div>
                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="text" name="format" id="floating_format"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder="format du produit " />
                                                        </div>
                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="number" name="qteProd_min" id="floating_qtemin"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder=" Quantité Minimale " />
                                                        </div>

                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="number" name="qteProd_max" id="floating_qtemax"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder=" Quantité Maxiamle" />
                                                        </div>
                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="number" name="prix" id="floating_prix"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder="Prix" />
                                                        </div>
                                                        <div class="relative z-0 w-full mb-5 group">
                                                            <select name="livraison" id="floating_livraison"
                                                                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                                                <option value="" disabled selected>Capacité à
                                                                    livrer</option>
                                                                <option value="oui">Oui</option>
                                                                <option value="non">Non</option>
                                                            </select>

                                                        </div>



                                                        {{-- les inputs suivants sont pour les services --}}



                                                        <div class="relative z-0 w-full mb-5 group">
                                                            <select name="qualification" id="floating_qualification"
                                                                class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                                                <option value="" disabled selected>Experiance dans le
                                                                    domaine</option>
                                                                <option value="Moins de 1 an">Moins de 1 an</option>
                                                                <option value="De 1 à 5 ans">De 1 à 5 ans</option>
                                                                <option value="De 5 à 10 ans">De 5 à 10 ans</option>
                                                                <option value="Plus de 10 ans">Plus de 10 ans</option>
                                                            </select>

                                                        </div>

                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="text" name="specialite"
                                                                id="floating_specialite"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder="Spécialite " />
                                                        </div>
                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="number" name="qte_service"
                                                                id="floating_qte_service"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder="Nombre de personnel " />
                                                        </div>





                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="text" name="ville" id="floating_ville"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder="Ville " />
                                                        </div>
                                                        <div class="space-y-3 w-full mb-3">
                                                            <input type="text" name="commune" id="floating_commune"
                                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                placeholder="Commune " />
                                                        </div>
                                                      
                                                        <div class="relative z-0 w-full mb-5 group">
                                                            <textarea id="floating_description" name="description"
                                                                class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                                rows="3" placeholder="Description de la publication"></textarea>
                                                        </div>

                                                        <div
                                                            class="relative z-0 flex justify-between items-center w-full flex-row">
                                                            <div class="flex items-center justify-center w-20"
                                                                id="floating_photo1">
                                                                <div class=" overflow-hidden rounded-md relative">
                                                                    <label for="file-upload1"
                                                                        class="flex flex-col items-center justify-center w-full h-30 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                                        <div
                                                                            class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="1.5" stroke="currentColor"
                                                                                class="w-6 h-6">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                                            </svg>

                                                                            <p
                                                                                class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                                                <span class="font-semibold">Photo 1</span>
                                                                            </p>
                                                                        </div>
                                                                    </label>
                                                                    <input id="file-upload1" class="hidden rounded-md "
                                                                        type="file" onchange="previewImage(this)"
                                                                        name="image">
                                                                    <img id="image-preview1"
                                                                        class="absolute inset-0 w-full h-full object-cover hidden">
                                                                    <button type="button" onclick="removeImage()"
                                                                        id="remove-button1"
                                                                        class="text-red-600 bg-white w-5 h-5 rounded-full absolute top-2 right-2 hidden">
                                                                        <svg class="w-full"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none" viewBox="0 0 24 24"
                                                                            stroke-width="1.5" stroke="currentColor"
                                                                            class="w-6 h-6">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M6 18 18 6M6 6l12 12" />
                                                                        </svg>

                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center justify-center w-20"
                                                                id="floating_photo2">
                                                                <div class=" overflow-hidden rounded-md relative">
                                                                    <label for="file-upload2"
                                                                        class="flex flex-col items-center justify-center w-full h-30 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                                        <div
                                                                            class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="1.5" stroke="currentColor"
                                                                                class="w-6 h-6">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                                            </svg>

                                                                            <p
                                                                                class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                                                <span class="font-semibold">Photo 2</span>
                                                                            </p>
                                                                        </div>
                                                                    </label>
                                                                    <input id="file-upload2" class="hidden rounded-md"
                                                                        type="file" onchange="previewImage2(this)"
                                                                        name="image2">
                                                                    <img id="image-preview2"
                                                                        class="absolute inset-0 w-full h-full object-cover hidden">
                                                                    <button type="button" onclick="removeImage2()"
                                                                        id="remove-button2"
                                                                        class="text-red-600 bg-white w-5 h-5 rounded-full absolute top-2 right-2 hidden">
                                                                        <svg class="w-full"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none" viewBox="0 0 24 24"
                                                                            stroke-width="1.5" stroke="currentColor"
                                                                            class="w-6 h-6">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M6 18 18 6M6 6l12 12" />
                                                                        </svg>

                                                                    </button>
                                                                </div>
                                                            </div>


                                                            <div class="flex items-center justify-center w-20"
                                                                id="floating_photo3">
                                                                <div class="overflow-hidden rounded-md relative">
                                                                    <label for="file-upload3"
                                                                        class="flex flex-col items-center justify-center w-full h-30 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                                        <div
                                                                            class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="1.5" stroke="currentColor">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                                            </svg>
                                                                            <p
                                                                                class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                                                <span class="font-semibold">Photo 3</span>
                                                                            </p>
                                                                        </div>
                                                                    </label>
                                                                    <input id="file-upload3" class="hidden rounded-md"
                                                                        type="file" onchange="previewImage3(this)"
                                                                        name="image3">
                                                                    <img id="image-preview3"
                                                                        class="absolute inset-0 w-full h-full object-cover hidden">
                                                                    <button type="button" onclick="removeImage3()"
                                                                        id="remove-button3"
                                                                        class="text-red-600 bg-white w-5 h-5 rounded-full absolute top-2 right-2 hidden">
                                                                        <svg class="w-full"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none" viewBox="0 0 24 24"
                                                                            stroke-width="1.5" stroke="currentColor">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M6 18 18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center justify-center w-20"
                                                                id="floating_photo4">
                                                                <div class="overflow-hidden rounded-md relative">
                                                                    <label for="file-upload4"
                                                                        class="flex flex-col items-center justify-center w-full h-30 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                                        <div
                                                                            class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="1.5" stroke="currentColor">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                                            </svg>
                                                                            <p
                                                                                class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                                                <span class="font-semibold">Photo 4</span>
                                                                            </p>
                                                                        </div>
                                                                    </label>
                                                                    <input id="file-upload4" class="hidden rounded-md"
                                                                        type="file" onchange="previewImage4(this)"
                                                                        name="image4">
                                                                    <img id="image-preview4"
                                                                        class="absolute inset-0 w-full h-full object-cover hidden">
                                                                    <button type="button" onclick="removeImage4()"
                                                                        id="remove-button4"
                                                                        class="text-red-600 bg-white w-5 h-5 rounded-full absolute top-2 right-2 hidden">
                                                                        <svg class="w-full"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none" viewBox="0 0 24 24"
                                                                            stroke-width="1.5" stroke="currentColor">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M6 18 18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t">
                                                <button type="button"
                                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                                                    data-hs-overlay="#hs-static-backdrop-modal">
                                                    Fermer
                                                </button>
                                                <button type="submit"
                                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                                    Ajouter
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>



                    <table class="w-full mt-5 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nom & photo</th>
                                <th scope="col" class="px-6 py-3">type</th>
                                <th scope="col" class="px-6 py-3">prix</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($produitCount == 0)

                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">
                                        <div class="flex flex-col justify-center items-center h-40">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun produit ou service
                                            </h1>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($produitsServices as $produitsService)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td
                                            class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                            <img class="w-10 h-10 rounded-md"
                                                src="{{ $produitsService->photoProd1 ? asset($produitsService->photoProd1) : asset('img/noimg.jpeg') }}"
                                                alt="Jese image">
                                            <div class="ml-3">
                                                <div class="text-base font-semibold">{{ $produitsService->name }}</div>

                                            </div>
                                        </td>
                                        <td class="px-6 py-4">{{ $produitsService->type }}</td>
                                        <td class="px-6 py-4">{{ $produitsService->prix }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex">
                                                <a href="#" data-hs-overlay="#hs-delete-{{ $produitsService->id }}"
                                                    class="font-medium text-red-600 dark:text-blue-500  mr-2">
                                                    <button type="submit"><svg xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg></button>
                                                </a>
                                            </div>

                                            <div id="hs-delete-{{ $produitsService->id }}"
                                                class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                                                <div
                                                    class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                                                    <div
                                                        class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                                                        <div class="absolute top-2 end-2">
                                                            <button type="button"
                                                                class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                                                data-hs-overlay="#hs-delete-{{ $produitsService->id }}">
                                                                <span class="sr-only">Close</span>
                                                                <svg class="flex-shrink-0 size-4"
                                                                    xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
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
                                                                <svg class="flex-shrink-0 size-5"
                                                                    xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" fill="currentColor"
                                                                    viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                                                </svg>
                                                            </span>
                                                            <!-- End Icon -->

                                                            <h3
                                                                class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                                                Supprimé
                                                            </h3>
                                                            <p class="text-gray-500 dark:text-neutral-500">
                                                                Vous etes sur de supprimé le produit ?
                                                            </p>

                                                            <div class="mt-6 flex justify-center gap-x-4">
                                                                <form
                                                                    action="{{ route('admin.products.destroy', $produitsService->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button type="submit"
                                                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                                                        Supprimer
                                                                    </button>
                                                                </form>
                                                                <button type="button"
                                                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                                                    data-hs-overlay="#hs-delete-{{ $produitsService->id }}">
                                                                    Annuler
                                                                </button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Message d'aucun résultat trouvé -->
                                            <div id="noResultMessage" class="h-20 flex justify-center items-center"
                                                style="display: none;">Aucun résultat
                                                trouvé.</div>



                                        </td>
                                    </tr>
                                @endforeach





                            @endif





                        </tbody>
                    </table>

                    <!-- Message d'aucun résultat trouvé -->
                    <div id="noResultMessage" class="h-20 flex justify-center items-center" style="display: none;">
                        Aucun résultat trouvé.
                    </div>

                </div>
                <div class="my-5 flex justify-end">

                </div>

            </div>
            <div class="p-4 mt-4 bg-white border border-gray-200 rounded-md">
                <div class="relative overflow-x-auto sm:rounded-lg">
                    <div
                        class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div>
                            <h1 class="font-bold text-md">Liste de consommation</h1>
                        </div>
                        {{-- consommation --}}
                        <button type="button"
                            class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                            data-hs-overlay="#hs-static-backdrop-model">
                            Ajouter

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                        </button>

                        {{-- consommation --}}
                        <div id="hs-static-backdrop-model"
                            class="hs-overlay size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]  hidden  bg-black bg-opacity-50"
                            data-hs-overlay-keyboard="false" aria-overlay="true" tabindex="-1">

                            <div
                                class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">


                                {{-- consommation --}}
                                <form action="{{ route('admin.client.storeCons', ['username' => $user->username]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto">



                                        <div class="flex justify-between items-center py-3 px-4 border-b">
                                            <h3 class="font-bold text-gray-800">
                                                Ajouter des consommations
                                            </h3>

                                            <button type="button"
                                                class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none"
                                                data-hs-overlay="#hs-static-backdrop-model">
                                                <span class="sr-only">Close</span>
                                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
                                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M18 6 6 18"></path>
                                                    <path d="m6 6 12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="p-4 overflow-y-auto">
                                            <div class="max-w-md mx-auto">

                                                <input type="text" name="user_id" hidden value="{{ $user->id }}">

                                                <div class="relative z-0 w-full mb-5 group">
                                                    <select name="type" id="chooseC"
                                                        class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                                        <option value="" disabled selected>Choisir le
                                                            type</option>
                                                        <option value="produits">Produit</option>
                                                        <option value="services">Service</option>
                                                    </select>

                                                </div>
                                                {{-- les inputs suivants sont pour les produits --}}
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="text" name="nameC" id="floating_nameC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder=" Nom de la consommation" />
                                                </div>
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="text" name="conditionnementC"
                                                        id="floating_conditionnementC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="Conditionnement" />
                                                </div>
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="text" name="formatC" id="floating_formatC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="Format de consommations " />
                                                </div>
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="number" name="qteC" id="floating_quantiteC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="quantité de consommations " />
                                                </div>
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="number" name="prixC" id="floating_prixC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="prix" />
                                                </div>
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="number" name="frequenceC" id="floating_cons"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="frequence de consommation [0-10]" />
                                                </div>

                                                <div class="relative z-0 w-full mb-5 group">
                                                    <select name="jour_achat" id="floating_jour_achat"
                                                        class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                                        <option value="" disabled selected>Jour d'achat</option>
                                                        <option value="Lundi">Lundi</option>
                                                        <option value="Mardi">Mardi</option>
                                                        <option value="Mercredi">Mercredi</option>
                                                        <option value="Jeudi">Jeudi</option>
                                                        <option value="Vendredi">Vendredi</option>
                                                        <option value="Samedi">Samedi</option>
                                                        <option value="Dimanche">Dimanche</option>
                                                        <option value="Plusieur jour de la semaine">Plusieur jour de la
                                                            semaine</option>
                                                    </select>

                                                </div>
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="text" name="qualificationC" id="floating_qualifC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="qualification service" />
                                                </div>
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="text" name="specialité" id="floating_specialiteC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="specialité " />
                                                </div>


                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="text" name="desriptionC" id="floating_descriptionC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder=" Desription" />
                                                </div>

                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="text" name="zone_activité"
                                                        id="floating_zone_activite"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="Zone d'activité " />
                                                </div>
                                                <div class="space-y-3 w-full mb-3">
                                                    <input type="text" name="villeC" id="floating_villeC"
                                                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                                        placeholder="ville " />
                                                </div>


                                            </div>

                                        </div>
                                        <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t">
                                            <button type="button"
                                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                                                data-hs-overlay="#hs-static-backdrop-model">
                                                Fermer
                                            </button>
                                            <button type="submit"
                                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                                Ajouter
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>


                    <table class="w-full mt-5 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nom </th>
                                <th scope="col" class="px-6 py-3">type</th>
                                <th scope="col" class="px-6 py-3">prix</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($consCount == 0)

                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">
                                        <div class="flex flex-col justify-center items-center h-40">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucune consommation
                                            </h1>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($consommations as $consommation)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class=" px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">

                                            <div class="ml-3">
                                                <div class="text-base font-semibold">{{ $consommation->name }}</div>

                                            </div>
                                        </td>
                                        <td class="px-6 py-4">{{ $consommation->type }}</td>
                                        <td class="px-6 py-4">{{ $consommation->prix }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex">
                                                <a href="#" data-hs-overlay="#hs-delete-{{ $consommation->id }}"
                                                    class="mr-2 font-medium text-red-600 dark:text-blue-500">
                                                    <button type="submit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </a>
                                            </div>
                                            <div id="hs-delete-{{ $consommation->id }}"
                                                class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                                                <div
                                                    class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                                                    <div
                                                        class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                                                        <div class="absolute top-2 end-2">
                                                            <button type="button"
                                                                class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                                                data-hs-overlay="#hs-delete">
                                                                <span class="sr-only">Close</span>
                                                                <svg class="flex-shrink-0 size-4"
                                                                    xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
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
                                                                <svg class="flex-shrink-0 size-5"
                                                                    xmlns="http://www.w3.org/2000/svg" width="16"
                                                                    height="16" fill="currentColor"
                                                                    viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                                                </svg>
                                                            </span>
                                                            <!-- End Icon -->

                                                            <h3
                                                                class="mb-2 text-2xl font-bold text-gray-800 dark:text-neutral-200">
                                                                Supprimé
                                                            </h3>
                                                            <p class="text-gray-500 dark:text-neutral-500">
                                                                Vous etes sur de supprimé la consommation ?
                                                            </p>

                                                            <div class="mt-6 flex justify-center gap-x-4">
                                                                <form
                                                                    action="{{ route('admin.consprod.destroy', $consommation->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button type="submit"
                                                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                                                        Supprimer
                                                                    </button>
                                                                </form>
                                                                <button type="button"
                                                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                                                    data-hs-overlay="#hs-delete">
                                                                    Annuler
                                                                </button>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <!-- Message d'aucun résultat trouvé -->
                    <div id="noResultMessage" class="h-20 flex justify-center items-center" style="display: none;">
                        Aucun résultat trouvé.
                    </div>

                </div>
                <div class="my-5 flex justify-end">

                </div>

            </div>
            <!-- Transactions List Group Card -->

            <div class="p-4 mt-4 bg-white border border-gray-200 rounded-md">
                <div class="relative overflow-x-auto sm:rounded-lg">
                    <div
                        class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                        <div>
                            <h1 class="font-bold text-md">Transactions</h1>
                        </div>

                        <div class="flex items-center">
                            <label for="table-search" class="sr-only">Search</label>
                            <div class="relative mr-2">
                                <div
                                    class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" onkeyup="searchTable()"
                                    class="block w-80 p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Rechercher...">
                            </div>
                        </div>

                    </div>

                    <hr>
                    @if ($transaCount == 0)
                        <hr>
                        <div class="w-full h-32 flex justify-center items-center">

                            <div class="flex flex-col justify-center items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucune transaction</h1>
                            </div>
                        </div>
                    @else
                        @foreach ($transactions as $transaction)
                            @if (
                                $transaction->type == 'Depot' ||
                                    ($transaction->type == 'Reception' && $transaction->receiver_user_id == $user->id) ||
                                    ($transaction->type == 'Envoie' && $transaction->sender_user_id == $user->id))
                                <div class="w-full flex items-center hover:bg-gray-100 rounded-xl p-4 focus:outline-none focus:bg-gray-100 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                                    href="#">
                                    <!-- Icon -->
                                    @if ($transaction->type == 'Depot')
                                        <div
                                            class="flex items-center justify-center p-1 rounded-md w-10 h-10 mr-2 bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:group-hover:bg-neutral-700 dark:group-focus:bg-neutral-700">

                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                                            </svg>

                                        </div>
                                    @elseif ($transaction->type == 'Reception' && $transaction->receiver_user_id == $user->id)
                                        <div
                                            class="flex items-center justify-center p-1 rounded-md w-10 h-10 mr-2 bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:group-hover:bg-neutral-700 dark:group-focus:bg-neutral-700">

                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                                            </svg>

                                        </div>
                                    @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $user->id)
                                        <div
                                            class="flex items-center justify-center p-1 rounded-md w-10 h-10 mr-2 bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:group-hover:bg-neutral-700 dark:group-focus:bg-neutral-700">

                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                            </svg>

                                        </div>
                                    @endif

                                    <!-- End Icon -->

                                    <div class="w-full">
                                        <div class="flex  justify-between items-center">
                                            <div>
                                                @if ($transaction->type == 'Depot')
                                                    <h4 class="font-medium dark:text-white">Rechargement</h4>
                                                @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $user->id)
                                                    <h4 class="font-medium dark:text-white">Envoyé à

                                                        @if ($transaction->receiverUser)
                                                            {{ $transaction->receiverUser->name }}
                                                        @elseif ($transaction->receiverAdmin)
                                                            {{ $transaction->receiverAdmin->name }}
                                                        @endif


                                                    </h4>
                                                @elseif ($transaction->type == 'Reception' && $transaction->receiver_user_id == $user->id)
                                                    <h4 class="font-medium dark:text-white">Reçu de

                                                        @if ($transaction->senderAdmin)
                                                            {{ $transaction->senderAdmin->name }}
                                                    </h4>
                                                @elseif($transaction->receiverUser)
                                                    {{ $transaction->receiverUser->name }}
                                                @endif
                            @endif
                            <ul class="flex">
                                @if ($transaction->type == 'Envoie' && $transaction->sender_user_id == $user->id)
                                    <li class="mr-2 dark:text-neutral-500">Envoyé le</li>
                                    <li class="dark:text-neutral-500">
                                        {{ $transaction->created_at->translatedFormat('j F Y \à H\hi') }}
                                    </li>
                                @elseif (($transaction->type == 'Reception' && $transaction->receiver_user_id == $user->id) || $transaction->type == 'Depot')
                                    <li class="mr-2 dark:text-neutral-500">Reçu le</li>
                                    <li class="dark:text-neutral-500">
                                        {{ $transaction->created_at->translatedFormat('j F Y \à H\hi') }}
                                    </li>
                                @endif

                            </ul>
                </div>

                <div>
                    @if (($transaction->type == 'Depot' || $transaction->type == 'Reception') && $transaction->receiver_user_id == $user->id)
                        <p class="text-md text-green-400 font-bold dark:text-white">+
                            {{ $transaction->amount }} FCFA</p>
                    @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $user->id)
                        <p class="text-md text-red-600  font-bold dark:text-white">-
                            {{ $transaction->amount }} FCFA</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- End Col -->
    </div>
    @endif
    @endforeach

    <div class="my-5 flex justify-end">
        {{ $transactions->links() }}
    </div>


    @endif


    </div>
    </div>

    </div>
    <div class="lg:col-span-1 col-span-3">
        <div class="flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md">
            <div class="mb-3">
                <h1 class="font-bold text-md">Information personnel</h1>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Nom et pronom</p>
                <p class="text-sm text-gray-400">{{ $user->name }}</p>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Username</p>
                <p class="text-sm text-gray-400">{{ $user->username }}</p>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Agent</p>
                <p class="text-sm text-gray-400">{{ $user->admin->name ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <p class="font-semibold text-sm">Numero de téléphone</p>
                <p class="text-sm text-gray-400">{{ $user->phone }}</p>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Pays de residence</p>
                <p class="text-sm text-gray-400">{{ $user->country }}</p>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Email</p>
                <p class="text-sm text-gray-400">{{ $user->email }}</p>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Nombre de publication</p>
                <p class="text-sm text-gray-400">{{ $produitCount }}</p>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Localité</p>
                <p class="text-sm text-gray-400">{{ $user->local_area }}</p>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Adresse</p>
                <p class="text-sm text-gray-400">{{ $user->address }}</p>
            </div>
            <div class="mb-3">
                <p class="font-semibold text-sm">Zone d'activité</p>
                <p class="text-sm text-gray-400">{{ $user->active_zone }}</p>
            </div>

            <div class="mb-3">
                <p class="font-semibold text-sm">Type d'acteur</p>
                <p class="text-sm text-gray-400">{{ $user->actor_type }}</p>
            </div>
            @if ($user->gender)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Sexe</p>
                    <p class="text-sm text-gray-400">{{ $user->gender }}</p>
                </div>
            @endif

            @if ($user->age)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Tranche d'age</p>
                    <p class="text-sm text-gray-400">{{ $user->age }}</p>
                </div>
            @endif

            @if ($user->social_status)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Status social</p>
                    <p class="text-sm text-gray-400">{{ $user->social_status }}</p>
                </div>
            @endif

            @if ($user->company_size)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Taille d'entreprise</p>
                    <p class="text-sm text-gray-400">{{ $user->company_size }}</p>
                </div>
            @endif

            @if ($user->service_type)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Type de service</p>
                    <p class="text-sm text-gray-400">{{ $user->service_type }}</p>
                </div>
            @endif

            @if ($user->organization_type)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Type d'organisation</p>
                    <p class="text-sm text-gray-400">{{ $user->organization_type }}</p>
                </div>
            @endif

            @if ($user->second_organization_type)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Type d'organisation 2</p>
                    <p class="text-sm text-gray-400">{{ $user->second_organization_type }}</p>
                </div>
            @endif
            @if ($user->communication_type)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Type de communauté</p>
                    <p class="text-sm text-gray-400">{{ $user->communication_type }}</p>
                </div>
            @endif

            @if ($user->mena_type)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Type de nenage</p>
                    <p class="text-sm text-gray-400">{{ $user->mena_type }}</p>
                </div>
            @endif

            @if ($user->mena_status)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Statut menage</p>
                    <p class="text-sm text-gray-400">{{ $user->mena_type }}</p>
                </div>
            @endif

            <div class="mb-3">
                <p class="font-semibold text-sm">Secteur d'activité</p>
                <p class="text-sm text-gray-400">{{ $user->sector }}</p>
            </div>

            @if ($user->industry)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Industrie</p>
                    <p class="text-sm text-gray-400">{{ $user->industry }}</p>
                </div>
            @endif
            @if ($user->construction)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Type de batiment</p>
                    <p class="text-sm text-gray-400">{{ $user->construction }}</p>
                </div>
            @endif
            @if ($user->commerce)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Commerce</p>
                    <p class="text-sm text-gray-400">{{ $user->commerce }}</p>
                </div>
            @endif
            @if ($user->services)
                <div class="mb-3">
                    <p class="font-semibold text-sm">Service</p>
                    <p class="text-sm text-gray-400">{{ $user->services }}</p>
                </div>
            @endif


            @auth('admin')
                @if (Auth::guard('admin')->user()->admin_type == 'admin')
                    <!-- Contenu spécifique à l'agent -->

                    <!-- Autres éléments spécifiques à l'agent -->

                    <a href="{{ route('client.editad', ['username' => $user->username]) }}"
                        class="w-full  text-blue-800 bg-blue-100 rounded-md text-center p-1 mb-3">
                        Modifier agent

                    </a>

                    <a href="#" data-hs-overlay="#hs-delete1"
                        class="w-full  text-red-800 bg-red-100 rounded-md text-center p-1 ">
                        Supprimé client

                    </a>
                @endif
            @endauth



            <div id="hs-delete1"
                class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
                <div
                    class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                    <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                        <div class="absolute top-2 end-2">
                            <button type="button"
                                class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-neutral-700"
                                data-hs-overlay="#hs-delete1">
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
                                Vous etes sur de supprimé le compte de l'utilisateur ?
                            </p>

                            <div class="mt-6 flex justify-center gap-x-4">
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-white dark:hover:bg-neutral-800">
                                        Supprimé
                                    </button>
                                </form>
                                <button type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                    data-hs-overlay="#hs-delete1">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>
    </div>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview1');
            const removeButton = document.getElementById('remove-button1');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                removeButton.classList.remove('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
                removeButton.classList.add('hidden');
            }
        }

        function removeImage() {
            const preview = document.getElementById('image-preview1');
            const removeButton = document.getElementById('remove-button1');
            const fileInput = document.getElementById('file-upload1');

            preview.src = '';
            preview.classList.add('hidden');
            removeButton.classList.add('hidden');
            fileInput.value = ''; // Clear the file input
        }

        function previewImage2(input) {
            const preview = document.getElementById('image-preview2');
            const removeButton = document.getElementById('remove-button2');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                removeButton.classList.remove('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
                removeButton.classList.add('hidden');
            }
        }

        function removeImage2() {
            const preview = document.getElementById('image-preview2');
            const removeButton = document.getElementById('remove-button2');
            const fileInput = document.getElementById('file-upload2');

            preview.src = '';
            preview.classList.add('hidden');
            removeButton.classList.add('hidden');
            fileInput.value = ''; // Clear the file input
        }

        function previewImage3(input) {
            const preview = document.getElementById('image-preview3');
            const removeButton = document.getElementById('remove-button3');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                removeButton.classList.remove('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
                removeButton.classList.add('hidden');
            }
        }

        function removeImage3() {
            const preview = document.getElementById('image-preview3');
            const removeButton = document.getElementById('remove-button3');
            const fileInput = document.getElementById('file-upload3');

            preview.src = '';
            preview.classList.add('hidden');
            removeButton.classList.add('hidden');
            fileInput.value = ''; // Clear the file input
        }

        function previewImage4(input) {
            const preview = document.getElementById('image-preview4');
            const removeButton = document.getElementById('remove-button4');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                removeButton.classList.remove('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
                removeButton.classList.add('hidden');
            }
        }

        function removeImage4() {
            const preview = document.getElementById('image-preview4');
            const removeButton = document.getElementById('remove-button4');
            const fileInput = document.getElementById('file-upload3');

            preview.src = '';
            preview.classList.add('hidden');
            removeButton.classList.add('hidden');
            fileInput.value = ''; // Clear the file input
        }

        function previewImage5(input) {
            const preview = document.getElementById('image-preview5');
            const removeButton = document.getElementById('remove-button5');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                removeButton.classList.remove('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
                removeButton.classList.add('hidden');
            }
        }

        function removeImage5() {
            const preview = document.getElementById('image-preview5');
            const removeButton = document.getElementById('remove-button5');
            const fileInput = document.getElementById('file-upload5');

            preview.src = '';
            preview.classList.add('hidden');
            removeButton.classList.add('hidden');
            fileInput.value = ''; // Clear the file input
        }
    </script>

    <script src="{{ asset('js/search2.js') }}"></script>
    <script src="{{ asset('js/affichage_champs.js') }}"></script>
@endsection
