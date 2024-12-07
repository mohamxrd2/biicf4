@php
    use App\Models\ProduitService;
@endphp

<div wire:poll.150ms>
    <div class="min-h-screen ">
        <div class="max-w-5xl mx-auto pt-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->

                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>

                            <h1 class="ml-3 text-xl font-semibold text-gray-900">Centre de notifications</h1>
                            <span class="ml-3 px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                <!-- Nombre de notifications non lues -->
                                {{ $unreadCount }}
                                nouvelle{{ $unreadCount > 1 ? 's' : '' }}
                            </span>

                        </div>
                        <button class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                            Tout marquer comme lu
                        </button>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="divide-y divide-gray-200">
                    <!-- Exemple de Notification -->
                    @foreach (auth()->user()->notifications as $notification)
                        @switch($notification->type)
                            @case('App\Notifications\Confirmation')
                            @case('App\Notifications\AchatBiicf')

                            @case('App\Notifications\AOGrouper')
                            @case('App\Notifications\AppelOffreGrouperNotification')

                            @case('App\Notifications\AppelOffreTerminerGrouper')
                            @case('App\Notifications\livraisonAchatdirect')

                            @case('App\Notifications\CountdownNotificationAd')
                            @case('App\Notifications\OffreNegosNotif')

                            @case('App\Notifications\VerifUser')
                            @case('App\Notifications\mainleveAd')

                            @case('App\Notifications\mainleveclient')
                            @case('App\Notifications\Confirmation')

                            @case('App\Notifications\Retrait')
                            @case('App\Notifications\DepositSos')

                            @case('App\Notifications\DepositRecu')
                            @case('App\Notifications\DepositSend')

                            @case('App\Notifications\AppelOffre')
                            @case('App\Notifications\OffreNotifGroup')

                            @case('App\Notifications\RefusAchat')
                            @case('App\Notifications\NegosTerminer')

                            @case('App\Notifications\AppelOffreTerminer')
                            @case('App\Notifications\OffreNegosDone')
                                @include('biicf.components.ConfirmationNotif', [
                                    'title' => $notification->data['title'],
                                    'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                    'description' => $notification->data['description'],
                                    'orderId' => $notification->data['code_unique'],
                                    'svg' => $notification->data['svg'],
                                    'markAsRead' => true,
                                    'delete' => true,
                                ])
                            @break

                            {{-- Notification affichant la vue OffreNotif --}}
                            @case ('App\Notifications\OffreNotif')
                                @include('biicf.components.OffreNotif', [
                                    'title' => 'Réception d\'offre',
                                    'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                    'description' => 'Vous avez reçu  une offre de ce produit.',
                                    'svg' =>
                                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6  text-green-600"> <path stroke-linecap="round" stroke-linejoin="round"d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /> </svg>',
                                    'markAsRead' => true,
                                    'delete' => true,
                                ])

                                @default
                                    <!-- Ajoutez ici un traitement par défaut ou laissez vide -->
                            @endswitch
                        @endforeach
                        <!-- Aucune notification -->
                        <!-- Section commentée qui peut être activée si aucune notification -->
                       @if ( $unreadCount < 0)

                       <div class="p-8 flex flex-col items-center justify-center bg-gray-50 rounded-lg shadow-lg">
                        <!-- Icône centrale -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-16 h-16 text-gray-500 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.97m1.965-2.277L21 21m-4.225-4.225a23.81 23.81 0 0 0 3.536-1.003A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6.53 6.53m10.245 10.245L6.53 6.53M3 3l3.53 3.53" />
                        </svg>

                        <!-- Titre et texte -->
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune notification</h3>
                        <p class="text-base text-gray-600">Vous n'avez aucune notification pour le moment.</p>

                 
                    </div>
                           
                       @endif
                        


                    </div>
                </div>
            </div>
        </div>


    </div>
