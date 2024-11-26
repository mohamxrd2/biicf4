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
                        @elseif ($notification->type === 'App\Notifications\Confirmation')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => $notification->data['title'],
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' =>
                                    $notification->data['descrip1'] . '' . $notification->data['descrip2'],
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                                                                                                                                                                                                                                                                                                                                                                                                stroke="currentColor" class="size-6 text-green-600">
                                                                                                                                                                                                                                                                                                                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                                                                                                                                                                                                                                                                                                                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                                                                                                                                                                                                                                                                                                                                                                                            </svg>',
                                'markAsRead' => true,
                                'delete' => true,
                            ])
                        @elseif ($notification->type === 'App\Notifications\RefusAchat')
                            @include('biicf.components.ConfirmationNotif', [
                                'title' => 'Commande refusé',
                                'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                                'description' =>
                                    'Votre commande #' . $notification->data['code_unique'] . ' a été refusés.',
                                'orderId' => $notification->data['code_unique'],
                                'svg' => '<svg class="w-full text-red-700 " xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                                                                                                                                                                                                                                                                                                                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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


    @if ($unreadCount == 0)
        <div class="flex flex-col items-center justify-center w-full h-96">

            <svg class="w-12 h-12 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.143 17.082a24.248 24.248 0 0 0 3.844.148m-3.844-.148a23.856 23.856 0 0 1-5.455-1.31 8.964 8.964 0 0 0 2.3-5.542m3.155 6.852a3 3 0 0 0 5.667 1.97m1.965-2.277L21 21m-4.225-4.225a23.81 23.81 0 0 0 3.536-1.003A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6.53 6.53m10.245 10.245L6.53 6.53M3 3l3.53 3.53" />
            </svg>

            <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun notification</h1>
        </div>
    @else
        <div class="relative flex items-center justify-center w-full mb-5">

            <h1 class="relative text-xl font-medium text-slate-800">
                Notifications
                @if ($unreadCount)
                    <span
                        class="absolute top-2 right-[-6px] w-4 h-4 text-[11px] font-semibold text-center flex items-center justify-center bg-red-700 text-white rounded-full transform translate-x-1/2 -translate-y-1/2 p-2">{{ $unreadCount }}</span>
                @endif
            </h1>
        </div>
        @foreach (auth()->user()->notifications as $notification)
            <div
                class="w-full px-3 py-2 @if ($notification->read_at == null) bg-white @else bg-gray-50 @endif  border-y border-gray-200 hover:bg-gray-50">
                @if (
                    $notification->type === 'App\Notifications\commandVerifag' ||
                        $notification->type === 'App\Notifications\commandVerifAd' ||
                        $notification->type === 'App\Notifications\commandVerifAp')
                    <a href="{{ route('notification.show', $notification->id) }}" class="">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">

                                <svg class="w-full text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <p class="font-semibold text-md">Verification de conformité</p>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                    Veillez passez a la verification de la conformité de votre produit !
                                </p>
                            </div>
                        </div>
                    </a>
                @elseif ($notification->type === 'App\Notifications\AOGrouper')
                    <a href="{{ route('notification.show', $notification->id) }}">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">



                                <svg class="w-full text-purple-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path fill-rule="evenodd"
                                        d="M15.22 6.268a.75.75 0 0 1 .968-.431l5.942 2.28a.75.75 0 0 1 .431.97l-2.28 5.94a.75.75 0 1 1-1.4-.537l1.63-4.251-1.086.484a11.2 11.2 0 0 0-5.45 5.173.75.75 0 0 1-1.199.19L9 12.312l-6.22 6.22a.75.75 0 0 1-1.06-1.061l6.75-6.75a.75.75 0 0 1 1.06 0l3.606 3.606a12.695 12.695 0 0 1 5.68-4.974l1.086-.483-4.251-1.632a.75.75 0 0 1-.432-.97Z"
                                        clip-rule="evenodd" />
                                </svg>



                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <p class="font-semibold text-md">Ajout de quantité </p>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                    Proposition d'ajout de quantité pour un appel offre grouper
                                </p>
                            </div>
                        </div>
                    </a>
                @elseif ($notification->type === 'App\Notifications\RefusVerif')
                    <div class="flex w-full">
                        <div class="w-16 h-16 mr-3 overflow-hidden">
                            <svg class="w-full text-red-700" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>

                        <div class="flex flex-col justify-between w-full ">
                            <div class="flex items-center justify-between w-full">
                                <p class="font-semibold text-md">Colis refuser</p>
                                <p class="text-[12px] text-gray-400 text-right">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </p>
                            </div>
                            <p class="text-sm font-normal text-slate-500 l max-w-1/2">Le colis à été refusé apres
                                verification !</p>
                        </div>
                    </div>
                @elseif ($notification->type === 'App\Notifications\AchatGroupBiicf')
                    <a href="{{ route('notification.show', $notification->id) }}" class="">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden ">
                                <img src="{{ asset($notification->data['photoProd']) }}" alt="Product Image"
                                    class="object-cover w-full h-full">

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $notification->data['nameProd'] }}</h3>

                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>

                                </div>
                                <div class="flex items-center justify-between w-full h-full">

                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous avez reçu une
                                        commande
                                        de cet article en
                                    <h1 class="text-[20px] font-bold">achat groupé </h1>
                                    </p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>

                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @elseif ($notification->type === 'App\Notifications\OffreNotif')
                    <a href="{{ route('biicf.postdet', $notification->data['produit_id']) }}" class="">

                        <div class="flex w-full">
                            @if (isset($notification->data['produit_id']))
                                @php
                                    $produtOffre = App\Models\ProduitService::find($notification->data['produit_id']);
                                @endphp
                            @endif

                            <div class="w-16 h-16 mr-3 overflow-hidden ">

                                <img src="{{ asset('post/all/' . $produtOffre->photoProd1) }}" alt="Product Image"
                                    class="object-cover w-full h-full">

                            </div>


                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full ">
                                    <h3 class="font-semibold text-md">{{ $produtOffre->name }}</h3>

                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>

                                </div>
                                <div class="flex items-center justify-between w-full h-full">

                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous avez reçu une
                                        une offre de ce produit
                                    </p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>

                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                    {{-- @elseif($notification->type === 'App\Notifications\AchatBiicf') --}}
                    {{-- <a href="{{ route('notification.show', $notification->id) }}" class="">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">
                                <img src="{{ asset('post/all/' . $notification->data['photoProd']) }}"
                                    alt="Product Image" class="object-cover w-full h-full">
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $notification->data['nameProd'] }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 max-w-1/2">
                                        Vous avez reçu une commande de cet article en achat direct
                                    </p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>

                                        </div>
                                    @endif
                                </div>


                            </div>
                        </div>
                    </a> --}}

                @elseif($notification->type === 'App\Notifications\AppelOffreGrouperNotification')
                    <a href="{{ route('notification.show', $notification->id) }}" class="">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">
                                <svg class="w-full text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path
                                        d="M4.913 2.658c2.075-.27 4.19-.408 6.337-.408 2.147 0 4.262.139 6.337.408 1.922.25 3.291 1.861 3.405 3.727a4.403 4.403 0 0 0-1.032-.211 50.89 50.89 0 0 0-8.42 0c-2.358.196-4.04 2.19-4.04 4.434v4.286a4.47 4.47 0 0 0 2.433 3.984L7.28 21.53A.75.75 0 0 1 6 21v-4.03a48.527 48.527 0 0 1-1.087-.128C2.905 16.58 1.5 14.833 1.5 12.862V6.638c0-1.97 1.405-3.718 3.413-3.979Z" />
                                    <path
                                        d="M15.75 7.5c-1.376 0-2.739.057-4.086.169C10.124 7.797 9 9.103 9 10.609v4.285c0 1.507 1.128 2.814 2.67 2.94 1.243.102 2.5.157 3.768.165l2.782 2.781a.75.75 0 0 0 1.28-.53v-2.39l.33-.026c1.542-.125 2.67-1.433 2.67-2.94v-4.286c0-1.505-1.125-2.811-2.664-2.94A49.392 49.392 0 0 0 15.75 7.5Z" />
                                </svg>
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $notification->data['productName'] }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous avez été ciblez dans
                                        un appel
                                        offre groupé <span class="text-bold">cliquez pour participer à la
                                            négociation</span> </p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @elseif($notification->type === 'App\Notifications\OffreNotifGroup')
                    <a href="{{ route('notification.show', $notification->id) }}" class="">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">
                                <svg class="w-full text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path
                                        d="M4.913 2.658c2.075-.27 4.19-.408 6.337-.408 2.147 0 4.262.139 6.337.408 1.922.25 3.291 1.861 3.405 3.727a4.403 4.403 0 0 0-1.032-.211 50.89 50.89 0 0 0-8.42 0c-2.358.196-4.04 2.19-4.04 4.434v4.286a4.47 4.47 0 0 0 2.433 3.984L7.28 21.53A.75.75 0 0 1 6 21v-4.03a48.527 48.527 0 0 1-1.087-.128C2.905 16.58 1.5 14.833 1.5 12.862V6.638c0-1.97 1.405-3.718 3.413-3.979Z" />
                                    <path
                                        d="M15.75 7.5c-1.376 0-2.739.057-4.086.169C10.124 7.797 9 9.103 9 10.609v4.285c0 1.507 1.128 2.814 2.67 2.94 1.243.102 2.5.157 3.768.165l2.782 2.781a.75.75 0 0 0 1.28-.53v-2.39l.33-.026c1.542-.125 2.67-1.433 2.67-2.94v-4.286c0-1.505-1.125-2.811-2.664-2.94A49.392 49.392 0 0 0 15.75 7.5Z" />
                                </svg>
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $notification->data['produit_name'] }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous etes ciblé pour un
                                        appel
                                        OFFRE NEGOCIER; cliquez pour participer à la négociation</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
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
                            <div class="w-16 h-16 mr-3 overflow-hidden">



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
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                        Vous venez de gagnez l'enchere sur le produit... Cliquez pour ajouter la
                                        quantité que vous voulez commander</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>


                            </div>
                        </div>
                    </a>
                @elseif($notification->type === 'App\Notifications\AppelOffreTerminer')
                    <a href="{{ route('notification.show', $notification->id) }}" class="">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">

                                <svg class="w-full text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">Négociation terminer</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                        Vous avez gagner accepter pour procéder à la livraison</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @elseif($notification->type === 'App\Notifications\AppelOffreTerminerGrouper')
                    <a href="{{ route('notification.show', $notification->id) }}" class="">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">

                                <svg class="w-full text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">Négociation terminer</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                        Vous avez gagner accepter pour procéder à la livraison</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @elseif ($notification->type === 'App\Notifications\OffreNegosNotif')
                    <a href="{{ route('notification.show', $notification->id) }}">
                        <div class="flex w-full">

                            <div class="w-16 h-16 mr-3 overflow-hidden">
                                <svg class="w-full text-yellow-300" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path fill-rule="evenodd"
                                        d="M19.5 21a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3h-5.379a.75.75 0 0 1-.53-.22L11.47 3.66A2.25 2.25 0 0 0 9.879 3H4.5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h15Zm-6.75-10.5a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25v2.25a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V10.5Z"
                                        clip-rule="evenodd" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $notification->data['produit_name'] }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous etes ciblé pour une
                                        offre groupé. Cliquez pour participer à la négociation</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
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
                            <div class="w-16 h-16 mr-3 overflow-hidden ">
                                <img src="{{ asset($produit->photoProd1) }}" alt="Product Image"
                                    class="object-cover w-full h-full">
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $produit->name }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous avez reçu une offre
                                        de ce produit !</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </a>
                @elseif (
                    $notification->type === 'App\Notifications\CountdownNotificationAg' ||
                        $notification->type === 'App\Notifications\CountdownNotificationAd' ||
                        $notification->type === 'App\Notifications\CountdownNotificationAp')
                    <a href="{{ route('notification.show', $notification->id) }}">


                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden ">

                                <svg class="w-full text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.621 9.879a3 3 0 0 0-5.02 2.897l.164.609a4.5 4.5 0 0 1-.108 2.676l-.157.439.44-.22a2.863 2.863 0 0 1 2.185-.155c.72.24 1.507.184 2.186-.155L15 18M8.25 15.75H12m-1.5-13.5H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>


                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">Facture proformat</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous avez été
                                        identifié discustion entre les livreurs terminées consulter votre facture
                                        proformat</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </a>
                @elseif ($notification->type === 'App\Notifications\GrouperFactureNotifications')
                    <a href="{{ route('notification.show', $notification->id) }}">

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden ">

                                <svg class="w-full text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.621 9.879a3 3 0 0 0-5.02 2.897l.164.609a4.5 4.5 0 0 1-.108 2.676l-.157.439.44-.22a2.863 2.863 0 0 1 2.185-.155c.72.24 1.507.184 2.186-.155L15 18M8.25 15.75H12m-1.5-13.5H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>


                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">Facture proformat</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous avez été
                                        identifié discustion entre les livreurs terminées consulter votre facture
                                        proformat</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </a>
                @elseif (
                    $notification->type === 'App\Notifications\livraisonVerif' ||
                        $notification->type === 'App\Notifications\livraisonAchatdirect' ||
                        $notification->type === 'App\Notifications\livraisonAppelOffre' ||
                        $notification->type === 'App\Notifications\livraisonAppelOffregrouper')
                    <a href="{{ route('notification.show', $notification->id) }}">
                        @php
                            $produit = ProduitService::find($notification->data['idProd']);
                        @endphp
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden ">
                                <img class="object-cover w-full h-full rounded-xl"
                                    src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="">
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $produit->name }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Vous avez été
                                        identifié
                                        dans une commande a livré !</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\AllerChercher')
                    <a href="{{ route('notification.show', $notification->id) }}">
                        @php
                            $produit = ProduitService::find($notification->data['idProd']);
                        @endphp
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden ">
                                <img class="object-cover w-full h-full rounded-xl"
                                    src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="">
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $produit->name }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Appretez pour aller
                                        chercher votre commande .</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">...Veuillez cliquez pour
                                    vérifier votre facture!</p>
                            </div>

                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\VerifUser')

                @elseif (
                    $notification->type === 'App\Notifications\mainleve' ||
                        $notification->type === 'App\Notifications\mainleveAd' ||
                        $notification->type === 'App\Notifications\mainleveAp')
                    <a href="{{ route('notification.show', $notification->id) }}">

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">
                                <svg class="w-full text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path
                                        d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 1 1 6 0h3a.75.75 0 0 0 .75-.75V15Z" />
                                    <path
                                        d="M8.25 19.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0ZM15.75 6.75a.75.75 0 0 0-.75.75v11.25c0 .087.015.17.042.248a3 3 0 0 1 5.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 0 0-3.732-10.104 1.837 1.837 0 0 0-1.47-.725H15.75Z" />
                                    <path d="M19.5 19.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                                </svg>
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">Livraison à effectué</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-gray-700 l max-w-1/2">Vous avez été
                                        identifié
                                        dans une commande a livré !</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </a>
                @elseif ($notification->type === 'App\Notifications\mainlevefour')
                    <a href="{{ route('notification.show', $notification->id) }}">
                        @php
                            $produit = ProduitService::find($notification->data['idProd']);
                        @endphp

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden ">
                                <img class="object-cover w-full h-full rounded-xl"
                                    src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="">
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $produit->name }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Arrivage du livreur et
                                        verification de confromité</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\attenteclient')
                    <a href="{{ route('notification.show', $notification->id) }}">
                        @php
                            $produit = ProduitService::find($notification->data['idProd']);
                        @endphp

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden ">
                                <img class="object-cover w-full h-full rounded-xl"
                                    src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="">
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $produit->name }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Attente du client pour
                                        recuperation du colis et
                                        verification de confromité</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\mainleveclient')
                    <a href="{{ route('notification.show', $notification->id) }}">
                        @php
                            $produit = ProduitService::find($notification->data['idProd']);
                        @endphp

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden ">
                                <img class="object-cover w-full h-full rounded-xl"
                                    src="{{ $produit->photoProd1 ? asset('post/all/' . $produit->photoProd1) : asset('img/noimg.jpeg') }}"
                                    alt="">
                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <h3 class="font-semibold text-md">{{ $produit->name }}</h3>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between w-full h-full">
                                    <p class="text-sm font-normal text-slate-500 l max-w-1/2">Arrivage du livreur et
                                        verification de confromité</p>
                                    @if ($notification->read_at == null)
                                        <div class="flex items-center justify-center w-10">
                                            <span class="w-2 h-2 bg-purple-700 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\colisaccept')
                    <a href="{{ route('notification.show', $notification->id) }}" class="">
                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">
                                <svg class="w-full text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <p class="font-semibold text-md">Colis livré avec sussès !</p>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                    La livraison à été effectué avec succèes !
                                </p>
                            </div>
                        </div>
                    </a>
                @elseif ($notification->type === 'App\Notifications\Retrait')
                    <a href="{{ route('notification.show', $notification->id) }}" class="">

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">


                                <svg class="w-full text-amber-900" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>


                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <p class="font-semibold text-md">Demande de Retrait</p>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                    Vous avez reçu une demande retrait veuillez accepter
                                </p>
                            </div>
                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\RefusRetrait')
                    <div class="flex w-full">
                        <div class="w-16 h-16 mr-3 overflow-hidden">
                            <svg class="w-full text-amber-900" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                        </div>

                        <div class="flex flex-col justify-between w-full">
                            <div class="flex items-center justify-between w-full">
                                <p class="font-semibold text-md">Demande de Retrait refuser</p>
                                <p class="text-[12px] text-gray-400 text-right">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </p>
                            </div>
                            <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                Votre demande de retrait a été refuser
                            </p>
                        </div>
                    </div>
                @elseif ($notification->type === 'App\Notifications\AcceptRetrait')
                    <div class="flex w-full">
                        <div class="w-16 h-16 mr-3 overflow-hidden">
                            <svg class="w-full text-amber-900" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                        </div>

                        <div class="flex flex-col justify-between w-full">
                            <div class="flex items-center justify-between w-full">
                                <p class="font-semibold text-md">Demande de Retrait accepter</p>
                                <p class="text-[12px] text-gray-400 text-right">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </p>
                            </div>
                            <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                Votre demande de retrait a été accepter avec success
                            </p>
                        </div>
                    </div>
                @elseif ($notification->type === 'App\Notifications\appelivreur')
                    <div class="flex w-full">
                        <div class="w-16 h-16 mr-3 overflow-hidden">
                            {{-- <svg class="w-full text-amber-900" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg> --}}
                        </div>

                        <div class="flex flex-col justify-between w-full">
                            <div class="flex items-center justify-between w-full">
                                <p class="font-semibold text-md">main leve effectuer pour le groupage </p>
                                <p class="text-[12px] text-gray-400 text-right">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </p>
                            </div>
                            <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                accepter la facture proformat et le proceder a la livraiso de votre colis
                            </p>
                        </div>
                    </div>
                @elseif ($notification->type === 'App\Notifications\DepositSos')
                    <a href="{{ route('notification.show', $notification->id) }}">

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-full text-gray-900" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <p class="font-semibold text-md">Demande de rechargement SOS </p>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                    Cliqué pour accepter la demande de rechargement SOS
                                </p>
                            </div>
                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\DepositRecu')
                    <a href="{{ route('notification.show', $notification->id) }}">

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-full text-green-400" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <p class="font-semibold text-md">Rechargement SOS </p>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                    Votre de demande de rechargement à été accepter
                                </p>
                            </div>
                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\DepositSend')
                    <a href="{{ route('notification.show', $notification->id) }}">

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-full text-blue-400" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <p class="font-semibold text-md">Rechargement SOS </p>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                    Veillez verifier le recu et confirmer
                                </p>
                            </div>
                        </div>

                    </a>
                @elseif ($notification->type === 'App\Notifications\PortionJournaliere')
                    <a href="#">

                        <div class="flex w-full">
                            <div class="w-16 h-16 mr-3 overflow-hidden">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-full text-blue-400" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                            </div>

                            <div class="flex flex-col justify-between w-full">
                                <div class="flex items-center justify-between w-full">
                                    <p class="font-semibold text-md">Rappel de remboursement Pour Le Credit
                                        {{ $notification->data['credit_id'] }}</p>
                                    <p class="text-[12px] text-gray-400 text-right">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-sm font-normal text-slate-500 l max-w-1/2">
                                    {{ $notification->data['message'] }}
                                </p>
                            </div>
                        </div>

                    </a>
                @endif


            </div>
        @endforeach
    @endif
</div>
