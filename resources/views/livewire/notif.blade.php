@php
    use App\Models\ProduitService;
@endphp

<div wire:poll.150ms>
    <div class="min-h-screen ">
        <div class="max-w-3xl mx-auto pt-8 px-4 sm:px-6 lg:px-8">
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
                                {{ $unreadCount }} nouvelles
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
                        @if ($notification->type === 'App\Notifications\AchatBiicf')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => 'Réception de Commande du produit ' . $notification->data['nameProd'],
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' =>
                                    'Votre commande #' . $notification->data['code_unique'] . ' a été confirmée.',
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        stroke="currentColor" class="size-6  text-green-600">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </svg>',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\OffreNotif')
                            @include('biicf.components.OffreNotif', [
                                'title' => 'Réception d\'offre',
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => 'Vous avez reçu  une offre de ce produit.',
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        stroke="currentColor" class="size-6  text-green-600">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </svg>',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\OffreNegosNotif')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => 'Groupage de fournisseurs',
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => 'Cliquez pour participer.',
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        stroke="currentColor" class="size-6  text-green-600">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </svg>',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\OffreNotifGroup')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => $notification->data['title'],
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => $notification->data['description'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        stroke="currentColor" class="size-6  text-green-600">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </svg>',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\Confirmation')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => $notification->data['title'],
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => $notification->data['description'],
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"      stroke="currentColor" class="size-6 text-green-600">                                                                                                                                                                                                                                                                                                                 <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </svg>
                                                                                                                                                                                        ',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\RefusAchat')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => $notification->data['title'],
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => $notification->data['description'],
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg class="w-full text-red-700 " xmlns="http://www.w3.org/2000/svg" fill="none"                                                                                                                                                                                                                                                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </svg>',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\livraisonAchatdirect')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => 'Négociation des livreurs',
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => 'Cliquez pour participer a la negociation.',
                                'orderId' => $notification->data['code_livr'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                                                                                                                                                                                                              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                                                                                                                                                                                                                                            </svg>

                                                                                                                                                                                                                                                                                                                                                                                                                                    ',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\AppelOffre')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => 'Appel Offre',
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => 'Cliquez pour participer a la negociation.',
                                'orderId' => $notification->data['code_livr'] ?? 'N/A',
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                                                                                                                                                                                                              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                                                                                                                                                                                                                                            </svg>

                                                                                                                                                                                                                                                                                                                                                                                                                                    ',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\CountdownNotificationAd')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => 'Facture Proformat',
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => 'une negociation demarre .veuillez y participer',
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                                                                                                                                                                                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 11.625h4.5m-4.5 2.25h4.5m2.121 1.527c-1.171 1.464-3.07 1.464-4.242 0-1.172-1.465-1.172-3.84 0-5.304 1.171-1.464 3.07-1.464 4.242 0M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                                                                                                                                                                                                                                                                                                                                                                      </svg>

                                                                                                                                                                                                                                                                                                                                                                                                                                    ',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif (
                            $notification->type === 'App\Notifications\mainleveAd' ||
                                $notification->type === 'App\Notifications\mainleveclient')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => $notification->data['title'],
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => $notification->data['description'],
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-blue-700">
                                                                                                                                                                                                                                                                                                                                                                                                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                                                                                                                                                                                                                                                                                                                                                                                                                    </svg>

                                                                                                                                                                                                                                                                                                                                                                                                                                    ',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\VerifUser')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => 'Main Levée Client',
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => 'Verification de l\'identité du client',
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                                                                                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                                                                                                                                                                                                                                    </svg> ',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\NegosTerminer')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => 'Ganagnant de l\'offre',
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' => 'Verification de l\'identité du client',
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                                                                                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                                                                                                                                                                                                                                    </svg> ',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @endif
                    @endforeach
                    <!-- Aucune notification -->
                    <!-- Section commentée qui peut être activée si aucune notification -->
                    <!--
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-4.215a1 1 0 00-.95-.685H6.355a1 1 0 00-.95.685L4 17h5m7 0a3.25 3.25 0 11-6.5 0h6.5z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune notification</h3>
                        <p class="mt-1 text-sm text-gray-500">Vous n'avez aucune notification pour le moment.</p>
                    </div>
                    -->
                </div>
            </div>
        </div>
    </div>


</div>
