{{-- <div class="max-w-4xl p-6 mx-auto mb-4 bg-white rounded-lg shadow-lg">

    @if (session()->has('success'))
        <div class="mt-4 text-green-500">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 text-red-500">
            {{ session('error') }}
        </div>
    @endif
    <h2 class="mb-4 text-xl font-medium">{{ $demandeur->name }}, vous a fait une demande de retrait</h2>

    <h1 class="text-4xl font-semibold">{{ $amount }} CFA</h1>

    <div class="flex w-full mt-5">
        @if ($notification->reponse)
            <div class="p-2 bg-gray-300 border rounded-md ">
                <p class="font-medium text-center text-md">Réponse envoyée</p>
            </div>
        @else
            <button wire:click='accepteRetrait'
                class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md">


                <span wire:loading.remove>
                    Accepter
                </span>
                <span wire:loading>
                    Chargement...
                    <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                    </svg>
                </span>
            </button>

            <button wire:click='refusRetrait' class="flex p-2 font-medium text-white bg-red-700 rounded-md"><svg
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="mr-2 size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384m-10.253 1.5H9.7m8.075-9.75c.01.05.027.1.05.148.593 1.2.925 2.55.925 3.977 0 1.487-.36 2.89-.999 4.125m.023-8.25c-.076-.365.183-.75.575-.75h.908c.889 0 1.713.518 1.972 1.368.339 1.11.521 2.287.521 3.507 0 1.553-.295 3.036-.831 4.398-.306.774-1.086 1.227-1.918 1.227h-1.053c-.472 0-.745-.556-.5-.96a8.95 8.95 0 0 0 .303-.54" />
                </svg>
                <span wire:loading.remove>
                    Refuser
                </span>
                <span wire:loading>
                    Chargement...
                    <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                    </svg>
                </span>

            </button>
        @endif

    </div>


</div> --}}

<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
    @if (session()->has('success'))
        <div class="mt-4 text-green-500">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 text-red-500">
            {{ session('error') }}
        </div>
    @endif
    <div class="space-y-6">
        <div class="border-b pb-4">
            <h2 class="text-2xl font-semibold text-gray-800">
                Demande de retrait
            </h2>
            <p class="text-gray-600 mt-2">
                {{ $demandeur->name }} vous a fait une demande de retrait
            </p>
        </div>

        <div class="text-center p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Montant du retrait</h3>
            <p class="text-4xl font-bold text-blue-700">{{ $amount }} CFA</p>
        </div>



        <div class="flex justify-center pt-4">
            @if ($notification->reponse)
                <div class="p-2 bg-gray-300 border rounded-md ">
                    <p class="font-medium text-center text-md">Réponse envoyée</p>
                </div>
            @else
                <button wire:click='accepteRetrait'
                    class="flex p-2 mr-4 font-medium text-white bg-green-700 rounded-md">


                    <span wire:loading.remove>
                        Accepter
                    </span>
                    <span wire:loading>
                        Chargement...
                        <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                        </svg>
                    </span>
                </button>

                <button wire:click='refusRetrait' class="flex p-2 font-medium text-white bg-red-700 rounded-md">

                    <span wire:loading.remove>
                        Refuser
                    </span>
                    <span wire:loading>
                        Chargement...
                        <svg class="inline-block w-5 h-5 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292zm0 0V1m0 3.354a7.646 7.646 0 100 15.292 7.646 7.646 0 000-15.292z" />
                        </svg>
                    </span>

                </button>
            @endif
        </div>
    </div>


</div>
