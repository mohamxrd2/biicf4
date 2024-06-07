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

            @if ($errors->any())
                <div class="bg-red-200 text-red-800 px-4 py-2 rounded-md mb-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
    
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
                        data-hs-stepper-nav-item='{
              "index": 1
            }'>
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
                        data-hs-stepper-nav-item='{
              "index": 2
            }'>
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
                                Nature d'acteur
                            </span>
                        </span>
                        <div
                            class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-teal-600">
                        </div>
                    </li>
    
                    <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group"
                        data-hs-stepper-nav-item='{
                "index": 3
              }'>
                        <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                            <span
                                class="size-7 flex justify-center items-center flex-shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full group-focus:bg-gray-200 hs-stepper-active:bg-blue-600 hs-stepper-active:text-white hs-stepper-success:bg-blue-600 hs-stepper-success:text-white hs-stepper-completed:bg-teal-500 hs-stepper-completed:group-focus:bg-teal-600">
                                <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">3</span>
                                <svg class="hidden flex-shrink-0 size-3 hs-stepper-success:block"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium text-gray-800">
                                Secteur d'activité
                            </span>
                        </span>
                        <div
                            class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-teal-600">
                        </div>
                    </li>
    
                    <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group"
                        data-hs-stepper-nav-item='{
                "index": 4
              }'>
                        <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                            <span
                                class="size-7 flex justify-center items-center flex-shrink-0 bg-gray-100 font-medium text-gray-800 rounded-full group-focus:bg-gray-200 hs-stepper-active:bg-blue-600 hs-stepper-active:text-white hs-stepper-success:bg-blue-600 hs-stepper-success:text-white hs-stepper-completed:bg-teal-500 hs-stepper-completed:group-focus:bg-teal-600">
                                <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">4</span>
                                <svg class="hidden flex-shrink-0 size-3 hs-stepper-success:block"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium text-gray-800">
                                Contact
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
                        <div data-hs-stepper-content-item='{
              "index": 1
            }'>
                            <div
                                class="p-4 bg-gray-50 flex flex-col justify-center items-center border border-dashed border-gray-200 rounded-xl h-full">
    
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
    
                                @include('admin.components.input', [
                                    'type' => 'text',
                                    'name' => 'username',
                                    'placeholder' => 'Nom d\'utlisateur',
                                ])
    
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
    
    
                            </div>
    
                        </div>
                        <!-- End First Contnet -->
    
                        <!-- First Contnet -->
                        <div data-hs-stepper-content-item='{
              "index": 2
            }' style="display: none;">
                            <div
                                class="p-4 bg-gray-50 flex flex-col justify-center items-center border border-dashed border-gray-200 rounded-xl h-full">
    
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
    
                                @include('admin.components.select', [
                                    'name' => 'user_sexe',
                                    'title' => 'Sexe',
                                    'options' => ['Masculin', 'Feminin'],
                                ])
    
                                @include('admin.components.select', [
                                    'name' => 'user_age',
                                    'title' => 'Tranche d\'age',
                                    'options' => ['Adolescent', 'Jeune', '3ème Age'],
                                ])
    
                                @include('admin.components.select', [
                                    'name' => 'user_status',
                                    'title' => 'Status social',
                                    'options' => ['Salarié', 'Travailleur', 'Autonome', 'Etudiant', 'Sans emploi'],
                                ])
    
    
                                @include('admin.components.select', [
                                    'name' => 'user_comp_size',
                                    'title' => 'Taille d\'entreprise',
                                    'options' => [
                                        'Grande entreprise',
                                        'Moyenne entreprise',
                                        'Petite entreprise',
                                        'Mini entreprise',
                                        'Micro entreprise',
                                    ],
                                    // Ajoutez d'autres attributs au besoin
                                ])
    
                                @include('admin.components.select', [
                                    'name' => 'user_serv',
                                    'title' => 'Type de service',
                                    'options' => [
                                        'Service ministeriel',
                                        'Administration publique',
                                        'Collectivité territoriale',
                                    ],
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'user_orgtyp',
                                    'title' => 'Type d\'organismes',
                                    'options' => ['National', 'International'],
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'user_orgtyp2',
                                    'title' => 'Choisir',
                                    'options' => ['ONG', 'Institution', 'Programme', 'Projet'],
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'user_com',
                                    'title' => 'Type de communauté',
                                    'options' => [
                                        'Localité',
                                        'Communauté',
                                        'Syndicat',
                                        'Mutuelle',
                                        'Association',
                                        'Club',
                                    ],
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'user_mena1',
                                    'title' => 'Type de ménage',
                                    'options' => ['Urbain', 'Rural'],
                                    // Ajoutez d'autres attributs au besoin
                                ])
    
                                @include('admin.components.select', [
                                    'name' => 'user_mena2',
                                    'title' => 'Statut',
                                    'options' => [
                                        'Salarié',
                                        'Entreprise',
                                        'Commerçant',
                                        'Producteur agricole',
                                        'Artisan',
                                        'Ouvrier',
                                        'Autre',
                                    ],
                                    // Ajoutez d'autres attributs au besoin
                                ])
    
    
    
    
                            </div>
                        </div>
                        <!-- End First Contnet -->
    
                        <!-- First Contnet -->
                        <div data-hs-stepper-content-item='{
              "index": 3
            }' style="display: none;">
                            <div
                                class="p-4 bg-gray-50 flex flex-col justify-center items-center border border-dashed border-gray-200 rounded-xl h-full">
                                @include('admin.components.select', [
                                    'name' => 'sector_activity',
                                    'title' => 'Secteur d\'activité',
                                    'options' => ['Industrie', 'Construction', 'Commerce', 'Service', 'Autre'],
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'industry',
                                    'title' => 'Industrie',
                                    'options' => [
                                        'Alimentaires',
                                        'Boissons',
                                        'Tabac',
                                        'Bois',
                                        'Papier',
                                        'Imprimerie',
                                        'Chimique',
                                        'Pharmaceutique',
                                        'Caoutchouc et plastique',
                                        'Produits non métalliques',
                                        'Métallurgie et produits métalliques',
                                        'Machines et équipements',
                                        'Matériels de transport',
                                        'Réparation et installation de machines et d\'équipements',
                                        'Distribution d\'électricité',
                                        'Distribution de gaz',
                                    ],
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'building_type',
                                    'title' => 'Type de bâtiment',
                                    'options' => ['Habitation', 'Usine', 'Pont & Chaussée'],
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'commerce_sector',
                                    'title' => 'Secteur d\'activité',
                                    'options' => ['Commerce', 'Réparation d\'automobiles et de motocycles'],
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'transport_sector',
                                    'title' => 'Secteur d\'activité',
                                    'options' => [
                                        'Transports et entreposage',
                                        'Hébergement et restauration',
                                        'Activités financières et d\'assurance',
                                        'Activités immobilières',
                                        'Service juridiques',
                                        'Service comptables',
                                        'Service de gestion',
                                        'Service d\'architecture',
                                        'Service d\'ingénierie',
                                        'Service de contrôle et d\'analyses techniques',
                                        'Autres activités spécialisées, scientifiques et techniques',
                                        'Services administratifs',
                                        'Service de soutien',
                                        'Administration publique',
                                        'Enseignement',
                                        'Service santé humaine',
                                        'Arts, spectacles et activités récréatives',
                                        'Autres activités de services',
                                    ],
                                    // Ajoutez d'autres attributs au besoin
                                ])
    
    
                            </div>
                        </div>
    
                        <!-- First Contnet -->
                        <div data-hs-stepper-content-item='{
                "index": 4
              }' style="display: none;">
                            <div
                                class="p-4 bg-gray-50 flex flex-col justify-center items-center border border-dashed border-gray-200 rounded-xl h-full">
    
                                @include('admin.components.select', [
                                    'name' => 'country',
                                    'title' => 'Choisissez un pays',
                                    'options' => [],
    
                                ])
                                @include('admin.components.input', [
                                    'name' => 'phone',
                                    'type' => 'tel',
                                    'placeholder' => 'Ex: +225 06 12 34 56 78',
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.input', [
                                    'name' => 'local',
                                    'type' => 'text',
                                    'placeholder' => 'Localité',
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.input', [
                                    'name' => 'adress_geo',
                                    'type' => 'text',
                                    'placeholder' => 'Adresse',
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.input', [
                                    'name' => 'email',
                                    'type' => 'email',
                                    'placeholder' => 'Email',
                                    // Ajoutez d'autres attributs au besoin
                                ])
                                @include('admin.components.select', [
                                    'name' => 'proximity',
                                    'title' => 'Zone d\'activité',
                                    'options' => [
                                        'Proximité',
                                        'Locale',
                                        'Nationale',
                                        'Sous Régionale',
                                        'Continentale',
                                        'Internationale',
                                    ],
                                ])
                                <input type="number"
                                class="py-3 px-4 mb-2 block w-full lg:w-1/2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                placeholder="Code de parrainage (Optionnel)">
                            </div>
                        </div>
                        <!-- End First Contnet -->
    
                        <!-- Final Contnet -->
                        <div data-hs-stepper-content-item='{
              "isFinal": true
            }' style="display: none;">
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

    


    

    


    <script src="{{ asset('js/country.js') }}"></script>
    <script src="{{ asset('js/select.js') }}"></script>
</body>
</html>