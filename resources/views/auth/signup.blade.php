<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Créer un compte</title>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div class="h-[100vh] flex justify-center items-center p-4">

        <div class="p-4 bg-white w-full ">


            <p class="text-center my-5 text-2xl">Creer un compte</p>



            @if (session('success'))
                <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <!-- Stepper -->
            <div data-hs-stepper="" class="">
                <!-- Stepper Nav -->
                <ul class="relative flex flex-row gap-x-2 overflow-x-auto">
                    <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group"
                        data-hs-stepper-nav-item='{ "index": 1 }'>
                        <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                            <span
                                class="size-7 flex justify-center items-center flex-shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full group-focus:bg-gray-200 hs-stepper-active:bg-blue-600 hs-stepper-active:text-white hs-stepper-success:bg-blue-600 hs-stepper-success:text-white hs-stepper-completed:bg-teal-500 hs-stepper-completed:group-focus:bg-teal-600">
                                <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">1</span>
                                <svg class="hidden flex-shrink-0 size-3 hs-stepper-success:block"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium text-gray-800">
                                Information personnel
                            </span>
                        </span>
                        <div
                            class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-teal-600">

                        </div>
                    </li>

                    <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group"
                        data-hs-stepper-nav-item='{ "index": 2  }'>
                        <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                            <span
                                class="size-7 flex justify-center items-center flex-shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full group-focus:bg-gray-200 hs-stepper-active:bg-blue-600 hs-stepper-active:text-white hs-stepper-success:bg-blue-600 hs-stepper-success:text-white hs-stepper-completed:bg-teal-500 hs-stepper-completed:group-focus:bg-teal-600">
                                <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">2</span>
                                <svg class="hidden flex-shrink-0 size-3 hs-stepper-success:block"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium text-gray-800">
                                Localisation & Contact
                            </span>
                        </span>
                        <div
                            class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-teal-600">
                        </div>
                    </li>
                    <!-- End Item -->
                </ul>
                <!-- End Stepper Nav -->
                <form action="{{ route('biicf.signup') }}" method="POST">
                    @csrf
                    <!-- Stepper Content -->
                    <div class="mt-5 sm:mt-8">
                        <!-- First Contnet -->
                        <div data-hs-stepper-content-item='{"index": 1 }'>

                            <div
                                class="p-4 bg-gray-50 flex flex-col justify-center items-center gap-2 border border-dashed border-gray-200 rounded-xl h-full">


                                <div class="flex justify-start lg:w-1/2">
                                    @include('admin.components.input', [
                                        'type' => 'text',
                                        'name' => 'name',
                                        'placeholder' => 'Nom ou raison social',
                                    ])



                                    @include('admin.components.input', [
                                        'type' => 'text',
                                        'name' => 'last-name',
                                        'placeholder' => 'Prénom',
                                    ])
                                </div>


                                <div class="flex justify-start lg:w-1/2">
                                    @include('admin.components.input2', [
                                        'type' => 'text',
                                        'name' => 'username',
                                        'placeholder' => 'Nom d\'utlisateur',
                                    ])
                                    @include('admin.components.input2', [
                                        'name' => 'email',
                                        'type' => 'email',
                                        'placeholder' => 'Email',
                                        // Ajoutez d'autres attributs au besoin
                                    ])
                                </div>

                                @include('admin.components.input', [
                                    'type' => 'password',
                                    'name' => 'password',
                                    'placeholder' => 'Mot de passe',
                                ])

                                @include('admin.components.input', [
                                    'type' => 'password',
                                    'name' => 'repeat-password',
                                    'placeholder' => 'Confirmer mot de passe',
                                ])

                                @include('admin.components.select', [
                                    'name' => 'user_type',
                                    'title' => 'Type d\'acteur',
                                    'options' => [
                                        'Personne physique',
                                        'Personne morale',
                                        'Service public',
                                        'Organisme',
                                        'Communauté',
                                        'Menage',
                                    ],
                                ])
                                <div>

                                    <label for="investisement">Proportion à investir (la somme minimale)</label>
                                </div>
                                @include('admin.components.select', [
                                    'name' => 'investisement',
                                    'title' => 'Choisissez une tranche(FCFA)',
                                    'options' => [
                                        '1-500.000',
                                        '500.001-1.000.000',
                                        '1.000.001-5.000.000',
                                        '5.000.001-10.000.000',
                                        '10.000.001-50.000.000',
                                        '50.000.001 et plus',
                                    ],
                                ])



                            </div>


                        </div>
                        <!-- End First Contnet -->

                        <!-- First Contnet -->
                        <div data-hs-stepper-content-item='{"index": 2 }' style="display: none;">
                            <div
                                class="p-4 bg-gray-50 flex flex-col justify-center items-center border border-dashed border-gray-200 rounded-xl h-full">
                                <input id="phone" type="tel"
                                    class="py-3 px-4 mb-2 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">


                                <select id="address-country"
                                    class="py-3 px-4 mb-2 block w-full lg:w-1/2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"></select>


                                @include('admin.components.input', [
                                    'name' => 'departement',
                                    'type' => 'text',
                                    'placeholder' => 'Saisissez votre departement',
                                    // Ajoutez d'autres attributs au besoin
                                ])

                                @include('admin.components.input', [
                                    'name' => 'ville',
                                    'type' => 'text',
                                    'placeholder' => 'Saisissez votre ville',
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.input', [
                                    'name' => 'commune',
                                    'type' => 'text',
                                    'placeholder' => 'Saisissez votre commune',
                                    // Ajoutez d'autres attributs au besoin
                                ])

                                <input type="number"
                                    class="py-3 px-4 mb-2 block w-full lg:w-1/2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                    placeholder="Code de parrainage (Optionnel)">


                                <!-- Affichage du continent et de la sous-région -->

                                <p id="continent">Continent: </p>
                                <p id="subregion">Sous-région: </p>

                                <!-- CSS for intl-tel-input -->
                                <link rel="stylesheet"
                                    href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

                                <!-- JS for intl-tel-input -->
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

                                <script>
                                    // Sélection des éléments DOM
                                    const input = document.querySelector("#phone");
                                    const addressDropdown = document.querySelector("#address-country");
                                    const continentElement = document.querySelector("#continent");
                                    const subregionElement = document.querySelector("#subregion");

                                    // Obtenez les données des pays via intl-tel-input
                                    const countryData = window.intlTelInputGlobals.getCountryData();

                                    // Remplir le sélecteur de pays dans l'adresse
                                    for (let i = 0; i < countryData.length; i++) {
                                        const country = countryData[i];
                                        const optionNode = document.createElement("option");
                                        optionNode.value = country.iso2;
                                        const textNode = document.createTextNode(country.name);
                                        optionNode.appendChild(textNode);
                                        addressDropdown.appendChild(optionNode);
                                    }

                                    // Initialisation du plugin intl-tel-input sur le champ du téléphone
                                    const iti = window.intlTelInput(input, {
                                        initialCountry: "CI", // Définit un pays par défaut (exemple: États-Unis)
                                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js" // Pour le formatage et les placeholders
                                    });

                                    // Synchroniser le sélecteur de pays avec le champ de téléphone
                                    addressDropdown.value = iti.getSelectedCountryData().iso2;

                                    // Fonction pour récupérer continent et sous-région via l'API Restcountries
                                    async function getRegionData(countryCode) {
                                        try {
                                            const response = await fetch(`https://restcountries.com/v3.1/alpha/${countryCode}`);
                                            const data = await response.json();
                                            const countryInfo = data[0];

                                            // Mettre à jour le contenu du DOM avec le continent et la sous-région
                                            continentElement.textContent = `Continent: ${countryInfo.region}`;
                                            subregionElement.textContent = `Sous-région: ${countryInfo.subregion}`;
                                        } catch (error) {
                                            console.error("Erreur lors de la récupération des informations régionales:", error);
                                        }
                                    }

                                    // Mettre à jour les infos lors d'un changement dans le champ de téléphone
                                    input.addEventListener('countrychange', () => {
                                        const countryCode = iti.getSelectedCountryData().iso2;
                                        addressDropdown.value = countryCode;
                                        getRegionData(countryCode); // Récupérer et afficher les infos régionales
                                    });

                                    // Mettre à jour le champ de téléphone et les infos régionales lors du changement de pays
                                    addressDropdown.addEventListener('change', () => {
                                        const countryCode = addressDropdown.value;
                                        iti.setCountry(countryCode);
                                        getRegionData(countryCode); // Récupérer et afficher les infos régionales
                                    });

                                    // Récupérer les infos régionales pour le pays par défaut
                                    getRegionData(iti.getSelectedCountryData().iso2);
                                </script>

                            </div>
                        </div>
                        <!-- End First Contnet -->

                        <!-- Final Contnet -->
                        <div data-hs-stepper-content-item='{"isFinal": true}' style="display: none;">
                            <div
                                class="p-4 h-48 bg-gray-50 flex justify-center items-center border border-dashed border-gray-200 rounded-xl">
                                <h3>
                                    Enregistrement terminé !
                                </h3>
                            </div>
                        </div>
                        <!-- End Final Contnet -->

                        <!-- Button Group -->
                        <div class="mt-5 flex justify-between items-center gap-x-2">
                            <button type="button"
                                class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                                data-hs-stepper-back-btn="">
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m15 18-6-6 6-6"></path>
                                </svg>
                                Retour
                            </button>
                            <button type="button"
                                class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                data-hs-stepper-next-btn="">
                                Suivant
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </button>
                            <button type="submit"
                                class=" py-2 px-3 inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                data-hs-stepper-finish-btn="" style="display: none;">
                                Terminé
                            </button>
                            <button type="button"
                                class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                                data-hs-stepper-reset-btn="" style="display: none;">
                                Reinitialisé
                            </button>
                        </div>
                        <!-- End Button Group -->
                    </div>
                    <!-- End Stepper Content -->

                </form>
                <div class="w-full mt-8 text-center">
                    <p class="mt-2 text-sm text-gray-600">
                        Vous avez déjà un compte?
                        <a class="text-blue-600 decoration-2  font-medium" href="{{ route('biicf.login') }}">
                            Se connecter
                        </a>
                    </p>

                </div>

            </div>
            <!-- End Stepper -->
        </div>



    </div>

</body>

</html>
