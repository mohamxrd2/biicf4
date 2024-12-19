<div id="search-input">
    <!-- En-tête -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <form wire:submit.prevent="search" class="">

            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">

                    <!-- Barre de recherche -->
                    <div class="relative hidden md:flex flex-1 mx-8">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" id="default-search" wire:model.live="keyword"
                            class="block w-full p-4 ps-10 text-sm sm:text-[12px] text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-purple-600 focus:border-purple-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Recherche de produit ou service..." required />
                    </div>

                    <!-- Menu utilisateur -->
                    <div class="flex items-center space-x-6">
                        <a href="#" class="text-gray-700 hover:text-blue-600">
                            <i class="fas fa-plus-circle mr-2"></i>Poster une annonce
                        </a>
                        <a href="#" class="text-gray-700 hover:text-blue-600">
                            <i class="fas fa-user-circle mr-2"></i>Mon compte
                        </a>
                    </div>
                </div>
            </div>
        </form>

    </header>

    <!-- Filtres -->
    <section class="border-b">
        <div class="container mx-auto px-6 py-4">
            <!-- Section de filtre -->
            <div id="filter-section"
                class="hidden opacity-0 translate-y-[-20px] transition-all duration-300 ease-in-out grid grid-cols-4 gap-3 mt-2 p-4 bg-purple-400 rounded-lg ">
                <div class="col-span-1">
                    <select wire:model.live="zone_economique" name="zoneEco" type="text"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Zone Economique">
                        <option selected>Zone economique</option>
                        <option value="proximite">Proximité</option>
                        <option value="locale">Locale</option>
                        <option value="departementale">Departementale</option>
                        <option value="nationale">Nationale</option>
                        <option value="sous_regionale">Sous Régionale</option>
                        <option value="continentale">Continentale</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <select wire:model.live="type" name="type"
                        class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
                        <option selected>Type</option>
                        <option>Produit</option>
                        <option>Service</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <input wire:model.live="qte" name="qte" type="number"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Quantité ">
                </div>
                <div class="col-span-1">
                    <input wire:model.live="prix" name="prix" type="number"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Prix unitaire">
                </div>
            </div>
        </div>
    </section>

    <!-- Liste des produits -->
    @if ($resultCount == 0)
        <div class="flex justify-center max-w-sm mx-auto mt-4 h-52">
            <div class="flex-col ">
                <svg class=" w-52 h-52" xmlns="http://www.w3.org/2000/svg" width="552.81023" height="515.45882"
                    viewBox="0 0 552.81023 515.45882" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path
                        d="M372.63287,365.4151H174.32491c-16.66894,0-30.22987-13.56093-30.22987-30.22987V136.87726c0-16.66894,13.56093-30.22987,30.22987-30.22987h198.30797c16.66894,0,30.22987,13.56093,30.22987,30.22987v198.30797c0,16.66894-13.56093,30.22987-30.22987,30.22987ZM174.32491,111.48416c-14.02422,0-25.39309,11.36888-25.39309,25.39309v198.30797c0,14.02422,11.36888,25.39309,25.39309,25.39309h138.90984c46.8289,0,84.79122-37.96232,84.79122-84.79122V136.87726c0-14.02422-11.36888-25.39309-25.39309-25.39309H174.32491Z"
                        fill="#f2f2f2" />
                    <path
                        d="M195.70439,132.1061c-9.38575,0-17.02112,7.63537-17.02112,17.02112v26.64175c0,9.38575,7.63537,17.02112,17.02112,17.02112h26.64175c9.38575,0,17.02112-7.63537,17.02112-17.02112v-26.64175c0-9.38575-7.63537-17.02112-17.02112-17.02112h-26.64175Z"
                        fill="#f2f2f2" />
                    <path
                        d="M195.70439,207.1061c-9.38575,0-17.02112,7.63537-17.02112,17.02112v26.64175c0,9.38575,7.63537,17.02112,17.02112,17.02112h26.64175c9.38575,0,17.02112-7.63537,17.02112-17.02112v-26.64175c0-9.38575-7.63537-17.02112-17.02112-17.02112h-26.64175Z"
                        fill="#f2f2f2" />
                    <path
                        d="M195.70439,282.1061c-9.38575,0-17.02112,7.63537-17.02112,17.02112v26.64175c0,9.38575,7.63537,17.02112,17.02112,17.02112h26.64175c9.38575,0,17.02112-7.63537,17.02112-17.02112v-26.64175c0-9.38575-7.63537-17.02112-17.02112-17.02112h-26.64175Z"
                        fill="#f2f2f2" />
                    <g>
                        <path
                            d="M213.3715,67.1061c-11.82633,0-21.44711,9.62079-21.44711,21.44711v33.56939c0,11.82633,9.62079,21.44711,21.44711,21.44711h33.56939c11.82633,0,21.44711-9.62079,21.44711-21.44711v-33.56939c0-11.82633-9.62079-21.44711-21.44711-21.44711h-33.56939Z"
                            fill="#153cd2" />
                        <path
                            d="M323.89034,160.9186c-11.82633,0-21.44711,9.62079-21.44711,21.44711v33.56939c0,11.82633,9.62079,21.44711,21.44711,21.44711h33.56939c11.82633,0,21.44711-9.62079,21.44711-21.44711v-33.56939c0-11.82633-9.62079-21.44711-21.44711-21.44711h-33.56939Z"
                            fill="#153cd2" />
                        <g>
                            <polygon
                                points="275.84229 498.80796 269.02267 498.75047 266.00379 471.83263 276.06892 471.91748 275.84229 498.80796"
                                fill="#a0616a" />
                            <path
                                d="M253.99807,509.65233c-.01481,1.6785,1.33828,3.06028,3.0229,3.07572l13.55914,.11314,2.37372-4.81149,.8718,4.83603,5.11615,.0464-1.2919-17.24842-1.77998-.11842-7.26029-.50048-2.34232-.15686-.0411,4.88024-10.89209,7.38777c-.82869,.56298-1.32776,1.49545-1.33604,2.49637Z"
                                fill="#2f2e41" />
                        </g>
                        <g>
                            <polygon
                                points="310.54007 498.80796 303.72044 498.75047 300.70157 471.83263 310.76669 471.91748 310.54007 498.80796"
                                fill="#a0616a" />
                            <path
                                d="M288.69584,509.65233c-.01481,1.6785,1.33828,3.06028,3.0229,3.07572l13.55914,.11314,2.37372-4.81149,.8718,4.83603,5.11615,.0464-1.2919-17.24842-1.77998-.11842-7.26029-.50048-2.34232-.15686-.0411,4.88024-10.89209,7.38777c-.82869,.56298-1.32776,1.49545-1.33604,2.49637Z"
                                fill="#2f2e41" />
                        </g>
                        <path
                            d="M249.08109,281.17831l27.64346,.87295,26.18854,5.81968s-2.61885,3.20082,6.11066,11.93033c0,0,11.63935,20.36886,3.49181,40.15576l-3.49181,79.14758s10.18085,55.79388,2.40219,73.5511l-10.28082,.64255-17.39634-73.54982-8.47914-79.20944-4.07377,77.98365s15.58482,53.52659,5.74359,74.77562l-11.13465-.64255-19.05157-74.13307s-8.14755-85.54922-6.98361-101.84432c1.16394-16.29509,9.31148-35.50002,9.31148-35.50002Z"
                            fill="#2f2e41" />
                        <g>
                            <path
                                d="M341.91407,156.11273c-3.77892,.97845-6.04918,4.83514-5.07069,8.61407,.44834,1.73156,1.50489,3.14028,2.87746,4.07089l-.00041,.00966-.53609,12.98479,9.93665,4.59846,.7932-19.98146-.06921,.00211c.86159-1.53494,1.15931-3.38933,.68324-5.22793-.97848-3.77893-4.83517-6.04911-8.61416-5.0706Z"
                                fill="#a0616a" />
                            <path
                                d="M286.37793,202.45483c-2.08746-2.06912-3.29844-4.87434-3.34374-7.92486-.06088-4.09228,1.98991-7.80651,5.48559-9.93539,4.02563-2.45174,9.05967-2.20364,12.82498,.63119l31.20286,23.4935,5.55989-33.84423,11.93675,3.84257-2.84806,44.07c-.19948,3.08263-1.94154,5.7983-4.6607,7.26426-2.71916,1.46596-5.94507,1.42927-8.63009-.09817l-45.0929-25.65511c-.90583-.51546-1.7216-1.13691-2.43458-1.84377Z"
                                fill="#3f3d56" />
                        </g>
                        <g>
                            <path
                                d="M238.79919,137.1819c1.9947,3.35541,.89159,7.69261-2.46385,9.68729-1.5375,.914-3.27962,1.17053-4.90955,.86503l-.00651,.00715-8.76131,9.59852-10.29404-3.73037,13.50476-14.7482,.04766,.05023c.46883-1.69663,1.563-3.22314,3.19555-4.19363,3.35544-1.99468,7.69258-.89152,9.68729,2.46398Z"
                                fill="#a0616a" />
                            <path
                                d="M242.58351,208.7873l-50.08512-13.52903c-2.98215-.8057-5.29885-3.05086-6.19773-6.00635-.89888-2.95549-.22401-6.11051,1.80467-8.44008l29.00517-33.30129,11.18221,5.67535-19.87987,27.94879,38.69929,5.28473c4.66979,.63785,8.41937,4.00591,9.55199,8.58126,.98359,3.97296-.17507,8.05448-3.09949,10.91773-2.17991,2.13441-5.01491,3.27394-7.95408,3.27362-1.00399,0-2.02084-.13303-3.02702-.40473Z"
                                fill="#3f3d56" />
                        </g>
                        <path
                            d="M313.98891,292.82223l-70.0381,3.21276c-1.92358-2.92109-1.40264-7.31347,1.29122-12.81367,10.17125-20.76721,2.44654-60.05462-2.74985-80.51179-1.45798-5.73976,2.4559-11.44589,8.34855-12.03515l6.60367-.66037,6.10424-18.63399h21.37875l8.86923,10.13266,15.05189,9.14389c-2.73972,28.22064-14.41356,68.0706,2.2172,88.05507,3.63737,4.37087,4.66873,9.09743,2.92321,14.1106Z"
                            fill="#3f3d56" />
                        <circle cx="271.51868" cy="150.9868" r="20.82357" fill="#a0616a" />
                        <path
                            d="M295.08231,137.65762c2.8972-10.56287-28.67928-19.62073-34.81549-11.11444-.85326-1.20934-4.00655-1.9439-5.45336-1.57293-1.44681,.37097-2.66357,1.29841-3.85072,2.19619-1.6323,1.2539-3.32392,2.55231-4.32553,4.35524-1.00909,1.79551-1.16487,4.24398,.19287,5.80205,1.07584,1.23904,2.9663,7.82332,4.58375,8.14978,1.12773,.23,2.07745,.41549,2.89358,.54904,.72714-1.06098,2.58132-2.39375,2.44778-3.67731,1.09807,.7271,.69691,2.00635,.47552,3.31376-.73718,4.35363-17.34626,38.05525-7.83345,28.12415,.9423,.55647,2.10715,1.07583,3.45008,1.55065,2.27775-3.43523,4.14747-7.48625,5.37914-11.7228l.00872,.07826c.42405,3.68168,3.11456,6.70263,6.67421,7.73388,14.27942,4.13681,25.82989-1.93337,29.80326-12.87997-1.45451-2.95297-2.08826-2.63223-1.95797-2.72004,1.81577-1.2238,4.31135-.42345,5.09179,1.62242,.2301,.60321,.43924,1.1182,.62043,1.50569,2.07002-7.40469,4.53201-6.33593-3.38458-21.29362Z"
                            fill="#2f2e41" />
                        <path
                            d="M285.23139,129.99395l-1.4586-7.45697c-.12261-.62684-.23896-1.30695,.04369-1.87973,.36317-.73595,1.29668-1.04096,2.10893-.92366,.81225,.11729,1.41978,.85923,2.22791,1.0021,2.80975,.49673,6.52379-2.27858,7.53053,4.7424,.41975,2.92732,5.09082,3.23652,6.65079,5.74883,1.55997,2.51232,1.75148,6.13862-.37749,8.19111-1.70031,1.63923-4.43095,1.82843-6.63933,.99107-2.20838-.83736-3.98071-2.52874-5.52887-4.31237s-2.9501-3.71374-4.73583-5.25948"
                            fill="#2f2e41" />
                        <path
                            d="M296.19459,141.33592c-5.69794-.79597-9.5818-2.86826-11.54365-6.15883-2.5677-4.30742-.84774-9.05031-.77338-9.25017l1.20447,.44772c-.016,.04361-1.57626,4.38523,.67957,8.15551,1.75478,2.9329,5.32489,4.79466,10.61057,5.53322l-.17758,1.27255Z"
                            fill="#153cd2" />
                    </g>
                    <path
                        d="M322.93382,60.76232c-16.75117,0-30.38116-13.62998-30.38116-30.38116S306.18264,0,322.93382,0s30.38116,13.62998,30.38116,30.38116-13.62998,30.38116-30.38116,30.38116Zm0-48.60985c-10.05189,0-18.22869,8.1768-18.22869,18.22869s8.1768,18.22869,18.22869,18.22869,18.22869-8.1768,18.22869-18.22869-8.1768-18.22869-18.22869-18.22869Z"
                        fill="#153cd2" />
                    <path
                        d="M369.67748,81.40904c-1.48939,0-2.98174-.54294-4.15367-1.64367l-28.51497-26.72296c-2.45067-2.29342-2.57528-6.13854-.27889-8.58624,2.29045-2.4566,6.13557-2.57824,8.58624-.27889l28.51497,26.72296c2.45067,2.29342,2.57528,6.13854,.27889,8.58624-1.19566,1.27874-2.81263,1.92256-4.43256,1.92256Z"
                        fill="#153cd2" />
                    <g>
                        <path
                            d="M498.55135,498.12587c2.06592,.12937,3.20768-2.43737,1.64468-3.93333l-.1555-.61819c.02047-.04951,.04105-.09897,.06178-.14839,2.08924-4.9818,9.16992-4.94742,11.24139,.04177,1.83859,4.42817,4.17942,8.86389,4.75579,13.54594,.25838,2.0668,.14213,4.17236-.31648,6.20047,4.30807-9.41059,6.57515-19.68661,6.57515-30.02077,0-2.59652-.14213-5.19301-.43275-7.78295-.239-2.11854-.56839-4.2241-.99471-6.31034-2.30575-11.2772-7.29852-22.01825-14.50012-30.98962-3.46197-1.89248-6.34906-4.85065-8.09295-8.39652-.62649-1.27891-1.11739-2.65462-1.34991-4.05618,.39398,.05168,1.48556-5.94866,1.18841-6.3168,.54906-.83317,1.53178-1.24733,2.13144-2.06034,2.98232-4.04341,7.0912-3.33741,9.23621,2.15727,4.58224,2.31266,4.62659,6.14806,1.81495,9.83683-1.78878,2.34682-2.03456,5.52233-3.60408,8.03478,.16151,.20671,.32944,.40695,.4909,.61366,2.96106,3.79788,5.52208,7.88002,7.68104,12.16859-.61017-4.76621,.29067-10.50822,1.82641-14.20959,1.74819-4.21732,5.02491-7.76915,7.91045-11.41501,3.46601-4.37924,10.57337-2.46806,11.18401,3.08332,.00591,.05375,.01166,.10745,.01731,.1612-.4286,.24178-.84849,.49867-1.25864,.76992-2.33949,1.54723-1.53096,5.17386,1.24107,5.60174l.06277,.00967c-.15503,1.54366-.41984,3.07444-.80734,4.57937,3.70179,14.31579-4.29011,19.5299-15.70147,19.76412-.25191,.12916-.49738,.25832-.74929,.38109,1.15617,3.25525,2.07982,6.59447,2.76441,9.97891,.61359,2.99043,1.03991,6.01317,1.27885,9.04888,.29715,3.83006,.27129,7.67959-.05168,11.50323l.01939-.13562c.82024-4.21115,3.10671-8.14462,6.4266-10.87028,4.94561-4.06264,11.93282-5.55869,17.26826-8.82425,2.56833-1.57196,5.85945,.45945,5.41121,3.43708l-.02182,.14261c-.79443,.32289-1.56947,.69755-2.31871,1.11733-.4286,.24184-.84848,.49867-1.25864,.76992-2.33949,1.54729-1.53096,5.17392,1.24107,5.6018l.06282,.00965c.0452,.00646,.08397,.01295,.12911,.01944-1.36282,3.23581-3.26168,6.23922-5.63854,8.82922-2.31463,12.49713-12.25603,13.68282-22.89022,10.04354h-.00648c-1.16259,5.06378-2.86128,10.01127-5.0444,14.72621h-18.02019c-.06463-.20022-.12274-.40692-.18089-.60717,1.6664,.10341,3.34571,.00649,4.98629-.29702-1.33701-1.64059-2.67396-3.29409-4.01097-4.93462-.03229-.0323-.05816-.0646-.08397-.09689-.67817-.8396-1.36282-1.67283-2.04099-2.51246l-.00036-.00102c-.04245-2.57755,.26652-5.14662,.87876-7.63984l.00057-.00035Z"
                            fill="#f2f2f2" />
                        <path
                            d="M0,514.26882c0,.66003,.53003,1.19,1.19006,1.19H551.48004c.65997,0,1.19-.52997,1.19-1.19,0-.65997-.53003-1.19-1.19-1.19H1.19006c-.66003,0-1.19006,.53003-1.19006,1.19Z"
                            fill="#ccc" />
                    </g>
                </svg>
                <p class="mt-4 text-sm text-slate-500">Aucun resultat pour votre recherche</p>
            </div>



        </div>
    @else
        @if ($keyword == '')


            <div class="flex flex-col p-6 mt-10 bg-white border border-gray-100 shadow-lg rounded-xl">
                <h1 class="text-2xl font-bold text-center">Faite un recherche</h1>

                <p class="mt-5 text-sm italic text-center text-gray-500">Tapez le nom du produit de la barre de
                    recherche ou rechercher par votre zone economique pour trouver des fournisseurs plus proche
                    et
                    lancer un appel d'offre et permettre au different fournisseur de discuter sur le
                    prix afin que vous ayez le meilleur prix.</p>

            </div>
        @else
            <section class="py-8">
                <div class="container mx-auto px-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                        <!-- Répéter la carte produit pour plus d'exemples -->
                        @foreach ($produits as $produit)
                            <div
                                class="bg-white rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <!-- Image produit -->
                                <div class="relative h-48">
                                    <img src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                        alt="{{ $produit->name }}" class="w-full h-full object-cover" />
                                    <div
                                        class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-sm ">
                                        4.5 ★ <!-- Exemple de notation -->
                                    </div>
                                </div>

                                <!-- Contenu produit -->
                                <div class="p-4">
                                    <!-- Titre et prix -->
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $produit->name }}</h3>
                                        <span
                                            class="text-lg font-bold text-yellow-500">{{ number_format($produit->prix, 0, ',', ' ') }}
                                            XOF</span>
                                    </div>

                                    <!-- Localisation -->
                                    <div class="flex items-center text-gray-600 text-sm mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 2C8.134 2 5 5.134 5 9c0 3.866 7 13 7 13s7-9.134 7-13c0-3.866-3.134-7-7-7z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 11a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                        <span>{{ $produit->user->commune }} • {{ $produit->user->ville }}</span>
                                    </div>

                                    <!-- Temps et bouton -->
                                    <div class="flex justify-between items-center mt-4">
                                        <span
                                            class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($produit->created_at)->diffForHumans() }}</span>
                                        <a href="{{ route('biicf.postdet', $produit->id) }}"
                                            class="bg-purple-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-500 transition-colors duration-200">
                                            Voir le produit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endif


    <!-- Pagination -->
    <section class="py-8">
        <div class="container mx-auto px-6">
            <div class="flex justify-center">
                <nav class="flex items-center space-x-2">
                    <button class="px-4 py-2 border rounded-lg hover:bg-gray-100">Précédent</button>
                    <button class="px-4 py-2 border rounded-lg bg-purple-600 text-white">1</button>
                    <button class="px-4 py-2 border rounded-lg hover:bg-gray-100">2</button>
                    <button class="px-4 py-2 border rounded-lg hover:bg-gray-100">3</button>
                    <button class="px-4 py-2 border rounded-lg hover:bg-gray-100">Suivant</button>
                </nav>
            </div>
        </div>
    </section>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("default-search");
            const filterSection = document.getElementById("filter-section");

            searchInput.addEventListener("focus", () => {
                filterSection.classList.remove("hidden", "opacity-0", "translate-y-[-20px]");
                filterSection.classList.add("opacity-100", "translate-y-0");
            });

            searchInput.addEventListener("blur", () => {
                setTimeout(() => {
                    if (!document.activeElement.closest("#filter-section")) {
                        filterSection.classList.add("opacity-0", "translate-y-[-20px]");
                        filterSection.classList.remove("opacity-100", "translate-y-0");
                        setTimeout(() => {
                            filterSection.classList.add("hidden");
                        }, 300); // Durée correspondant à `duration-300`
                    }
                }, 200);
            });
        });
    </script>

</div>
