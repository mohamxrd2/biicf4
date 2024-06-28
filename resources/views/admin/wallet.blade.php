@extends('admin.layout.navside')

@section('title', 'Porte-feuille')

@section('content')
    <div class="grid grid-cols-3 gap-4">
        <livewire:wallet lazy/>

        <div class="lg:col-span-1 col-span-3 ">

            <div>

                @auth('admin')
                    @if (Auth::guard('admin')->user()->admin_type == 'admin')
                        <div x-data="{ isModal2Open: false }"
                            class="w-full p-5 bg-white border flex items-center rounded-2xl hover:bg-gray-50 mb-4 cursor-pointer"
                            data-hs-overlay="#monney-send1">

                            <div class="flex flex-col">
                                <p class="font-bold  mb-3">Envoyé a un agent</p>
                                <div class="flex items-center">
                                    <s class="rounded-full w-8 h-8 bg-gray-200 flex items-center justify-center mr-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </s>

                                    <div class="flex -space-x-4 rtl:space-x-reverse">

                                        @if ($agentCount == 0)
                                            <p class="text-gray-600">Aucun agent</p>
                                        @else
                                            @foreach ($agents->take(5) as $agent)
                                                <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800"
                                                    src="{{ asset($agent->photo) }}" alt="">
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                @endauth

                <form action="{{ route('recharge.account') }}" method="POST">
                    @csrf
                    <div id="monney-send1"
                        class="hs-overlay h-auto hs-overlay-open:opacity-100 hs-overlay-open:duration-500 hidden size-full fixed top-0 start-0 z-[80] opacity-0 overflow-x-hidden transition-all overflow-y-auto pointer-events-none">
                        <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">


                            <div
                                class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                                <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                                    <h3 class="font-bold text-gray-800 dark:text-white">
                                        Recharger le compte
                                    </h3>
                                    <button type="button"
                                        class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700"
                                        data-hs-overlay="#monney-send1">
                                        <span class="sr-only">Close</span>
                                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6 6 18"></path>
                                            <path d="m6 6 12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="p-4 overflow-y-auto">

                                    <div class=" w-full mb-3">

                                        <input type="hidden" id="agent_id" name="agent_id" value="">

                                        <div class="relative" data-hs-combo-box="">
                                            <div class="relative">
                                                <input
                                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                                    type="text" placeholder="Entrez le nom de l'agent"
                                                    data-hs-combo-box-input="">
                                                <div class="absolute top-1/2 end-3 -translate-y-1/2"
                                                    data-hs-combo-box-toggle="">
                                                    <svg class="flex-shrink-0 size-3.5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m7 15 5 5 5-5"></path>
                                                        <path d="m7 9 5-5 5 5"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="relative z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300"
                                                style="display: none;" data-hs-combo-box-output="">

                                                <!-- Utiliser la boucle foreach pour générer les éléments de la liste déroulante -->
                                                @foreach ($agents as $agent)
                                                    <div class="cursor-pointer  py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100"
                                                        tabindex="{{ $loop->index }}"
                                                        data-hs-combo-box-output-item="{{ $agent->id }}">
                                                        <div class="flex">
                                                            <img class="w-5 h-5 mr-2 rounded-full"
                                                                src="{{ asset($agent->photo) }}" alt="">
                                                            <div class="flex justify-between items-center w-full">
                                                                <span data-hs-combo-box-search-text="{{ $agent->username }}"
                                                                    data-hs-combo-box-value="{{ $agent->id }}">{{ $agent->username }}({{ $agent->name }})</span>
                                                                <span class="hidden hs-combo-box-selected:block">
                                                                    <svg class="flex-shrink-0 size-3.5 text-blue-600"
                                                                        xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M20 6 9 17l-5-5"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>


                                    <div class="space-y-3 w-full mb-3">
                                        <input type="number" name="amount" id="floating_prix"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Entrez la somme" />
                                    </div>
                                </div>
                                <div
                                    class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                                    <button type="reset"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800"
                                        data-hs-overlay="#monney-send1">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                        Envoyé
                                    </button>
                                </div>
                            </div>


                        </div>
                    </div>
                </form>





                <div class="w-full p-5 bg-white border flex items-center rounded-2xl hover:bg-gray-50 mb-4 cursor-pointer"
                    data-hs-overlay="#monney-send2">

                    <div class="flex flex-col">
                        <p class="font-bold  mb-3">Envoyé a un client</p>
                        <div class="flex items-center">
                            <div class="rounded-full w-8 h-8 bg-gray-200 flex items-center justify-center mr-5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>

                            <div class="flex -space-x-4 rtl:space-x-reverse">

                                @if ($userCount == 0)

                                    <p class="text-gray-600">Aucun client</p>
                                @else
                                    @foreach ($users->take(5) as $user)
                                        <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800"
                                            src="{{ asset($user->photo) }}" alt="">
                                    @endforeach

                                @endif



                            </div>

                        </div>

                    </div>

                </div>

                <form action="{{ route('recharge.clientaccount') }}" method="POST">
                    @csrf

                    <div id="monney-send2"
                        class="hs-overlay h-auto hs-overlay-open:opacity-100 hs-overlay-open:duration-500 hidden size-full fixed top-0 start-0 z-[80] opacity-0 overflow-x-hidden transition-all overflow-y-auto pointer-events-none">
                        <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">


                            <div
                                class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
                                <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                                    <h3 class="font-bold text-gray-800 dark:text-white">
                                        Recharger le compte
                                    </h3>
                                    <button type="button"
                                        class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700"
                                        data-hs-overlay="#monney-send2">
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

                                    <div class=" w-full mb-3">

                                        <input type="hidden" id="user_id" name="user_id" value="">

                                        <div class="relative" data-hs-combo-box="">
                                            <div class="relative">
                                                <input
                                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                                    type="text" placeholder="Entrez le nom du client"
                                                    data-hs-combo-box-input="">
                                                <div class="absolute top-1/2 end-3 -translate-y-1/2"
                                                    data-hs-combo-box-toggle="">
                                                    <svg class="flex-shrink-0 size-3.5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m7 15 5 5 5-5"></path>
                                                        <path d="m7 9 5-5 5 5"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="relative z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300"
                                                style="display: none;" data-hs-combo-box-output="">

                                                <!-- Utiliser la boucle foreach pour générer les éléments de la liste déroulante -->
                                                @foreach ($users as $user)
                                                    <div class="cursor-pointer  py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100"
                                                        tabindex="{{ $loop->index }}"
                                                        data-hs-combo-box-output-item="{{ $user->id }}">
                                                        <div class="flex">
                                                            <img class="w-5 h-5 mr-2 rounded-full"
                                                                src="{{ asset($user->photo) }}" alt="">
                                                            <div class="flex justify-between items-center w-full">
                                                                <span
                                                                    data-hs-combo-box-search-text="{{ $user->username }} "
                                                                    data-hs-combo-box-value="{{ $user->id }}">{{ $user->username }}({{ $user->name }})</span>
                                                                <span class="hidden hs-combo-box-selected:block">
                                                                    <svg class="flex-shrink-0 size-3.5 text-blue-600"
                                                                        xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M20 6 9 17l-5-5"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>


                                    <div class="space-y-3 w-full mb-3">
                                        <input type="number" name="amount" id="floating_prix"
                                            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                            placeholder="Entrez la somme" />
                                    </div>
                                </div>
                                <div
                                    class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                                    <button type="reset"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800"
                                        data-hs-overlay="#monney-send2">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                        Envoyé
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comboBoxItems = document.querySelectorAll('[data-hs-combo-box-output-item]');
            const agentIdInput = document.getElementById('agent_id');

            comboBoxItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    const agentId = item.getAttribute('data-hs-combo-box-output-item');
                    agentIdInput.value = agentId;
                });
            });

            const comboBoxItems2 = document.querySelectorAll('[data-hs-combo-box-output-item]');
            const userIdInput = document.getElementById('user_id');

            comboBoxItems2.forEach(function(item) {
                item.addEventListener('click', function() {
                    const userId = item.getAttribute('data-hs-combo-box-output-item');
                    userIdInput.value = userId;
                });
            });
        });
    </script>

@endsection
