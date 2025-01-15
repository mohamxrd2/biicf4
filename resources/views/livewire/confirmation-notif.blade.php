<div>
    <x-offre.alert-messages />

    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto max-w-2xl px-4 2xl:px-0">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl mb-2">Merci pour votre
                confiance !</h2>
            @if ($notification->data['quantiteTotal'])
                <p class="text-gray-500 dark:text-gray-400 mb-6 md:mb-8">
                    Votre commande <span
                        class="font-medium text-gray-900 dark:text-white hover:underline">#{{ $notification->data['code_unique'] }}</span>,
                    d'une quantité de <span
                        class="font-medium text-gray-900">{{ $notification->data['quantiteTotal'] }}</span>,
                    doit être validée dans un délai de 48 heures après la réception de cette notification, afin de
                    permettre
                    aux fournisseurs ciblés de se regrouper efficacement.
                </p>
                <div
                    class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 mb-3 border border-blue-100 dark:from-blue-900 dark:to-indigo-900">
                    <div class="flex items-center space-x-3 mb-4">
                        <svg class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-blue-800 dark:text-blue-300">Prix à payer</h3>
                    </div>
                    <p class="text-4xl font-bold text-blue-900 dark:text-white tracking-wider">
                        {{ number_format($this->produit->prix * $this->notification->data['quantiteTotal'] ?? 0, 0, ',', ' ') }}
                        FCFA
                    </p>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 mb-6 md:mb-8">Votre achat <a href="#"
                        class="font-medium text-gray-900 dark:text-white hover:underline">#{{ $notification->data['code_unique'] }}</a>
                    sera traitée sous
                    24 heures pendant les jours ouvrables. Nous vous enverrons une notification une fois que votre
                    achat aura
                    été recu par le fournisseur.
                </p>
            @endif



            <div
                class="space-y-4 sm:space-y-2 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800 mb-6 md:mb-8">
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Date</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">
                        {{ $notification->created_at }}</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Mode de paiement</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">Compte Operation Courant</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Nom</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{ $user->name }}</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Adresse</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{ $user->commune }}</dd>
                </dl>
                <dl class="sm:flex items-center justify-between gap-4">
                    <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Téléphone</dt>
                    <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{ $user->phone }}</dd>
                </dl>
            </div>

            @if ($notification->data['quantiteTotal'] && !$notification->reponse)
                {{-- Bouton  --}}
                <div class="flex space-x-2">
                    <button wire:click.prevent='CommandeRefuser' wire:loading.attr="disabled"
                        @class([
                            'px-4 py-2 text-sm font-medium rounded-lg shadow-md transition-colors',
                            'opacity-50 cursor-not-allowed' => $isLoading,
                            'text-gray-700 bg-gray-100 hover:bg-gray-200' => !$isLoading,
                        ]) {{ $isLoading ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="CommandeRefuser">
                            Refuser la facture
                        </span>
                        <span wire:loading wire:target="CommandeRefuser" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Refus en cours...
                        </span>
                    </button>

                    {{-- Bouton Accepter --}}
                    <button wire:click.prevent='CommandeAccepter' wire:loading.attr="disabled"
                        @class([
                            'px-4 py-2 text-sm font-medium rounded-lg shadow-md transition-colors',
                            'opacity-50 cursor-not-allowed' => $isLoading,
                            'text-white bg-blue-600 hover:bg-blue-700' => !$isLoading,
                        ]) {{ $isLoading ? 'disabled' : '' }}>

                        <span wire:loading.remove wire:target="CommandeAccepter">
                            Accepter la facture
                        </span>
                        <span wire:loading wire:target="CommandeAccepter" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Accepter en cours...
                        </span>
                    </button>
                </div>
            @elseif ($notification->reponse == 'accepter')
                <div class="flex items-center space-x-4">
                    <a href="#" data-modal-target="timeline-modal" data-modal-toggle="timeline-modal"
                        class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-500 dark:hover:bg-green-600 focus:outline-none dark:focus:ring-green-700">Suivre
                        votre commande</a>
                </div>

                <!-- Main modal -->
                <div id="timeline-modal" tabindex="-1" aria-hidden="true"
                    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Les etapes de la commande
                                </h3>
                                <button type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-modal-toggle="timeline-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-4 md:p-5">
                                <ol class="relative border-s border-gray-200 dark:border-gray-600 ms-3.5 mb-4 md:mb-5">
                                    <li class="mb-10 ms-8">
                                        <span
                                            class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -start-3.5 ring-8 ring-white dark:ring-gray-700 dark:bg-gray-600">
                                            <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path fill="currentColor"
                                                    d="M6 1a1 1 0 0 0-2 0h2ZM4 4a1 1 0 0 0 2 0H4Zm7-3a1 1 0 1 0-2 0h2ZM9 4a1 1 0 1 0 2 0H9Zm7-3a1 1 0 1 0-2 0h2Zm-2 3a1 1 0 1 0 2 0h-2ZM1 6a1 1 0 0 0 0 2V6Zm18 2a1 1 0 1 0 0-2v2ZM5 11v-1H4v1h1Zm0 .01H4v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM10 11v-1H9v1h1Zm0 .01H9v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM10 15v-1H9v1h1Zm0 .01H9v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM15 15v-1h-1v1h1Zm0 .01h-1v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM15 11v-1h-1v1h1Zm0 .01h-1v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM5 15v-1H4v1h1Zm0 .01H4v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM2 4h16V2H2v2Zm16 0h2a2 2 0 0 0-2-2v2Zm0 0v14h2V4h-2Zm0 14v2a2 2 0 0 0 2-2h-2Zm0 0H2v2h16v-2ZM2 18H0a2 2 0 0 0 2 2v-2Zm0 0V4H0v14h2ZM2 4V2a2 2 0 0 0-2 2h2Zm2-3v3h2V1H4Zm5 0v3h2V1H9Zm5 0v3h2V1h-2ZM1 8h18V6H1v2Zm3 3v.01h2V11H4Zm1 1.01h.01v-2H5v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H5v2h.01v-2ZM9 11v.01h2V11H9Zm1 1.01h.01v-2H10v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H10v2h.01v-2ZM9 15v.01h2V15H9Zm1 1.01h.01v-2H10v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H10v2h.01v-2ZM14 15v.01h2V15h-2Zm1 1.01h.01v-2H15v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H15v2h.01v-2ZM14 11v.01h2V11h-2Zm1 1.01h.01v-2H15v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H15v2h.01v-2ZM4 15v.01h2V15H4Zm1 1.01h.01v-2H5v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H5v2h.01v-2Z" />
                                            </svg>
                                        </span>
                                        <h3
                                            class="flex items-start mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                                            Flowbite Application UI v2.0.0 <span
                                                class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 ms-3">Latest</span>
                                        </h3>
                                        <time
                                            class="block mb-3 text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Released
                                            on Nov 10th, 2023</time>
                                        <button type="button"
                                            class="py-2 px-3 inline-flex items-center text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                            <svg class="w-3 h-3 me-1.5" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                                                <path
                                                    d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                                            </svg>
                                            Download
                                        </button>
                                    </li>
                                    <li class="mb-10 ms-8">
                                        <span
                                            class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -start-3.5 ring-8 ring-white dark:ring-gray-700 dark:bg-gray-600">
                                            <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path fill="currentColor"
                                                    d="M6 1a1 1 0 0 0-2 0h2ZM4 4a1 1 0 0 0 2 0H4Zm7-3a1 1 0 1 0-2 0h2ZM9 4a1 1 0 1 0 2 0H9Zm7-3a1 1 0 1 0-2 0h2Zm-2 3a1 1 0 1 0 2 0h-2ZM1 6a1 1 0 0 0 0 2V6Zm18 2a1 1 0 1 0 0-2v2ZM5 11v-1H4v1h1Zm0 .01H4v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM10 11v-1H9v1h1Zm0 .01H9v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM10 15v-1H9v1h1Zm0 .01H9v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM15 15v-1h-1v1h1Zm0 .01h-1v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM15 11v-1h-1v1h1Zm0 .01h-1v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM5 15v-1H4v1h1Zm0 .01H4v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM2 4h16V2H2v2Zm16 0h2a2 2 0 0 0-2-2v2Zm0 0v14h2V4h-2Zm0 14v2a2 2 0 0 0 2-2h-2Zm0 0H2v2h16v-2ZM2 18H0a2 2 0 0 0 2 2v-2Zm0 0V4H0v14h2ZM2 4V2a2 2 0 0 0-2 2h2Zm2-3v3h2V1H4Zm5 0v3h2V1H9Zm5 0v3h2V1h-2ZM1 8h18V6H1v2Zm3 3v.01h2V11H4Zm1 1.01h.01v-2H5v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H5v2h.01v-2ZM9 11v.01h2V11H9Zm1 1.01h.01v-2H10v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H10v2h.01v-2ZM9 15v.01h2V15H9Zm1 1.01h.01v-2H10v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H10v2h.01v-2ZM14 15v.01h2V15h-2Zm1 1.01h.01v-2H15v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H15v2h.01v-2ZM14 11v.01h2V11h-2Zm1 1.01h.01v-2H15v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H15v2h.01v-2ZM4 15v.01h2V15H4Zm1 1.01h.01v-2H5v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H5v2h.01v-2Z" />
                                            </svg>
                                        </span>
                                        <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Flowbite
                                            Figma
                                            v2.8.0</h3>
                                        <time
                                            class="block mb-3 text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Released
                                            on Oct 7th, 2023</time>
                                        <button type="button"
                                            class="py-2 px-3 inline-flex items-center text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                            <svg class="w-3 h-3 me-1.5" aria-hidden="true" viewBox="0 0 30 45"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.50012 45C11.6401 45 15.0002 41.6399 15.0002 37.4999V29.9999H7.50012C3.36009 29.9999 0 33.3599 0 37.4999C0 41.6399 3.36009 45 7.50012 45Z"
                                                    fill="#0ACF83" />
                                                <path
                                                    d="M0 22.5C0 18.36 3.36009 14.9999 7.50012 14.9999H15.0002V29.9999H7.50012C3.36009 30.0001 0 26.64 0 22.5Z"
                                                    fill="#A259FF" />
                                                <path
                                                    d="M0 7.50006C0 3.36006 3.36009 0 7.50012 0H15.0002V14.9999H7.50012C3.36009 14.9999 0 11.6401 0 7.50006Z"
                                                    fill="#F24E1E" />
                                                <path
                                                    d="M15.0002 0H22.4999C26.6399 0 30 3.36006 30 7.50006C30 11.6401 26.6399 14.9999 22.4999 14.9999L15.0002 14.9999V0Z"
                                                    fill="#FF7262" />
                                                <path
                                                    d="M30 22.5C30 26.64 26.6399 30 22.4999 30C18.3599 30 14.9998 26.64 14.9998 22.5C14.9998 18.36 18.3599 14.9999 22.4999 14.9999C26.6399 14.9999 30 18.36 30 22.5Z"
                                                    fill="#1ABCFE" />
                                            </svg>
                                            Duplicate in Figma
                                        </button>
                                    </li>
                                    <li class="ms-8">
                                        <span
                                            class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -start-3.5 ring-8 ring-white dark:ring-gray-700 dark:bg-gray-600">
                                            <svg class="w-2.5 h-2.5 text-gray-500 dark:text-gray-400"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 20 20">
                                                <path fill="currentColor"
                                                    d="M6 1a1 1 0 0 0-2 0h2ZM4 4a1 1 0 0 0 2 0H4Zm7-3a1 1 0 1 0-2 0h2ZM9 4a1 1 0 1 0 2 0H9Zm7-3a1 1 0 1 0-2 0h2Zm-2 3a1 1 0 1 0 2 0h-2ZM1 6a1 1 0 0 0 0 2V6Zm18 2a1 1 0 1 0 0-2v2ZM5 11v-1H4v1h1Zm0 .01H4v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM10 11v-1H9v1h1Zm0 .01H9v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM10 15v-1H9v1h1Zm0 .01H9v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM15 15v-1h-1v1h1Zm0 .01h-1v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM15 11v-1h-1v1h1Zm0 .01h-1v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM5 15v-1H4v1h1Zm0 .01H4v1h1v-1Zm.01 0v1h1v-1h-1Zm0-.01h1v-1h-1v1ZM2 4h16V2H2v2Zm16 0h2a2 2 0 0 0-2-2v2Zm0 0v14h2V4h-2Zm0 14v2a2 2 0 0 0 2-2h-2Zm0 0H2v2h16v-2ZM2 18H0a2 2 0 0 0 2 2v-2Zm0 0V4H0v14h2ZM2 4V2a2 2 0 0 0-2 2h2Zm2-3v3h2V1H4Zm5 0v3h2V1H9Zm5 0v3h2V1h-2ZM1 8h18V6H1v2Zm3 3v.01h2V11H4Zm1 1.01h.01v-2H5v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H5v2h.01v-2ZM9 11v.01h2V11H9Zm1 1.01h.01v-2H10v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H10v2h.01v-2ZM9 15v.01h2V15H9Zm1 1.01h.01v-2H10v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H10v2h.01v-2ZM14 15v.01h2V15h-2Zm1 1.01h.01v-2H15v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H15v2h.01v-2ZM14 11v.01h2V11h-2Zm1 1.01h.01v-2H15v2Zm1.01-1V11h-2v.01h2Zm-1-1.01H15v2h.01v-2ZM4 15v.01h2V15H4Zm1 1.01h.01v-2H5v2Zm1.01-1V15h-2v.01h2Zm-1-1.01H5v2h.01v-2Z" />
                                            </svg>
                                        </span>
                                        <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Flowbite
                                            Library v1.2.2</h3>
                                        <time
                                            class="block mb-3 text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Released
                                            on December 2nd, 2021</time>
                                    </li>
                                </ol>
                                <button
                                    class="text-white inline-flex w-full justify-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    My Downloads
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($notification->reponse == 'refuser')
                <div class="flex items-center space-x-4">
                    <a href="#" data-modal-target="timeline-modal" data-modal-toggle="timeline-modal"
                        class="text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none dark:focus:ring-gray-700">
                        Commande Refusée
                    </a>
                </div>
            @endif

        </div>
    </section>
</div>
