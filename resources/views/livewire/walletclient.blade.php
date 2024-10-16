<div>
    <div class="p-4">
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

        {{-- <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 rounded-lg w-96 shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold tracking-wider">{{ strtoupper($user->name) }}</h2>
                        <p class="text-sm">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <!-- Add any logo or card image -->
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/800px-Mastercard-logo.svg.png"
                            alt="Card Logo" class="h-10">
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="text-lg font-mono tracking-widest">
                        {{ $this->formatAccountNumber($userWallet->Numero_compte) }}
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold">{{ number_format($userWallet->balance, 2, ',', ' ') }}<span
                                class="text-white text-xl ml-1">FCFA</span>
                        </h1>
                    </div>
                    <div class="flex space-x-4 mt-6">
                        <button wire:click="transfert" data-tooltip-target="tooltip-A"
                            class="bg-black text-white font-semibold py-2 px-4 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h1v4h1m6 0v6h1.293l3.293-3.293A1 1 0 0015 16V8a1 1 0 00-1.707-.707L10 10H6v4m8-4v6m6 2a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h8m4 0h.5a1.5 1.5 0 011.5 1.5v.5">
                                </path>
                            </svg>
                            <div id="tooltip-A" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Transfert d'argent <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </button>
                        <button wire:click="retrait" data-tooltip-target="tooltip-B"
                            class="bg-yellow-400 text-black font-semibold py-2 px-4 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6M9 8h6M9 16h6m-7 4h8a1 1 0 001-1V4a1 1 0 00-1-1h-8a1 1 0 00-1 1v15a1 1 0 001 1zm-3 0h-.01">
                                </path>
                            </svg>
                            <div id="tooltip-B" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Retrait d'argent <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </button>
                    </div>
                </div>
            </div> --}}

        <div class="grid gap-y-4 mb-6 w-full grid-cols-1 md:grid-cols-2">


            <div class="flex flex-col bg-gray-100 col-span-1">
                <!-- Card Container -->
                <div
                    class="bg-gradient-to-br from-black to-blue-500 rounded-lg shadow-lg p-6 w-full md:w-96 h-56 text-white relative">
                    <!-- Card Chip Icon -->
                    <div class=" top-4 flex justify-between ">

                        <img src="https://as1.ftcdn.net/v2/jpg/00/76/54/60/1000_F_76546001_fEMIgXIZEYF5HiNXwXzP0gI83FFCQSqv.jpg"
                            alt="Cart sim" class="h-10 rounded-md">



                    </div>

                    <!-- Card Number -->
                    <div class="mt-8 text-2xl font-mono font-semibold tracking-widest"
                        style="font-family: 'Montserrat', sans-serif;">
                        {{ $this->formatAccountNumber($userWallet->Numero_compte) }}
                    </div>

                    <!-- Card Holder Info -->
                    <div class="flex justify-between items-center mt-6">
                        <div>
                            <p class="text-sm">{{ strtoupper($user->name) }}</p>
                            <p class="text-sm">12/24</p>
                        </div>

                        <!-- Card Logo Icon -->
                        <div class="">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/800px-Mastercard-logo.svg.png"
                                alt="Card Logo" class="h-10">
                        </div>
                    </div>
                </div>


                <h2 class="text-xl text-slate-800 font-semibold mt-4 font-mono">Actions rapide</h2>

                <div class="w-full md:w-96 border border-gray-300 rounded-lg p-3 bg-white mt-4 flex justify-around">
                    <button wire:click="transfert" >
                        <div class="flex flex-col justify-center items-center">
                            <div class=" bg-blue-500 p-2 w-10 h-10 rounded-full flex justify-center items-center text-white"
                                data-tooltip-target="tooltip-A">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>

                            </div>
                            <div id="tooltip-A" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Envoie d'argent <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                            <p class="text-md font-normal text-gray-600">Envoyer</p>

                        </div>
                    </button>
                    <button wire:click="retrait" >

                    <div class="flex flex-col justify-center items-center">
                        <div class="bg-black p-2 w-10 h-10 rounded-full flex justify-center items-center text-white"
                            data-tooltip-target="tooltip-B">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                            </svg>


                        </div>
                        <div id="tooltip-B" role="tooltip"
                            class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Retrait d'argent <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>

                        <p class="text-md font-normal text-gray-600">Retirer</p>

                    </div>
                </button>
                    

                    <div class="flex flex-col justify-center items-center">
                        <div class="bg-black p-2 w-10 h-10 rounded-full flex justify-center items-center text-white"
                            data-tooltip-target="tooltip-C">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>

                        </div>
                        <div id="tooltip-C" role="tooltip"
                            class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Transfert entre compte <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>

                        <p class="text-md font-normal text-gray-600">Transfert</p>

                    </div>




                </div>


            </div>



            <div class="flex-1 col-span-1 ">


                <div class="grid gap-4 grid-cols-1 md:grid-cols-2 mt-4">
                    <div class="p-4 bg-white border border-gray-300 rounded-xl col-span-1 md:col-span-2">
                        <!-- Contenu du premier élément ici -->
                        <div class="flex flex-col ">
                            <div class="w-full flex justify-between">
                                <div class="flex items-center">
                                    <div class="bg-gray-600 p-2 w-8 h-8 rounded-full flex justify-center items-center text-white"
                                        data-tooltip-target="tooltip-D">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                        </svg>



                                    </div>
                                    <h2 class="ml-3 font-bold text-md text-gray-800">Compte courant</h2>

                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-coc"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="size-6 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>

                                <div id="tooltip-coc" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    Compte d'operation courante <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>




                            </div>


                            <p class="text-md font-meduim text-gray-800 mt-4">
                                {{ number_format($userWallet->balance, 2, ',', ' ') }} FCFA</p>


                        </div>
                    </div>
                    <div class="p-4 bg-white border border-gray-300 rounded-xl col-span-1">
                        <!-- Contenu du premier élément ici -->
                        <div class="flex flex-col ">
                            <div class="w-full flex justify-between">
                                <div class="flex items-center">
                                    <div class="bg-gray-600 p-2 w-8 h-8 rounded-full flex justify-center items-center text-white"
                                        data-tooltip-target="tooltip-D">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>


                                    </div>
                                    <h2 class="ml-3 font-bold text-md text-gray-800">COI</h2>

                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    data-tooltip-target="tooltip-coi" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>
                                <div id="tooltip-coi" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    Compte des Opérations d’Investissement<div class="tooltip-arrow" data-popper-arrow>
                                    </div>
                                </div>

                            </div>

                            <p class="text-md font-meduim text-gray-800 mt-4">
                                {{ number_format($coi->Solde, 2, ',', ' ') }} FCFA</p>


                        </div>
                    </div>
                    <div class="p-4 bg-white border border-gray-300 rounded-xl col-span-1">
                        <!-- Contenu du deuxième élément ici -->
                        <div class="flex flex-col ">
                            <div class="w-full flex justify-between">
                                <div class="flex items-center">
                                    <div class="bg-gray-600 p-2 w-8 h-8 rounded-full flex justify-center items-center text-white"
                                        data-tooltip-target="tooltip-D">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m9 7.5 3 4.5m0 0 3-4.5M12 12v5.25M15 12H9m6 3H9m12-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>



                                    </div>
                                    <h2 class="ml-3 font-bold text-md text-gray-800">CEDD</h2>

                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    data-tooltip-target="tooltip-cedd" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>
                                <div id="tooltip-cedd" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    Compte d’Epargne à Durée Déterminée <div class="tooltip-arrow" data-popper-arrow>
                                    </div>
                                </div>


                            </div>

                            <p class="text-md font-meduim text-gray-800 mt-4">
                                {{ number_format($cedd->Solde, 2, ',', ' ') }} FCFA</p>


                        </div>
                    </div>
                    <div class="p-4 bg-white border border-gray-300 rounded-xl col-span-1">
                        <!-- Contenu du troisième élément ici -->
                        <div class="flex flex-col ">
                            <div class="w-full flex justify-between">
                                <div class="flex items-center">
                                    <div class="bg-gray-600 p-2 w-8 h-8 rounded-full flex justify-center items-center text-white"
                                        data-tooltip-target="tooltip-D">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>



                                    </div>
                                    <h2 class="ml-3 font-bold text-md text-gray-800">CFA</h2>

                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    data-tooltip-target="tooltip-cfa" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>
                                <div id="tooltip-cfa" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    Collecte des Financements Accordés<div class="tooltip-arrow" data-popper-arrow>
                                    </div>
                                </div>

                            </div>

                            <p class="text-md font-meduim text-gray-800 mt-4">
                                {{ number_format($cfa->Solde, 2, ',', ' ') }} FCFA</p>


                        </div>
                    </div>
                    <div class="p-4 bg-white border border-gray-300 rounded-xl col-span-1">
                        <!-- Contenu du quatrième élément ici -->
                        <div class="flex flex-col ">
                            <div class="w-full flex justify-between">
                                <div class="flex items-center">
                                    <div class="bg-gray-600 p-2 w-8 h-8 rounded-full flex justify-center items-center text-white"
                                        data-tooltip-target="tooltip-D">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>



                                    </div>
                                    <h2 class="ml-3 font-bold text-md text-gray-800">CEFP</h2>

                                </div>

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    data-tooltip-target="tooltip-cefp" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>
                                <div id="tooltip-cefp" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    Compte d’Epargne des Fonds Propres <div class="tooltip-arrow" data-popper-arrow>
                                    </div>
                                </div>

                            </div>

                            <p class="text-md font-meduim text-gray-800 mt-4">
                                {{ number_format($cefd->Solde, 2, ',', ' ') }} FCFA</p>


                        </div>
                    </div>
                </div>




            </div>
        </div>


        @if ($currentPage === 'transaction')
            <!-- Transactions Section -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Transactions</h2>
                    <div class="lg:flex items-center space-x-2  hidden">

                        <div class="relative">
                            <input type="text" placeholder="Rechercher..."
                                class="px-4 py-2 border border-gray-300 rounded-lg">
                            <svg class="w-5 h-5 text-gray-400 absolute top-3 right-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1116.65 4.35a7.5 7.5 0 010 10.3z"></path>
                            </svg>
                        </div>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg">Filter</button>
                    </div>
                </div>

                <!-- Transaction List -->
                <div>

                    @if ($transacCount == 0)
                        <div class="text-center w-full h-80 flex-col justify-center items-center">
                            <div class="flex justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold mb-2">Aucune transaction</h2>
                            <p class="text-gray-500">Vous verez les historiques des transactions ici !</p>
                        </div>
                    @else
                        @foreach ($transactions as $transaction)
                            @if (
                                ($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId) ||
                                    ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId) ||
                                    ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId) ||
                                    ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId) ||
                                    ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId))
                                <div
                                    class="flex justify-between items-center hover:bg-gray-100 p-4 rounded-xl cursor-pointer">
                                    <div class="flex items-center">
                                        @if ($transaction->type == 'Depot' || ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId))
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                                                </svg>
                                            </div>
                                        @elseif($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId)
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                                                </svg>
                                            </div>
                                        @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                                </svg>
                                            </div>
                                        @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                                </svg>
                                            </div>
                                        @elseif ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId)
                                            <div
                                                class="bg-gray-200 lg:flex hidden justify-center items-center lg:w-10 lg:h-10 w-8 h-8 rounded-full mr-4">
                                                <svg class="w-4 h-4 text-black font-bold"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 4.5L4.5 19.5M19.5 4.5H12M19.5 4.5v11.25" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div>
                                            @if ($transaction->type == 'Depot')
                                                <h3 class="text-sm font-medium">Rechargement</h3>
                                            @elseif ($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId)
                                                <h3 class="text-sm font-medium">
                                                    @if ($transaction->senderAdmin)
                                                        {{ $transaction->senderAdmin->name }}
                                                    @elseif($transaction->senderUser)
                                                        {{ $transaction->senderUser->name }}
                                                    @endif
                                                </h3>
                                            @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                                <h3 class="text-sm font-medium">
                                                    @if ($transaction->receiverUser)
                                                        {{ $transaction->receiverUser->name }}
                                                    @endif
                                                </h3>
                                            @elseif ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId)
                                                <h3 class="text-sm font-medium">Commission</h3>
                                            @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                                <h3 class="text-sm font-medium">Gele</h3>
                                            @elseif ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId)
                                                <h3 class="text-sm font-medium">Retrait</h3>
                                            @endif
                                            <p class="text-sm text-gray-500">
                                                @if ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                                    Envoie
                                                @elseif (
                                                    ($transaction->type == 'Reception' && $transaction->receiver_user_id == $userId) ||
                                                        $transaction->type == 'Depot' ||
                                                        ($transaction->type == 'Commission' && $transaction->receiver_user_id == $userId))
                                                    Reception
                                                @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                                    Gele pour achat
                                                @elseif ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId)
                                                    Retrait
                                                @endif
                                                • {{ $transaction->created_at->translatedFormat('j F Y \à H\hi') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        @if (
                                            ($transaction->type == 'Depot' || $transaction->type == 'Reception' || $transaction->type == 'Commission') &&
                                                $transaction->receiver_user_id == $userId)
                                            <div class="text-sm font-medium text-green-500">
                                                +{{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                            </div>
                                        @elseif ($transaction->type == 'Gele' && $transaction->sender_user_id == $userId)
                                            <div class="text-sm font-medium text-blue-600 text-end">
                                                - {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                            </div>
                                        @elseif ($transaction->type == 'Envoie' && $transaction->sender_user_id == $userId)
                                            <div class="text-sm font-medium text-red-500">
                                                - {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                            </div>
                                        @elseif ($transaction->type == 'withdrawal' && $transaction->sender_user_id == $userId)
                                            <div class="text-sm font-medium text-red-500">
                                                - {{ number_format($transaction->amount, 2, ',', ' ') }} FCFA
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    @endif
                    <!-- More transactions... -->
                </div>
            </div>
        @elseif ($currentPage === 'transfert')
            @livewire('transfert-client')
        @elseif ($currentPage === 'retrait')
            @livewire('withdrawal-component')
        @endif

    </div>

    <style>
        .slider-container {
            overflow: hidden;
            position: relative;
        }

        .slider {
            display: flex;
            transition: transform 0.5s ease;
        }

        .slide {
            margin: 0 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            animation: slideInRight 0.5s ease-out forwards;
        }

        .slide:hover {
            transform: translateY(-10px);
        }

        button.prev,
        button.next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 1;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        button.prev:hover,
        button.next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        button.prev {
            left: 10px;
        }

        button.next {
            right: 10px;
        }

        .slider-indicators {
            display: flex;
            justify-content: center;
        }

        .indicator {
            background-color: #ccc;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        .indicator.active {
            background-color: #333;
        }

        @media (max-width: 768px) {
            .slide {
                min-width: 80%;
            }
        }

        @media (max-width: 480px) {
            .slide {
                min-width: 100%;
            }

            button.prev,
            button.next {
                padding: 5px;
            }
        }

        @keyframes slideInRight {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>

    <script>
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        const prevButton = document.querySelector('.prev');
        const nextButton = document.querySelector('.next');
        const indicators = document.querySelectorAll('.indicator');

        let currentIndex = 0;

        function updateSliderPosition() {
            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentIndex);
            });
        }

        prevButton.addEventListener('click', () => {
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
            updateSliderPosition();
        });

        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
            updateSliderPosition();
        });

        updateSliderPosition();
    </script>
</div>
