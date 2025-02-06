<div>
    <div class="p-4">
        @if (session('success'))
            <div class="px-4 py-2 mb-4 text-green-800 bg-green-200 rounded-md">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="px-4 py-2 mb-4 text-red-800 bg-red-200 rounded-md">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid w-full grid-cols-3 gap-4">
            <div class="flex flex-col col-span-3 lg:col-span-2">
                <div class="grid grid-cols-3 gap-4">
                    <div
                        class="flex flex-col col-span-3 p-4 bg-blue-700 border border-gray-300 lg:col-span-1 rounded-xl">
                        <!-- Contenu du premier bloc -->
                        <div class="flex items-center justify-between">
                            <div class="p-2 text-white bg-blue-400 rounded-xl">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-coc"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-white size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>

                            <div id="tooltip-coc" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Compte d'operation courante <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                        </div>
                        <h2 class="mt-3 font-mono text-white text-md font-meduim">Compte courant</h2>

                        <p class="mt-4 text-xl font-bold text-white">
                            {{ number_format($userWallet->balance, 2, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="col-span-3 p-4 bg-white border border-gray-300 lg:col-span-1 rounded-xl">
                        <!-- Contenu du deuxième bloc -->

                        <div class="flex items-center justify-between">
                            <div class="p-2 text-green-500 bg-green-300 rounded-xl">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                </svg>


                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-rdm"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="text-gray-700 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>

                            <div id="tooltip-rdm" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Totale reçu les 30 derniers jours <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                        </div>
                        <h2 class="mt-3 font-mono text-md font-meduim">Revenu du mois</h2>

                        <p class="mt-4 text-xl font-bold ">
                            {{ number_format($receptionTransactionSum, 2, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="col-span-3 p-4 bg-white border border-gray-300 lg:col-span-1 rounded-xl">
                        <!-- Contenu du troisième bloc -->
                        <div class="flex items-center justify-between">
                            <div class="p-2 text-red-500 bg-red-300 rounded-xl">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181" />
                                </svg>



                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-std"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="text-gray-700 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>

                            <div id="tooltip-std" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Totale envoyé les 30 derniers jours <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                        </div>
                        <h2 class="mt-3 font-mono text-md font-meduim">Dépense du mois</h2>

                        <p class="mt-4 text-xl font-bold ">
                            {{ number_format($envoieTransactionSum, 2, ',', ' ') }} FCFA</p>
                    </div>
                </div>

                <h2 class="mt-4 font-mono text-xl font-semibold text-slate-800">Actions rapide</h2>

                <div class="flex justify-around w-full p-3 mt-4 bg-white border border-gray-300 rounded-lg ">
                    <button wire:click="deposit">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center w-10 h-10 p-2 text-white bg-green-500 rounded-full"
                                data-tooltip-target="tooltip-w">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 7.5h-.75A2.25 2.25 0 0 0 4.5 9.75v7.5a2.25 2.25 0 0 0 2.25 2.25h7.5a2.25 2.25 0 0 0 2.25-2.25v-7.5a2.25 2.25 0 0 0-2.25-2.25h-.75m-6 3.75 3 3m0 0 3-3m-3 3V1.5m6 9h.75a2.25 2.25 0 0 1 2.25 2.25v7.5a2.25 2.25 0 0 1-2.25 2.25h-7.5a2.25 2.25 0 0 1-2.25-2.25v-.75" />
                                </svg>

                            </div>

                            <div id="tooltip-w" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Recharger son compte <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                            <p class="font-normal text-gray-600 text-md">Déposer</p>
                        </div>
                    </button>

                    <button wire:click="envoie">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center w-10 h-10 p-2 text-white bg-blue-500 rounded-full "
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
                            <p class="font-normal text-gray-600 text-md">Envoyer</p>

                        </div>
                    </button>
                    <button wire:click="retrait">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center w-10 h-10 p-2 text-white bg-purple-500 rounded-full"
                                data-tooltip-target="tooltip-B">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" />
                                </svg>

                            </div>
                            <div id="tooltip-B" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Retrait d'argent <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                            <p class="font-normal text-gray-600 text-md">Retirer</p>

                        </div>
                    </button>
                    <button wire:click="transfert">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center w-10 h-10 p-2 text-white bg-black rounded-full"
                                data-tooltip-target="tooltip-C">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                                </svg>



                            </div>
                            <div id="tooltip-C" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Transfert entre compte <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                            <p class="font-normal text-gray-600 text-md">Transfert</p>

                        </div>

                    </button>

                    <a href="{{ route('biicf.remboursement') }}">
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex items-center justify-center w-10 h-10 p-2 text-white bg-yellow-300 rounded-full"
                                data-tooltip-target="tooltip-E">

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>



                            </div>
                            <div id="tooltip-E" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Suivis de rembourssement de prêt <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                            <p class="font-normal text-gray-600 text-md">Renboursement</p>

                        </div>

                    </a>


                </div>

                <div class="w-full mt-5">

                    @if ($currentPage === 'transaction')
                        <x-transaction-list :transactions="$transactions" :userId="$userId" :hasMoreTransactions="$hasMoreTransactions" />
                    @elseif($currentPage === 'envoie')
                        @livewire('transfert-argent')
                    @elseif($currentPage === 'retrait')
                        @livewire('withdrawal-component')
                    @elseif($currentPage === 'deposit')
                        @livewire('deposit-client')
                    @elseif($currentPage === 'transfert')
                        @livewire('transfert-account')
                    @endif

                </div>

            </div>
            <div class="flex flex-col col-span-3 lg:col-span-1">

                <div
                    class="relative w-full h-56 p-6 text-white rounded-lg shadow-lg bg-gradient-to-br from-black to-blue-500 ">
                    <!-- Card Chip Icon -->
                    <div class="flex justify-between top-4">

                        <img src="https://as1.ftcdn.net/v2/jpg/00/76/54/60/1000_F_76546001_fEMIgXIZEYF5HiNXwXzP0gI83FFCQSqv.jpg"
                            alt="Cart sim" class="h-10 rounded-md">

                    </div>

                    <!-- Card Number -->
                    <div class="mt-8 font-mono text-2xl font-semibold tracking-widest"
                        style="font-family: 'Montserrat', sans-serif;">
                        {{ $this->formatAccountNumber($userWallet->Numero_compte) }}
                    </div>

                    <!-- Card Holder Info -->
                    <div class="flex items-center justify-between mt-6">
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
                <h2 class="mt-4 font-mono text-xl font-semibold">
                    Tontine
                </h2>

                <a href="{{route('tontine')}}"
                    class="inline-flex items-center justify-center mt-4 px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-500 
                text-white text-center font-bold rounded-md shadow-lg hover:from-purple-700 hover:to-blue-700 
                transform hover:scale-105 transition-all duration-300 ease-in-out">
                    Initier une tontine
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>





                <h2 class="mt-4 font-mono text-xl font-semibold">Sous comptes</h2>



                <div class="p-4 mt-4 bg-white border border-gray-300 rounded-xl">
                    <!-- Contenu du premier élément ici -->
                    <div class="flex flex-col ">
                        <div class="flex justify-between w-full">
                            <div class="flex items-center">
                                <div
                                    class="flex items-center justify-center w-8 h-8 p-2 text-white bg-gray-600 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>


                                </div>
                                <h2 class="ml-3 font-bold text-gray-800 text-md">COI</h2>

                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-coi"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="text-gray-600 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            <div id="tooltip-coi" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Compte des Opérations d’Investissement<div class="tooltip-arrow" data-popper-arrow>
                                </div>
                            </div>

                        </div>

                        <p class="mt-4 text-gray-800 text-md font-meduim">
                            {{ number_format($coi->Solde, 2, ',', ' ') }} FCFA</p>


                    </div>
                </div>
                <div class="p-4 mt-4 bg-white border border-gray-300 rounded-xl">
                    <!-- Contenu du deuxième élément ici -->
                    <div class="flex flex-col ">
                        <div class="flex justify-between w-full">
                            <div class="flex items-center">
                                <div
                                    class="flex items-center justify-center w-8 h-8 p-2 text-white bg-purple-600 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m9 7.5 3 4.5m0 0 3-4.5M12 12v5.25M15 12H9m6 3H9m12-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>



                                </div>
                                <h2 class="ml-3 font-bold text-gray-800 text-md">CEDD</h2>

                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-cedd"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="text-gray-600 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            <div id="tooltip-cedd" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Compte d’Epargne à Durée Déterminée <div class="tooltip-arrow" data-popper-arrow>
                                </div>
                            </div>


                        </div>

                        <p class="mt-4 text-gray-800 text-md font-meduim">
                            {{ number_format($cedd->Solde, 2, ',', ' ') }} FCFA</p>


                    </div>
                </div>
                <div class="p-4 mt-4 bg-white border border-gray-300 rounded-xl">
                    <!-- Contenu du troisième élément ici -->
                    <div class="flex flex-col ">
                        <div class="flex justify-between w-full">
                            <div class="flex items-center">
                                <div
                                    class="flex items-center justify-center w-8 h-8 p-2 text-white bg-blue-600 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>



                                </div>
                                <h2 class="ml-3 font-bold text-gray-800 text-md">CFA</h2>

                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-cfa"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="text-gray-600 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            <div id="tooltip-cfa" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Collecte des Financements Accordés<div class="tooltip-arrow" data-popper-arrow>
                                </div>
                            </div>

                        </div>

                        <p class="mt-4 text-gray-800 text-md font-meduim">
                            {{ number_format($cfa->Solde, 2, ',', ' ') }} FCFA</p>


                    </div>
                </div>
                <div class="p-4 mt-4 bg-white border border-gray-300 rounded-xl">
                    <!-- Contenu du quatrième élément ici -->
                    <div class="flex flex-col ">
                        <div class="flex justify-between w-full">
                            <div class="flex items-center">
                                <div
                                    class="flex items-center justify-center w-8 h-8 p-2 text-white bg-green-600 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>



                                </div>
                                <h2 class="ml-3 font-bold text-gray-800 text-md">CEFP</h2>

                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-cefp"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="text-gray-600 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            <div id="tooltip-cefp" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Compte d’Epargne des Fonds Propres <div class="tooltip-arrow" data-popper-arrow>
                                </div>
                            </div>

                        </div>

                        <p class="mt-4 text-gray-800 text-md font-meduim">
                            {{ number_format($cefd->Solde, 2, ',', ' ') }} FCFA</p>
                    </div>
                </div>

                <div class="p-4 mt-4 bg-white border border-gray-300 rounded-xl">
                    <!-- Contenu du quatrième élément ici -->
                    <div class="flex flex-col ">
                        <div class="flex justify-between w-full">
                            <div class="flex items-center">
                                <div
                                    class="flex items-center justify-center w-8 h-8 p-2 text-white bg-yellow-600 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>



                                </div>
                                <h2 class="ml-3 font-bold text-gray-800 text-md">CRP</h2>

                            </div>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" data-tooltip-target="tooltip-crp"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="text-gray-600 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            <div id="tooltip-crp" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                Compte de remboursement de prêt <div class="tooltip-arrow" data-popper-arrow>
                                </div>
                            </div>

                        </div>

                        <p class="mt-4 text-gray-800 text-md font-meduim">
                            {{ number_format($crp->Solde, 2, ',', ' ') }} FCFA</p>
                    </div>
                </div>

            </div>

        </div>

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
        // const slider = document.querySelector('.slider');
        // const slides = document.querySelectorAll('.slide');
        // const prevButton = document.querySelector('.prev');
        // const nextButton = document.querySelector('.next');
        // const indicators = document.querySelectorAll('.indicator');

        // let currentIndex = 0;

        // function updateSliderPosition() {
        //     slider.style.transform = `translateX(-${currentIndex * 100}%)`;
        //     indicators.forEach((indicator, index) => {
        //         indicator.classList.toggle('active', index === currentIndex);
        //     });
        // }

        // prevButton.addEventListener('click', () => {
        //     currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
        //     updateSliderPosition();
        // });

        // nextButton.addEventListener('click', () => {
        //     currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
        //     updateSliderPosition();
        // });

        // updateSliderPosition();
    </script>

    <script>
        function toggleModal(visible) {
            const modal = document.getElementById('transactionModal');
            modal.classList.toggle('hidden', !visible);
        }
    </script>
</div>
