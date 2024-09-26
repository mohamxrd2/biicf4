@extends('biicf.layout.navside')

@section('title', 'Acceuil')

@section('content')



    <div class="grid grid-cols-3 gap-4">

        <div class="lg:col-span-2 col-span-3">

            @livewire('search-bar')

        </div>

        <div class="lg:col-span-1 lg:block hidden">
            <div class="flex flex-col ">
                <div class="flex bg-white border border-gray-200 p-4 rounded-xl mb-3">
                    <img class="h-12 w-12 border-2 border-white rounded-full dark:border-gray-800 object-cover"
                        src="{{ asset($user->photo) }}" alt="">

                    <div class="flex flex-col ml-3">
                        <p class="font-semibold"> {{ $user->name }}</p>
                        <p class="text-[12px] text-gray-500 "> {{ $user->username }}</p>
                    </div>
                </div>
                <div class="flex flex-col bg-white border border-gray-200 p-4 rounded-xl">
                    <p class="font-semibold">Thèmes les plus rechercher</p>

                    <div class="space-y-3.5 capitalize text-xs font-normal mt-5 mb-2 text-gray-600 dark:text-white/80">
                        <a href="#">
                            <div class="flex items-center gap-3 p">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> artificial intelligence
                                    </h4>
                                    <div class="mt-0.5"> 1,245,62 post </div>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="block">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> Web developers</h4>
                                    <div class="mt-0.5"> 1,624 post </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="block">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> Ui Designers</h4>
                                    <div class="mt-0.5"> 820 post </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="block">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> affiliate marketing </h4>
                                    <div class="mt-0.5"> 480 post </div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="block">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-black dark:text-white text-sm"> affiliate marketing </h4>
                                    <div class="mt-0.5"> 480 post </div>
                                </div>
                            </div>
                        </a>


                    </div>

                </div>
                <footer class="text-center text-sm text-gray-600 pt-8 pb-11 ">
                    &copy; 2024 BIICF. Tous droits réservés.
                </footer>
            </div>

        </div>
    </div>


@endsection
