@extends('biicf.layout.navside')

@section('title', 'Notification')

@php
    use App\Models\ProduitService;
@endphp

@section('content')
    <!-- Afficher les messages de succès -->
    @if (session('success'))
        <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- Afficher les messages d'erreur -->
    @if (session('error'))
        <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mb-3">
            {{ session('error') }}
        </div>
    @endif


    <div class="max-w-5xl mx-auto ">
        <div class="w-full mb-5 relative flex justify-center items-center">

            <h1 class="text-xl font-medium text-slate-800 relative">
                Notifications
                @if ($unreadCount)
                    <span
                        class="absolute top-2 right-[-6px] w-4 h-4 text-[11px] font-semibold text-center flex items-center justify-center bg-red-700 text-white rounded-full transform translate-x-1/2 -translate-y-1/2 p-2">{{ $unreadCount }}</span>
                @endif

            </h1>
        </div>
        @if ($unreadCount == 0)



            <div class="flex flex-col justify-center items-center h-96 w-full">

                <svg class="w-12 h-12 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.97m1.965-2.277L21 21m-4.225-4.225a23.81 23.81 0 0 0 3.536-1.003A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6.53 6.53m10.245 10.245L6.53 6.53M3 3l3.53 3.53" />
                </svg>

                <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun notification</h1>
            </div>
        @else
            @foreach (auth()->user()->notifications as $notification)
                <div
                    class="w-full px-3 py-2 @if ($notification->read_at == null) bg-white @else bg-gray-50 @endif  border-y border-gray-200 hover:bg-gray-50">
                    @if (isset($notification->data['message']) && isset($notification->data['accept']))
                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                <div class="w-16 h-16 overflow-hidden mr-3">
                                    <svg class="w-full text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>

                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full">
                                        <p class="text-md font-semibold">{{ $notification->data['message'] }}</p>
                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-slate-500 l max-w-1/2  font-normal">
                                        {{ $notification->data['accept'] }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @elseif (isset($notification->data['message']) && isset($notification->data['reason']))
                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                <div class="w-16 h-16 overflow-hidden mr-3">
                                    <svg class="w-full text-red-700" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>

                                <div class="flex flex-col justify-between w-full ">
                                    <div class="flex justify-between items-center w-full">
                                        <p class="text-md font-semibold">{{ $notification->data['message'] }}</p>
                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-slate-500 l max-w-1/2  font-normal">Raison:
                                        {{ $notification->data['reason'] }}</p>
                                </div>
                            </div>
                        </a>
                    @elseif ($notification->type === 'App\Notifications\AchatGroupBiicf')
                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                <div class=" w-16 h-16  overflow-hidden mr-3">
                                    <img src="{{ asset($notification->data['photoProd']) }}" alt="Product Image"
                                        class="w-full h-full object-cover">

                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full">
                                        <h3 class="text-md font-semibold">{{ $notification->data['nameProd'] }}</h3>

                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>

                                    </div>
                                    <div class="flex justify-between items-center w-full h-full">

                                        <p class="text-sm text-slate-500 l max-w-1/2  font-normal">Vous avez reçu une
                                            commande
                                            de cet article en achat groupé
                                        </p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>

                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @elseif ($notification->type === 'App\Notifications\OffreNotif')
                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                @if (isset($notification->data['produit_id']))
                                    @php
                                        $produtOffre = App\Models\ProduitService::find(
                                            $notification->data['produit_id'],
                                        );
                                    @endphp
                                @endif

                                <div class=" w-16 h-16  overflow-hidden mr-3">
                                    <img src="{{ asset($produtOffre->photoProd1) }}" alt="Product Image"
                                        class="w-full h-full object-cover">

                                </div>


                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full ">
                                        <h3 class="text-md font-semibold">{{ $produtOffre->name }}</h3>

                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>

                                    </div>
                                    <div class="flex justify-between items-center w-full h-full">

                                        <p class="text-sm text-slate-500 l max-w-1/2  font-normal">Vous avez reçu une
                                            une offre de ce produit
                                        </p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>

                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @elseif($notification->type === 'App\Notifications\AchatBiicf')
                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                <div class="w-16 h-16 overflow-hidden mr-3">
                                    <img src="{{ asset($notification->data['photoProd']) }}" alt="Product Image"
                                        class="w-full h-full object-cover">
                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full">
                                        <h3 class="text-md font-semibold">{{ $notification->data['nameProd'] }}</h3>
                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex justify-between items-center w-full h-full">
                                        <p class="text-sm text-slate-500 l max-w-1/2 font-normal">Vous avez reçu une
                                            commande de cet article en achat direct</p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @elseif($notification->type === 'App\Notifications\AppelOffre')
                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                <div class="w-16 h-16 overflow-hidden mr-3">
                                    <svg class="w-full text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path
                                            d="M4.913 2.658c2.075-.27 4.19-.408 6.337-.408 2.147 0 4.262.139 6.337.408 1.922.25 3.291 1.861 3.405 3.727a4.403 4.403 0 0 0-1.032-.211 50.89 50.89 0 0 0-8.42 0c-2.358.196-4.04 2.19-4.04 4.434v4.286a4.47 4.47 0 0 0 2.433 3.984L7.28 21.53A.75.75 0 0 1 6 21v-4.03a48.527 48.527 0 0 1-1.087-.128C2.905 16.58 1.5 14.833 1.5 12.862V6.638c0-1.97 1.405-3.718 3.413-3.979Z" />
                                        <path
                                            d="M15.75 7.5c-1.376 0-2.739.057-4.086.169C10.124 7.797 9 9.103 9 10.609v4.285c0 1.507 1.128 2.814 2.67 2.94 1.243.102 2.5.157 3.768.165l2.782 2.781a.75.75 0 0 0 1.28-.53v-2.39l.33-.026c1.542-.125 2.67-1.433 2.67-2.94v-4.286c0-1.505-1.125-2.811-2.664-2.94A49.392 49.392 0 0 0 15.75 7.5Z" />
                                    </svg>
                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full">
                                        <h3 class="text-md font-semibold">{{ $notification->data['productName'] }}</h3>
                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex justify-between items-center w-full h-full">
                                        <p class="text-sm text-slate-500 l max-w-1/2 font-normal">Vous avez reçu un appel
                                            offre cliquez pour participer à la négociation</p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @elseif($notification->type === 'App\Notifications\OffreNotifGroup')
                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                <div class="w-16 h-16 overflow-hidden mr-3">
                                    <svg class="w-full text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path
                                            d="M4.913 2.658c2.075-.27 4.19-.408 6.337-.408 2.147 0 4.262.139 6.337.408 1.922.25 3.291 1.861 3.405 3.727a4.403 4.403 0 0 0-1.032-.211 50.89 50.89 0 0 0-8.42 0c-2.358.196-4.04 2.19-4.04 4.434v4.286a4.47 4.47 0 0 0 2.433 3.984L7.28 21.53A.75.75 0 0 1 6 21v-4.03a48.527 48.527 0 0 1-1.087-.128C2.905 16.58 1.5 14.833 1.5 12.862V6.638c0-1.97 1.405-3.718 3.413-3.979Z" />
                                        <path
                                            d="M15.75 7.5c-1.376 0-2.739.057-4.086.169C10.124 7.797 9 9.103 9 10.609v4.285c0 1.507 1.128 2.814 2.67 2.94 1.243.102 2.5.157 3.768.165l2.782 2.781a.75.75 0 0 0 1.28-.53v-2.39l.33-.026c1.542-.125 2.67-1.433 2.67-2.94v-4.286c0-1.505-1.125-2.811-2.664-2.94A49.392 49.392 0 0 0 15.75 7.5Z" />
                                    </svg>
                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full">
                                        <h3 class="text-md font-semibold">{{ $notification->data['produit_name'] }}</h3>
                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex justify-between items-center w-full h-full">
                                        <p class="text-sm text-slate-500 l max-w-1/2 font-normal">Vous etes ciblé pour un
                                            appel
                                            OFFRE NEGOCIER; cliquez pour participer à la négociation</p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @elseif($notification->type === 'App\Notifications\NegosTerminer')
                        {{-- a ameliore le front --}}

                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                <div class="w-16 h-16 overflow-hidden mr-3">



                                    <svg class="w-full text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                                    </svg>


                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                    <div class="flex justify-between items-center w-full h-full">
                                        <p class="text-sm text-slate-500 l max-w-1/2 font-normal">
                                            {{ $notification->data['offre']['message'] }}</p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>
                                            </div>
                                        @endif
                                    </div>


                                </div>
                            </div>
                        </a>

                    @elseif($notification->type === 'App\Notifications\AppelOffreTerminer')
                        <a href="{{ route('notification.show', $notification->id) }}" class="">
                            <div class="flex w-full">
                                <div class="w-16 h-16 overflow-hidden mr-3">



                                    <svg class="w-full text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                                    </svg>


                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full">
                                        <h3 class="text-md font-semibold">{{ $notification->data['name'] }}</h3>
                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex justify-between items-center w-full h-full">
                                        <p class="text-sm text-slate-500 l max-w-1/2 font-normal">
                                            {{ $notification->data['message'] }}</p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>


                    @elseif ($notification->type === 'App\Notifications\OffreNegosNotif')
                        <a href="{{ route('notification.show', $notification->id) }}">
                            <div class="flex w-full">

                                <div class="w-16 h-16 overflow-hidden mr-3">
                                    <svg class="w-full text-yellow-300" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                        <path fill-rule="evenodd"
                                            d="M19.5 21a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3h-5.379a.75.75 0 0 1-.53-.22L11.47 3.66A2.25 2.25 0 0 0 9.879 3H4.5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h15Zm-6.75-10.5a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25v2.25a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V10.5Z"
                                            clip-rule="evenodd" />
                                    </svg>

                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full">
                                        <h3 class="text-md font-semibold">{{ $notification->data['produit_name'] }}</h3>
                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex justify-between items-center w-full h-full">
                                        <p class="text-sm text-slate-500 l max-w-1/2 font-normal">Vous etes ciblé pour une
                                            offre groupé. Cliquez pour participer à la négociation</p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>
                                            </div>
                                        @endif
                                    </div>
                                </div>



                            </div>


                        </a>
                    @elseif ($notification->type === 'App\Notifications\OffreNegosDone')
                        <a href="{{ route('notification.show', $notification->id) }}">
                            @php
                                $produit = ProduitService::find($notification->data['produit_id']);
                            @endphp
                            <div class="flex w-full">
                                <div class=" w-16 h-16  overflow-hidden mr-3">
                                    <img src="{{ asset($produit->photoProd1) }}" alt="Product Image"
                                        class="w-full h-full object-cover">
                                </div>

                                <div class="flex flex-col justify-between w-full">
                                    <div class="flex justify-between items-center w-full">
                                        <h3 class="text-md font-semibold">{{ $produit->name }}</h3>
                                        <p class="text-[12px] text-gray-400 text-right">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex justify-between items-center w-full h-full">
                                        <p class="text-sm text-slate-500 l max-w-1/2 font-normal">Vous avez reçu une offre
                                            de ce produit !</p>
                                        @if ($notification->read_at == null)
                                            <div class="w-10 flex justify-center items-center">
                                                <span class="w-2 h-2 rounded-full bg-purple-700"></span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                        </a>
                    @endif

                </div>
            @endforeach

        @endif


    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

@endsection
