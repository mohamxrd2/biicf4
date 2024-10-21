<div>
    {{-- <div class="max-w-lg mx-auto p-6 bg-white shadow-lg rounded-lg mb-4">
        <!-- Bouton pour vérifier l'utilisateur -->
        <button id="requestCreditButton" wire:click="verifyUser" wire:loading.attr="disabled"
            class="py-2 px-3 w-full inline-flex items-center justify-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
            <span wire:loading.remove>Vérification</span>
            <span wire:loading>Envoi en cours...</span>
        </button>

        <!-- Message affiché après la vérification -->
        @if ($message)
            <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                <p class="text-sm text-gray-700">{{ $message }}</p>
            </div>
        @endif
    </div> --}}



    {{-- @if ($isEligible) --}}
    <form wire:submit.prevent="submit" class="">
        <div class="mx-auto p-6 bg-white shadow-lg rounded-lg space-y-6">
            @if ($successMessage)
                <div class="bg-green-500 text-white p-4 rounded-md mt-2 mb-6">
                    {{ $successMessage }}
                </div>
            @endif

            @if ($errors->has('submitError'))
                <div class="bg-red-500 text-white p-4 rounded-md mt-2 mb-6">
                    {{ $errors->first('submitError') }}
                </div>
            @endif
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Ajouter un projet</h2>

            <div x-data="{ typeFinancement: '' }">


                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-3">
                    <div class="col-span-2">

                        <p class="text-xl font-bold text-gray-800 underline">INFORMATION SUR LE PROJET</p>
                    </div>

                    <div class="w-full">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom du projet</label>
                        <input type="text" wire:model="name" id="name"
                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                            placeholder="Entrez le nom du projet">
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Montant -->
                    <div class="w-full">
                        <label for="montant" class="block text-sm font-medium text-gray-700">Montant</label>
                        <input wire:model="montant" type="number" id="montant"
                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                            placeholder="Entrez le montant">
                        @error('montant')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Taux -->
                    <div class="w-full">
                        <label for="taux" class="block text-sm font-medium text-gray-700">Taux(%)</label>
                        <input wire:model="taux" type="number" id="taux"
                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                            placeholder="Entrez le taux">
                        @error('taux')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                        <select id="categorie" wire:model="categorie" required
                            class="mt-1 block w-full border  border-gray-300 rounded-md p-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="" disabled>Sélectionnez une catégorie</option>

                            <!-- Catégories tirées du document -->
                            <option value="AGRICULTURE VIVRIERE">AGRICULTURE VIVRIERE</option>
                            <option value="AGRICULTURE DESTINEE A L'INDUSTRIE OU A L'EXPORTATION">AGRICULTURE DESTINEE A
                                L'INDUSTRIE OU A L'EXPORTATION</option>
                            <option value="ELEVAGE ET CHASSE">ELEVAGE ET CHASSE</option>
                            <option value="ACTIVITES ANNEXES A L'AGRICULTURE L'ELEVAGE ET LA CHASSE">ACTIVITES ANNEXES A
                                L'AGRICULTURE L'ELEVAGE ET LA CHASSE</option>
                            <option value="SYLVICULTURE ET EXPLOITATION FORESTIERE">SYLVICULTURE ET EXPLOITATION
                                FORESTIERE
                            </option>
                            <option value="PECHE">PECHE</option>
                            <option value="AQUACULTURE PISCICULTURE">AQUACULTURE PISCICULTURE</option>
                            <option value="EXTRACTION DE CHARBON ET DE LIGNITE">EXTRACTION DE CHARBON ET DE LIGNITE
                            </option>
                            <option value="EXTRACTION D'HYDROCARBURES">EXTRACTION D'HYDROCARBURES</option>
                            <option value="EXTRACTION DE MINERAIS MÉTALLIQUES">EXTRACTION DE MINERAIS MÉTALLIQUES
                            </option>
                            <option value="EXTRACTION DE PIERRES DE SABLES ET D'ARGILES">EXTRACTION DE PIERRES DE SABLES
                                ET
                                D'ARGILES</option>
                            <option value="ACTIVITES EXTRACTIVES N.C.A.">ACTIVITES EXTRACTIVES N.C.A.</option>
                            <option value="ACTIVITES DE SOUTIEN A L’EXTRACTION D’HYDROCARBURES">ACTIVITES DE SOUTIEN A
                                L’EXTRACTION D’HYDROCARBURES</option>
                            <option value="ACTIVITES DE SOUTIEN AUX AUTRES INDUSTRIES EXTRACTIVES">ACTIVITES DE SOUTIEN
                                AUX
                                AUTRES INDUSTRIES EXTRACTIVES</option>
                            <option value="ABATTAGE TRANSFORMATION ET CONSERVATION DE LA VIANDE">ABATTAGE TRANSFORMATION
                                ET
                                CONSERVATION DE LA VIANDE</option>
                            <option value="TRANSFORMATION ET CONSERVATION DE POISSONS CRUSTACES ET MOLLUSQUES">
                                TRANSFORMATION ET
                                CONSERVATION DE POISSONS CRUSTACES ET MOLLUSQUES</option>
                            <option value="TRANSFORMATION ET CONSERVATION DE FRUITS ET LEGUMES">TRANSFORMATION ET
                                CONSERVATION
                                DE FRUITS ET LEGUMES</option>
                            <option value="FABRICATION DE CORPS GRAS D'ORIGINE ANIMALE ET VEGETALE">FABRICATION DE CORPS
                                GRAS
                                D'ORIGINE ANIMALE ET VEGETALE</option>
                            <option value="TRAVAIL DES GRAINS ; FABRICATION DE PRODUITS AMYLACES">TRAVAIL DES GRAINS ;
                                FABRICATION DE PRODUITS AMYLACES</option>
                            <option value="FABRICATION DE PRODUITS ALIMENTAIRES A BASE DE CEREALES N.C.A.">FABRICATION
                                DE
                                PRODUITS ALIMENTAIRES A BASE DE CEREALES N.C.A.</option>
                            <option value="TRANSFORMATION DU CACAO ET DU CAFÉ">TRANSFORMATION DU CACAO ET DU CAFÉ
                            </option>
                            <option value="FABRICATION D'AUTRES PRODUITS ALIMENTAIRES">FABRICATION D'AUTRES PRODUITS
                                ALIMENTAIRES</option>
                            <option value="FABRICATION DE BOISSONS ALCOOLISÉES">FABRICATION DE BOISSONS ALCOOLISÉES
                            </option>
                            <option value="FABRICATION DE BOISSONS NON ALCOOLISES ET D'EAUX MINERALES">FABRICATION DE
                                BOISSONS
                                NON ALCOOLISES ET D'EAUX MINERALES</option>
                            <option value="FABRICATION DE PRODUITS A BASE DE TABAC">FABRICATION DE PRODUITS A BASE DE
                                TABAC
                            </option>
                            <option value="FILATURE TISSAGE ET ENNOBLISSEMENT DE TEXTILE">FILATURE TISSAGE ET
                                ENNOBLISSEMENT
                                DE
                                TEXTILE</option>
                            <option value="FABRICATION D'AUTRES ARTICLES TEXTILES">FABRICATION D'AUTRES ARTICLES
                                TEXTILES
                            </option>
                            <option value="FABRICATION DE VETEMENTS">FABRICATION DE VETEMENTS</option>
                            <option value="SERVICE DE COUTURE SUR MESURE">SERVICE DE COUTURE SUR MESURE</option>
                            <option value="TRAVAIL DU CUIR; FABRICATION D'ARTICLES DE VOYAGE">TRAVAIL DU CUIR;
                                FABRICATION
                                D'ARTICLES DE VOYAGE</option>
                            <option value="FABRICATION DE CHAUSSURES ET ARTICLES CHAUSSANTS">FABRICATION DE CHAUSSURES
                                ET
                                ARTICLES CHAUSSANTS</option>
                            <option value="TRAVAIL DU BOIS">TRAVAIL DU BOIS</option>
                            <option value="FABRICATION D'ARTICLES EN BOIS LIEGE VANNERIE ET SPARTERIE">FABRICATION
                                D'ARTICLES EN
                                BOIS LIEGE VANNERIE ET SPARTERIE</option>
                            <option value="FABRICATION DE PAPIER CARTONS ET D’ARTICLES EN PAPIER OU EN CARTON">
                                FABRICATION
                                DE
                                PAPIER CARTONS ET D’ARTICLES EN PAPIER OU EN CARTON</option>
                            <option value="IMPRIMERIE ET ACTIVITES CONNEXES">IMPRIMERIE ET ACTIVITES CONNEXES</option>
                            <option value="REPRODUCTION D'ENREGISTREMENTS">REPRODUCTION D'ENREGISTREMENTS</option>
                            <option value="RAFFINAGE DU PETROLE">RAFFINAGE DU PETROLE</option>
                            <option value="COKEFACTION">COKEFACTION</option>
                            <option value="FABRICATION DE PRODUITS CHIMIQUES DE BASE">FABRICATION DE PRODUITS CHIMIQUES
                                DE
                                BASE
                            </option>
                            <option value="FABRICATION DE PRODUITS CHIMIQUES FONCTIONNELS">FABRICATION DE PRODUITS
                                CHIMIQUES
                                FONCTIONNELS</option>
                            <option value="FABRICATION DE PRODUITS PHARMACEUTIQUES">FABRICATION DE PRODUITS
                                PHARMACEUTIQUES
                            </option>
                            <option value="TRAVAIL DU CAOUTCHOUC">TRAVAIL DU CAOUTCHOUC</option>
                            <option value="TRAVAIL DU PLASTIQUE">TRAVAIL DU PLASTIQUE</option>
                            <option value="FABRICATION DE VERRE ET D'ARTICLES EN VERRE">FABRICATION DE VERRE ET
                                D'ARTICLES
                                EN
                                VERRE</option>
                            <option value="FABRICATION DE PRODUITS CÉRAMIQUES">FABRICATION DE PRODUITS CÉRAMIQUES
                            </option>
                            <option value="FABRICATION DE CIMENTS ET AUTRES PRODUITS MINERAUX">FABRICATION DE CIMENTS ET
                                AUTRES
                                PRODUITS MINERAUX</option>
                            <option value="FABRICATION DE COMPOSANTS ELECTRONIQUE D'ORDINATEURS ET DE PERIPHERIQUES">
                                FABRICATION
                                DE COMPOSANTS ELECTRONIQUE D'ORDINATEURS ET DE PERIPHERIQUES</option>
                            <option
                                value="FABRICATION D'EQUIPEMENTS DE COMMUNICATION ET DE PRODUITS ELECTRONIQUES GRAND PUBLICS">
                                FABRICATION D'EQUIPEMENTS DE COMMUNICATION ET DE PRODUITS ELECTRONIQUES GRAND PUBLICS
                            </option>
                            <option
                                value="FABRICATION D'EQUIPEMENTS D'IMAGERIE MEDICALE DE PRECISION D'OPTIQUE ET D'HORLOGERIE">
                                FABRICATION D'EQUIPEMENTS D'IMAGERIE MEDICALE DE PRECISION D'OPTIQUE ET D'HORLOGERIE
                            </option>
                            <option value="FABRICATION DE MACHINES ET MATERIELS ELECTROTECHNIQUES">FABRICATION DE
                                MACHINES
                                ET
                                MATERIELS ELECTROTECHNIQUES</option>
                            <option value="FABRICATION D'AUTRES MATERIELS ELECTRIQUES">FABRICATION D'AUTRES MATERIELS
                                ELECTRIQUES</option>
                            <option value="CONSTRUCTION DE VÉHICULES AUTOMOBILES">CONSTRUCTION DE VÉHICULES AUTOMOBILES
                            </option>
                            <option value="CONSTRUCTION NAVALE AÉRONAUTIQUE ET FERROVIAIRE">CONSTRUCTION NAVALE
                                AÉRONAUTIQUE ET
                                FERROVIAIRE</option>
                            <option value="FABRICATION DE MEUBLES ET MATELAS">FABRICATION DE MEUBLES ET MATELAS</option>
                            <option value="AUTRES INDUSTRIES">AUTRES INDUSTRIES</option>
                            <option value="REPARATION DE MACHINES ET D'EQUIPEMENTS PROFESSIONNELS">REPARATION DE
                                MACHINES
                                ET
                                D'EQUIPEMENTS PROFESSIONNELS</option>
                            <option value="INSTALLATION DE MACHINES ET D'EQUIPEMENTS PROFESSIONNELS">INSTALLATION DE
                                MACHINES
                                ET D'EQUIPEMENTS PROFESSIONNELS</option>
                            <option value="PRODUCTION TRANSPORT ET DISTRIBUTION D'ÉLECTRICITÉ">PRODUCTION TRANSPORT ET
                                DISTRIBUTION D'ÉLECTRICITÉ</option>
                            <option value="PRODUCTION ET DISTRIBUTION DE COMBUSTIBLES GAZEUX ET DE GLACE">PRODUCTION ET
                                DISTRIBUTION DE COMBUSTIBLES GAZEUX ET DE GLACE</option>
                            <option value="CAPTAGE TRAITEMENT ET DISTRIBUTION D'EAU">CAPTAGE TRAITEMENT ET DISTRIBUTION
                                D'EAU
                            </option>
                            <option value="COLLECTE ET TRAITEMENT DES EAUX USEES">COLLECTE ET TRAITEMENT DES EAUX USEES
                            </option>
                            <option value="COLLECTE TRAITEMENT ET ELIMINATION DES DECHETS ; RECUPERATION">COLLECTE
                                TRAITEMENT
                                ET ELIMINATION DES DECHETS ; RECUPERATION</option>
                            <option value="DEPOLLUTION ET GESTION DES DECHETS">DEPOLLUTION ET GESTION DES DECHETS
                            </option>
                            <option value="PROMOTION IMMOBILIÈRE">PROMOTION IMMOBILIÈRE</option>
                            <option value="CONSTRUCTION DE BÂTIMENTS COMPLETS">CONSTRUCTION DE BÂTIMENTS COMPLETS
                            </option>
                            <option value="GENIE CIVIL">GENIE CIVIL</option>
                            <option value="ACTIVITÉS SPECIALISEES DE CONSTRUCTION">ACTIVITÉS SPECIALISEES DE
                                CONSTRUCTION
                            </option>
                            <option value="COMMERCE DE VÉHICULES AUTOMOBILES">COMMERCE DE VÉHICULES AUTOMOBILES
                            </option>
                            <option value="ENTRETIEN ET REPARATION DE VEHICULES AUTOMOBILES">ENTRETIEN ET REPARATION DE
                                VEHICULES AUTOMOBILES</option>
                            <option value="COMMERCE DE PIECES DETACHEES ET D'ACCESSOIRES AUTOMOBILES">COMMERCE DE
                                PIECES
                                DETACHEES ET D'ACCESSOIRES AUTOMOBILES</option>
                            <option value="COMMERCE ET RÉPARATION DE MOTOCYCLES">COMMERCE ET RÉPARATION DE MOTOCYCLES
                            </option>

                        </select>
                        @error('categorie')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="relative">
                        <label for="durer" class="block text-sm font-medium text-gray-700">Date limite</label>
                        <input wire:model="durer" type="date" id="durer"
                            min="{{ now()->addDay()->toDateString() }}"
                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        @error('durer')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>



                    <div>
                        <label for="type_financement" class="block text-sm font-medium text-gray-700">Type de
                            financement</label>
                        <select wire:model="type_financement" id="type_financement" x-model="typeFinancement"
                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200">
                            <option value="" disabled>Sélectionnez le type de financement</option>
                            <option value="direct">Direct</option>
                            <option value="groupé">Groupé</option>
                        </select>
                        @error('type_financement')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <!-- Conteneur pour afficher les options dynamiquement -->
                        <div class="flex space-x-4 mt-4" x-show="typeFinancement">
                            <!-- Champ de saisie pour le financement direct -->
                            <div x-show="typeFinancement === 'direct'" class="flex flex-col flex-1">
                                <label for="username_direct"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Entrez le username pour le financement direct <span class="">*</span>
                                </label>

                                <!-- Champ de saisie avec recherche d'utilisateur en direct -->
                                <input wire:model.live="search"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                    type="text" placeholder="Entrez le nom de l'utilisateur">

                                <!-- Affichage des résultats de la recherche -->
                                @if (!empty($search))

                                    @foreach ($users as $user)
                                        <div class="cursor-pointer py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg"
                                            wire:click="selectUser('{{ $user->id }}', '{{ $user->username }}')">
                                            <div class="flex">
                                                <img class="w-5 h-5 mr-2 rounded-full" src="{{ asset($user->photo) }}"
                                                    alt="">
                                                <div class="flex justify-between items-center w-full">
                                                    <span>{{ $user->username }} ({{ $user->name }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>


                            <!-- Champ de saisie pour le financement groupé -->
                            <div x-show="typeFinancement === 'groupé'" class="flex flex-col flex-1 ">
                                <label for="bailleur_groupé"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ciblez un type
                                    bailleur pour le financement groupé</label>
                                <select wire:model="bailleur_groupé" id="bailleur_groupé"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="" disabled>Sélectionnez un bailleur</option>
                                    <option value="bank">Bank/IFD</option>
                                    <option value="pgm">Pgm Public/Para-Public</option>
                                    <option value="fonds">Fonds d’investissement</option>
                                    <option value="particulier">Particulier</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div x-data="{ mode_recouvre: '' }">
                        <label for="mode_recouvre" class="block text-sm font-medium text-gray-700">choissisez le mode
                            de recouvrement
                        </label>
                        <select wire:model="mode_recouvre" id="mode_recouvre" x-model="mode_recouvre"
                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200">
                            <option value="" disabled>Sélectionnez un mode de recouvrement</option>
                            <option value="ftO">Financement par total Obligations</option>
                            <option value="faO">Financement par Actions & Obligations </option>
                        </select>

                        <!-- Conteneur pour afficher les options dynamiquement -->
                        <div class="flex space-x-4 mt-4" x-show="mode_recouvre">

                            <!-- Champ de saisie pour le financement groupé -->
                            <div x-show="mode_recouvre === 'faO'" class="flex flex-col flex-1 ">
                                <div class="space-y-4">
                                    <!-- Portion du financement par Actions -->
                                    <div class="relative">
                                        <label for="portionActions"
                                            class="block text-sm font-medium text-gray-700">Portion du Financement par
                                            Actions (FCFA)</label>
                                        <input type="number" wire:model="portionActions" id="portionActions"
                                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                                            placeholder="Entrez la portion en FCFA">
                                        @error('portionActions')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Portion du financement par Obligations -->
                                    <div class="relative">
                                        <label for="portionObligations"
                                            class="block text-sm font-medium text-gray-700">Portion du Financement par
                                            Obligations (FCFA)</label>
                                        <input type="number" wire:model="portionObligations" id="portionObligations"
                                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                                            placeholder="Entrez la portion en FCFA">
                                        @error('portionObligations')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--  --}}
                    <div class="col-span-2">

                        <p class="text-xl font-bold text-gray-800 underline">DESCRIPTION DU PROJET</p>
                    </div>

                    <!-- Description -->
                    <div class="relative">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="description" id="description"
                            class="mt-1 block w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition ease-in-out duration-200"
                            placeholder="Entrez la description" rows="5" style="min-height: 600px;"></textarea>
                        @error('description')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Photo 1 -->

                        <div class="relative">
                            <label for="photo1" class="block text-sm font-medium text-gray-700">Image 1</label>
                            @if (!$photo1)
                                <!-- Zone de téléchargement stylisée -->
                                <label for="photo1"
                                    class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                        <path fill-rule="evenodd"
                                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                                </label>
                            @else
                                <!-- Affichage de la photo et bouton de suppression -->
                                <div class="relative">
                                    <img src="{{ $photo1->temporaryUrl() }}" alt="Preview Photo 1"
                                        class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                                    <button wire:click="$set('photo1', null)" type="button"
                                        class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            <input wire:model="photo1" type="file" id="photo1" class="hidden"
                                accept="image/*">
                            @error('photo1')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Photo 2 -->
                        <div class="relative">
                            <label for="photo2" class="block text-sm font-medium text-gray-700">Image 2</label>
                            @if (!$photo2)
                                <label for="photo2"
                                    class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                        <path fill-rule="evenodd"
                                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                                </label>
                            @else
                                <div class="relative">
                                    <img src="{{ $photo2->temporaryUrl() }}" alt="Preview Photo 2"
                                        class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                                    <button wire:click="$set('photo2', null)" type="button"
                                        class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            <input wire:model="photo2" type="file" id="photo2" class="hidden"
                                accept="image/*">
                            @error('photo2')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Photo 3 -->
                        <div class="relative">
                            <label for="photo3" class="block text-sm font-medium text-gray-700">Image 3</label>

                            @if (!$photo3)
                                <!-- Zone de téléchargement stylisée (visible si aucune photo n'est téléchargée) -->
                                <label for="photo3"
                                    class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                        viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                        <path fill-rule="evenodd"
                                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd" />
                                    </svg>

                                    <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                                </label>
                            @else
                                <!-- Affichage de la photo et bouton de suppression (visible si une photo est téléchargée) -->
                                <div class="relative">
                                    <img src="{{ $photo3->temporaryUrl() }}" alt="Preview Photo 3"
                                        class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                                    <!-- Bouton de suppression -->
                                    <button wire:click="$set('photo3', null)" type="button"
                                        class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif

                            <!-- Input de type fichier caché -->
                            <input wire:model="photo3" type="file" id="photo3" class="hidden"
                                accept="image/*">

                            @error('photo3')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>



                        <!-- Photo 4 -->
                        <div class="relative">
                            <label for="photo4" class="block text-sm font-medium text-gray-700">Image 4</label>
                            @if (!$photo4)
                                <label for="photo4"
                                    class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                        <path fill-rule="evenodd"
                                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                                </label>
                            @else
                                <div class="relative">
                                    <img src="{{ $photo4->temporaryUrl() }}" alt="Preview Photo 4"
                                        class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                                    <button wire:click="$set('photo4', null)" type="button"
                                        class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            <input wire:model="photo4" type="file" id="photo4" class="hidden"
                                accept="image/*">
                            @error('photo4')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Photo 5 -->
                        <div class="relative">
                            <label for="photo5" class="block text-sm font-medium text-gray-700">Image 5</label>
                            @if (!$photo5)
                                <label for="photo5"
                                    class="mt-1 flex flex-col items-center justify-center cursor-pointer border-2 border-dashed border-gray-300 rounded-md h-40 w-full hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                        viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 9a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 9Z" />
                                        <path fill-rule="evenodd"
                                            d="M9.344 3.071a49.52 49.52 0 0 1 5.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 0 0 1.11-.71l.822-1.315a2.942 2.942 0 0 1 2.332-1.39ZM6.75 12.75a5.25 5.25 0 1 1 10.5 0 5.25 5.25 0 0 1-10.5 0Zm12-1.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm text-gray-600 mt-2">Cliquez ou déposez une photo</span>
                                </label>
                            @else
                                <div class="relative">
                                    <img src="{{ $photo5->temporaryUrl() }}" alt="Preview Photo 5"
                                        class="w-full h-auto rounded-md shadow-lg border border-gray-300">
                                    <button wire:click="$set('photo5', null)" type="button"
                                        class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                            <input wire:model="photo5" type="file" id="photo5" class="hidden"
                                accept="image/*">
                            @error('photo5')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>



                    <!-- Submit Button with Loading -->
                    <div class="flex justify-center mt-6">
                        <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-md inline-flex items-center space-x-2 transition ease-in-out duration-200 mr-3"
                            @if ($isSubmitting) disabled @endif>
                            <span>{{ $isSubmitting ? 'En cours...' : 'Soumettre' }}</span>
                            <span wire:loading wire:target="submit" class="animate-spin">
                                <div class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-gray-400 rounded-full"
                                    role="status" aria-label="loading"></div>
                            </span>
                        </button>

                        <button type="button"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none"
                            wire:click="resetForm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>

    {{-- @endif --}}




</div>
