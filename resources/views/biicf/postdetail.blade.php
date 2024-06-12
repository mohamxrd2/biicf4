@extends('biicf.layout.navside')

@section('title', 'Details')

@section('content')


    <div class="max-w-7xl mx-auto p-4 grid lg:grid-cols-5 gap-4 ">
        <!-- Left Side: Image -->
        <div class="lg:h-500px fixed-image lg:col-span-3 col-span-5">
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
                                                src="{{ asset($photo) }}" alt="Image">
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="flex justify-center h-full bg-gray-100 dark:bg-neutral-900">
                        <img class="max-w-full h-auto rounded-md" src="{{ asset('img/noimg.jpeg') }}" alt="Image">
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
        </div>




        <!-- Right Side: Product Details -->
        <div class="lg:h-500px h-auto overflow-y-auto p-4 lg:col-span-2 col-span-5">
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
                    <button class="w-1/2 bg-purple-500 text-white py-2 mr- rounded-xl" id="btnAchatDirect">Achat
                        direct</button>
                    <button class="w-1/2 bg-green-500 text-white py-2 ml-2 rounded-xl" id="btnAchatGroup">Achat
                        groupé</button>
                </div>
            @else
                <button class="w-full bg-red-500 text-white py-2 mr- rounded-xl"
                    data-hs-overlay="#hs-delete-{{ $produit->id }}">Supprimé produit</button>

                <button class="w-full mt-3 bg-green-500 text-white py-2 mr- rounded-xl"
                    data-hs-overlay="#hs-offre-{{ $produit->id }}">fais une offre(clients potentiels)</button>

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
                                    le nombre de clients potentiels sont ({{ $nombreProprietaires }})
                                </p>

                                <div class="mt-6 flex justify-center gap-x-4">
                                    <form action="{{ route('biicf.sendoffre', $produit->id) }}" method="POST">
                                        @csrf
                                        @method('POST')

                                        <!-- Champ caché pour l'ID du produit -->
                                        <input type="hidden" name="produit_id" value="{{ $produit->id }}">

                                        <button type="submit"
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
            @endif

            <!-- Afficher les messages de succès -->
            @if (session('success'))
                <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mt-3">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Afficher les messages d'erreur -->
            @if (session('error'))
                <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mt-3">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Formulaire pour l'achat direct -->
            <form action="{{ route('achatD.store', ['id' => $id]) }}" id="formAchatDirect"
                class="mt-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md" style="display: none;"
                method="POST">
                @csrf
                @method('POST')
                <h1 class="text-xl text-center mb-3">Achat direct</h1>

                <div class="space-y-3 mb-3 w-full">
                    <input type="number" id="quantityInput" name="quantité"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Quantité" data-min="{{ $produit->qteProd_min }}"
                        data-max="{{ $produit->qteProd_max }}" oninput="updateMontantTotalDirect()" required>
                </div>

                <div class="space-y-3 mb-3 w-full">
                    <input type="text" id="locationInput" name="localite"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Lieu de livraison" required>
                </div>

                <div class="space-y-3 mb-3 w-full">
                    <input type="text" id="specificite" name="specificite"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Specificité (Facultatif)">
                </div>

                <input type="hidden" name="userTrader" value="{{ $produit->user->id }}">
                <input type="hidden" name="nameProd" value="{{ $produit->name }}">
                <input type="hidden" name="userSender" value="{{ $userId }}">
                <input type="hidden" name="message" value="Un utilisateur veut acheter ce produit">
                <input type="hidden" name="photoProd" value="{{ $produit->photoProd1 }}">
                <input type="hidden" name="idProd" value="{{ $produit->id }}">

                <div class="flex justify-between px-4 mb-3 w-full">
                    <p class="font-semibold text-sm text-gray-500">Prix total:</p>
                    <p class="text-sm text-purple-600" id="montantTotal">0 FCFA</p>
                    <input type="text" name="montantTotal" id="montant_total_input" hidden>
                </div>

                <p id="errorMessage" class="text-sm text-center text-red-500 hidden">Erreur</p>

                <div class="w-full text-center mt-3">
                    <button type="reset"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-200 text-black hover:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none">Annulé</button>
                    <button type="submit" id="submitButton"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                        disabled>Envoyé</button>
                </div>
            </form>

            <!-- Formulaire pour l'achat groupé -->
            <form action="{{ route('achatG.store', ['id' => $id]) }}"
                class="mt-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md" id="formAchatGroup"
                style="display: none;" method="POST">
                @csrf
                @method('POST')
                <h1 class="text-xl text-center mb-3">Achat groupé</h1>


                <div>
                    <p class="text-center text-md font-medium text-gray-700 mb-3">Nombre de participants: <span
                            class="text-md  text-purple-800">{{ $nbreAchatGroup }}</span></p>
                </div>

                <div class="space-y-3 mb-3 w-full">
                    <input type="number" id="quantityInput1" name="quantité"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Quantité" data-min="{{ $produit->qteProd_min }}"
                        data-max="{{ $produit->qteProd_max }}" oninput="updateMontantTotalGroup()" required>
                </div>

                <div class="space-y-3 mb-3 w-full">
                    <input type="text" id="locationInput1" name="localite"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Lieu de livraison" required>
                </div>

                <div class="space-y-3 mb-3 w-full">
                    <input type="text" id="specificite1" name="specificite"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Specificité (Facultatif)">
                </div>

                <input type="hidden" name="userTrader" value="{{ $produit->user->id }}">
                <input type="hidden" name="nameProd" value="{{ $produit->name }}">
                <input type="hidden" name="userSender" value="{{ $userId }}">
                <input type="hidden" name="message" value="Un utilisateur veut acheter ce produit">
                <input type="hidden" name="photoProd" value="{{ $produit->photoProd1 }}">
                <input type="hidden" name="idProd" value="{{ $produit->id }}">

                <div class="flex justify-between px-4 mb-3 w-full">
                    <p class="font-semibold text-sm text-gray-500">Prix total:</p>
                    <p class="text-sm text-purple-600" id="montantTotal1">0 FCFA</p>
                    <input type="text" name="montantTotal" id="montant_total_input1" hidden>
                </div>

                <p id="errorMessage1" class="text-sm text-center text-red-500 hidden">Erreur</p>

                <div class="w-full text-center mt-3">
                    <button type="reset"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-200 text-black hover:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none">Annulé
                    </button>
                    <button type="submit" id="submitButton1"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                        disabled>Envoyé
                    </button>
                </div>


                @if ($nbreAchatGroup > 0 && isset($userId) && isset($produit->user->id) && $userId != $produit->user->id)
                    <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">
                        <span class="mb-2">Temps restant pour cet achat groupé</span>
                        <div id="countdown"
                            class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100 p-3 rounded-xl w-auto">
                            <div>-</div>:
                            <div>-</div>:
                            <div>-</div>:
                            <div>-</div>
                        </div>
                    </div>
                @endif
            </form>


        </div>







    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnAchatDirect = document.getElementById('btnAchatDirect');
            const btnAchatGroup = document.getElementById('btnAchatGroup');
            const formAchatDirect = document.getElementById('formAchatDirect');
            const formAchatGroup = document.getElementById('formAchatGroup');

            btnAchatDirect.addEventListener('click', function() {
                if (formAchatDirect.style.display === 'none' || formAchatDirect.style.display === '') {
                    formAchatDirect.style.display = 'block';
                    formAchatGroup.style.display = 'none';
                } else {
                    formAchatDirect.style.display = 'none';
                }
            });

            btnAchatGroup.addEventListener('click', function() {
                if (formAchatGroup.style.display === 'none' || formAchatGroup.style.display === '') {
                    formAchatGroup.style.display = 'block';
                    formAchatDirect.style.display = 'none';
                } else {
                    formAchatGroup.style.display = 'none';
                }
            });
        });

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

        // Fonction pour mettre à jour le montant total pour l'achat direct
        function updateMontantTotalDirect() {
            const quantityInput = document.getElementById('quantityInput');
            const price = parseFloat(document.querySelector('[data-price]').getAttribute('data-price'));
            const minQuantity = parseInt(quantityInput.getAttribute('data-min'));
            const maxQuantity = parseInt(quantityInput.getAttribute('data-max'));
            const quantity = parseInt(quantityInput.value);
            const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
            const montantTotalElement = document.getElementById('montantTotal');
            const errorMessageElement = document.getElementById('errorMessage');
            const submitButton = document.getElementById('submitButton');
            const montantTotalInput = document.getElementById('montant_total_input');

            const userBalance = {{ $userWallet->balance }};

            if (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity) {
                errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
                errorMessageElement.classList.remove('hidden');
                montantTotalElement.innerText = '0 FCFA';
                submitButton.disabled = true;
            } else if (montantTotal > userBalance) {
                errorMessageElement.innerText =
                    `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
                errorMessageElement.classList.remove('hidden');
                montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
                submitButton.disabled = true;
            } else {
                errorMessageElement.classList.add('hidden');
                montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
                montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input
                submitButton.disabled = false;
            }
        }

        // Fonction pour mettre à jour le montant total pour l'achat groupé
        function updateMontantTotalGroup() {
            const quantityInput = document.getElementById('quantityInput1');
            const price = parseFloat(document.querySelector('[data-price]').getAttribute('data-price'));
            const minQuantity = parseInt(quantityInput.getAttribute('data-min'));
            const maxQuantity = parseInt(quantityInput.getAttribute('data-max'));
            const quantity = parseInt(quantityInput.value);
            const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
            const montantTotalElement = document.getElementById('montantTotal1');
            const errorMessageElement = document.getElementById('errorMessage1');
            const submitButton = document.getElementById('submitButton1');
            const montantTotalInput = document.getElementById('montant_total_input1');

            const userBalance = {{ $userWallet->balance }};

            if (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity) {
                errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
                errorMessageElement.classList.remove('hidden');
                montantTotalElement.innerText = '0 FCFA';
                submitButton.disabled = true;
            } else if (montantTotal > userBalance) {
                errorMessageElement.innerText =
                    `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
                errorMessageElement.classList.remove('hidden');
                montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
                submitButton.disabled = true;
            } else {
                errorMessageElement.classList.add('hidden');
                montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
                montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input
                submitButton.disabled = false;
            }
        }



        // Convertir la date de départ en objet Date JavaScript
        const startDate = new Date("{{ $datePlusAncienne }}");

        // Ajouter 5 jours à la date de départ
        startDate.setDate(startDate.getDate() + 5);

        // Mettre à jour le compte à rebours à intervalles réguliers
        const countdownTimer = setInterval(updateCountdown, 1000);

        function updateCountdown() {
            // Obtenir la date et l'heure actuelles
            const currentDate = new Date();

            // Calculer la différence entre la date cible et la date de départ en millisecondes
            const difference = startDate.getTime() - currentDate.getTime();

            // Convertir la différence en jours, heures, minutes et secondes
            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((difference % (1000 * 60)) / 1000);

            // Afficher le compte à rebours dans l'élément HTML avec l'id "countdown"
            const countdownElement = document.getElementById('countdown');
            countdownElement.innerHTML = `
             <div>${days}j</div>:
             <div>${hours}h</div>:
             <div>${minutes}m</div>:
            <div>${seconds}s</div>
              `;

            // Arrêter le compte à rebours lorsque la date cible est atteinte
            if (difference <= 0) {
                clearInterval(countdownTimer);
                countdownElement.innerHTML = "Temps écoulé !";
            }
        }
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
