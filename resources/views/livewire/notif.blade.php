<div wire:poll.150ms>
    <div class="w-full mb-5 relative flex justify-center items-center">

        <h1 class="text-xl font-medium text-slate-800 relative">
            Notifications
            @if ($unreadCount)
                <span
                    class="absolute top-2 right-[-6px] w-4 h-4 text-[11px] font-semibold text-center flex items-center justify-center bg-red-700 text-white rounded-full transform translate-x-1/2 -translate-y-1/2 p-2">{{ $unreadCount }}</span>
            @endif
        </h1>
    </div>
    @foreach ($notifications as $notification)
        <div
            class="w-full px-3 py-2 @if ($notification->read_at == null) bg-white @else bg-gray-50 @endif border-y border-gray-200 hover:bg-gray-50">
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
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm text-slate-500 max-w-1/2 font-normal">{{ $notification->data['accept'] }}
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
                        <div class="flex flex-col justify-between w-full">
                            <div class="flex justify-between items-center w-full">
                                <p class="text-md font-semibold">{{ $notification->data['message'] }}</p>
                                <p class="text-[12px] text-gray-400 text-right">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm text-slate-500 max-w-1/2 font-normal">Raison:
                                {{ $notification->data['reason'] }}</p>
                        </div>
                    </div>
                </a>
            @elseif ($notification->type === 'App\Notifications\AchatGroupBiicf')
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
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                            </div>
                            <div class="flex justify-between items-center w-full h-full">
                                <p class="text-sm text-slate-500 max-w-1/2 font-normal">Vous avez reçu une commande de
                                    cet article en achat groupé</p>
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
                                $produtOffre = App\Models\ProduitService::find($notification->data['produit_id']);
                            @endphp
                        @endif
                        <div class="w-16 h-16 overflow-hidden mr-3">
                            <img src="{{ asset($produtOffre->photoProd1) }}" alt="Product Image"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col justify-between w-full">
                            <div class="flex justify-between items-center w-full">
                                <h3 class="text-md font-semibold">{{ $produtOffre->name }}</h3>
                                <p class="text-[12px] text-gray-400 text-right">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                            </div>
                            <div class="flex justify-between items-center w-full h-full">
                                <p class="text-sm text-slate-500 max-w-1/2 font-normal">Vous avez reçu une offre de ce
                                    produit</p>
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
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                            </div>
                            <div class="flex justify-between items-center w-full h-full">
                                <p class="text-sm text-slate-500 max-w-1/2 font-normal">Vous avez reçu une commande de
                                    cet article en achat direct</p>
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
                            <svg class="w-full text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.362 5.214A6.264 6.264 0 0 0 12 4.5c-.883 0-1.725.178-2.486.5" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.702 6.697a9 9 0 1 1 4.84 10.483" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.33 8.042a11.877 11.877 0 0 1 3.413-3.632 11.815 11.815 0 0 1 8.788-1.644A11.948 11.948 0 0 1 21 12a11.98 11.98 0 0 1-.383 3.022" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.502 17.228 3.702 6.697a9 9 0 1 1 4.84 10.483" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.33 8.042a11.877 11.877 0 0 1 3.413-3.632 11.815 11.815 0 0 1 8.788-1.644A11.948 11.948 0 0 1 21 12a11.98 11.98 0 0 1-.383 3.022" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.502 17.228 3.702 6.697a9 9 0 1 1 4.84 10.483" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.33 8.042a11.877 11.877 0 0 1 3.413-3.632 11.815 11.815 0 0 1 8.788-1.644A11.948 11.948 0 0 1 21 12a11.98 11.98 0 0 1-.383 3.022" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.502 17.228 3.702 6.697a9 9 0 1 1 4.84 10.483" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.33 8.042a11.877 11.877 0 0 1 3.413-3.632 11.815 11.815 0 0 1 8.788-1.644A11.948 11.948 0 0 1 21 12a11.98 11.98 0 0 1-.383 3.022" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.502 17.228 3.702 6.697a9 9 0 1 1 4.84 10.483" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.33 8.042a11.877 11.877 0 0 1 3.413-3.632 11.815 11.815 0 0 1 8.788-1.644A11.948 11.948 0 0 1 21 12a11.98 11.98 0 0 1-.383 3.022" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5.502 17.228 3.702 6.697a9 9 0 1 1 4.84 10.483" />
                            </svg>
                        </div>
                        <div class="flex flex-col justify-between w-full">
                            <div class="flex justify-between items-center w-full">
                                <p class="text-md font-semibold">Vous avez reçu un appel d'offre</p>
                                <p class="text-[12px] text-gray-400 text-right">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    @endforeach
</div>
