<div class="p-4 mx-auto max-w-4xl">
    <div class="overflow-hidden bg-white rounded-2xl shadow-lg dark:bg-neutral-800">
        {{-- Banner --}}
        <div class="relative h-48 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10">
            <div class="absolute inset-0 backdrop-blur-sm"></div>
        </div>

        {{-- Profile Header --}}
        <div class="relative px-6 -mt-20 sm:px-8">
            {{-- Photo de profil et informations principales --}}
            <div class="flex flex-col items-center space-y-4 sm:flex-row sm:items-end sm:space-y-0 sm:space-x-6">
                <form action="" method="post" enctype="multipart/form-data" id="photo-upload-form">
                    @csrf
                    @method('PUT')
                    <div class="relative">
                        <div
                            class="overflow-hidden w-32 h-32 rounded-2xl ring-4 ring-white shadow-lg dark:ring-neutral-700">
                            <img id="img" src="{{ asset($user->photo) }}" class="object-cover w-full h-full"
                                alt="{{ $user->name }}" />
                            <input type="file" id="file-upload1" name="image" class="hidden"
                                onchange="previewImageAndSubmit(this)" />
                            <img id="image-preview1" class="hidden object-cover absolute inset-0 w-full h-full">
                        </div>
                        <label for="file-upload1"
                            class="absolute right-0 bottom-0 p-2 text-white bg-blue-500 rounded-full shadow-lg transition-colors duration-200 cursor-pointer hover:bg-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </label>
                    </div>
                </form>

                <div class="text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="font-medium text-blue-500">{{ '@' . $user->username }}</p>
                </div>
            </div>

            {{-- Messages de notification --}}
            @if (session('success'))
                <div class="p-4 mt-6 text-green-700 bg-green-50 rounded-xl border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mt-6 text-red-700 bg-red-50 rounded-xl border border-red-200">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Navigation --}}
            <div class="mt-8 border-b border-gray-200 dark:border-neutral-700">
                <nav class="flex flex-wrap gap-2 sm:gap-8" aria-label="Tabs">
                    <button type="button"
                        class="inline-flex gap-2 items-center px-1 py-4 text-sm font-medium text-gray-500 whitespace-nowrap border-b-2 border-transparent transition-colors duration-200 hs-tab-active:text-blue-600 hs-tab-active:border-blue-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500 active"
                        id="basic-tabs-item-1" data-hs-tab="#basic-tabs-1" aria-controls="basic-tabs-1" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informations
                    </button>

                    <button type="button"
                        class="inline-flex gap-2 items-center px-1 py-4 text-sm font-medium text-gray-500 whitespace-nowrap border-b-2 border-transparent transition-colors duration-200 hs-tab-active:text-blue-600 hs-tab-active:border-blue-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500"
                        id="basic-tabs-item-2" data-hs-tab="#basic-tabs-2" aria-controls="basic-tabs-2" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier profil
                    </button>

                    <button type="button"
                        class="inline-flex gap-2 items-center px-1 py-4 text-sm font-medium text-gray-500 whitespace-nowrap border-b-2 border-transparent transition-colors duration-200 hs-tab-active:text-blue-600 hs-tab-active:border-blue-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500"
                        id="basic-tabs-item-3" data-hs-tab="#basic-tabs-3" aria-controls="basic-tabs-3" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Sécurité
                    </button>
                </nav>
            </div>

            {{-- Contenu des onglets --}}
            <div class="py-6">
                {{-- Onglet Informations --}}
                <div id="basic-tabs-1" role="tabpanel" aria-labelledby="basic-tabs-item-1">
                    {{-- Section Double Authentification --}}
                    @if ($user->actor_type == 'Institution' && $user->user_joint == null)
                        <div class="p-6 mb-8 bg-blue-50 rounded-xl dark:bg-blue-900/20">
                            <h3 class="mb-4 text-lg font-semibold text-blue-800 dark:text-blue-300">
                                Double Verification
                            </h3>
                            <livewire:user-search-profile />
                        </div>
                    @endif

                    @if ($user->user_joint)
                        <div class="p-6 mb-8 bg-green-50 rounded-xl dark:bg-green-900/20">
                            <div class="flex gap-3 items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-300">
                                        Double Verification Activée
                                    </h3>
                                    <p class="text-sm text-green-600 dark:text-green-400">
                                        Votre compte est sécurisé avec la double verification
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Informations utilisateur --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        @php
                            $userInfo = [
                                ['label' => 'Nom et prénom', 'value' => $user->name, 'icon' => 'user'],
                                ['label' => 'Username', 'value' => $user->username, 'icon' => 'at-symbol'],
                                ['label' => 'Agent', 'value' => $user->admin->name ?? 'N/A', 'icon' => 'users'],
                                ['label' => 'Téléphone', 'value' => $user->phone, 'icon' => 'phone'],
                                ['label' => 'Code de parrainage', 'value' => $user->id, 'icon' => 'ticket'],
                                ['label' => 'Email', 'value' => $user->email, 'icon' => 'mail'],
                            ];

                            if ($user->country) {
                                $userInfo[] = ['label' => 'Pays', 'value' => $user->country, 'icon' => 'globe'];
                            }
                            if ($user->local_area) {
                                $userInfo[] = [
                                    'label' => 'Localité',
                                    'value' => $user->local_area,
                                    'icon' => 'location-marker',
                                ];
                            }
                            if ($user->address) {
                                $userInfo[] = ['label' => 'Adresse', 'value' => $user->address, 'icon' => 'home'];
                            }
                            if ($user->active_zone) {
                                $userInfo[] = [
                                    'label' => 'Zone d\'activité',
                                    'value' => $user->active_zone,
                                    'icon' => 'map',
                                ];
                            }
                            if ($user->actor_type) {
                                $userInfo[] = [
                                    'label' => 'Type d\'acteur',
                                    'value' => $user->actor_type,
                                    'icon' => 'user-group',
                                ];
                            }
                            if ($user->gender) {
                                $userInfo[] = [
                                    'label' => 'Sexe',
                                    'value' => $user->gender,
                                    'icon' => 'user-circle',
                                ];
                            }
                            if ($user->service_type) {
                                $userInfo[] = [
                                    'label' => 'Type de service',
                                    'value' => $user->service_type,
                                    'icon' => 'briefcase',
                                ];
                            }
                            if ($user->sector) {
                                $userInfo[] = [
                                    'label' => 'Secteur d\'activité',
                                    'value' => $user->sector,
                                    'icon' => 'office-building',
                                ];
                            }

                            // Define SVG paths for each icon type
                            $iconPaths = [
                                'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                'at-symbol' =>
                                    'M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207',
                                'users' =>
                                    'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                                'phone' =>
                                    'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
                                'ticket' =>
                                    'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
                                'mail' =>
                                    'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                'globe' =>
                                    'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                'location-marker' =>
                                    'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
                                'home' =>
                                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                                'map' =>
                                    'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
                                'user-group' =>
                                    'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                                'user-circle' =>
                                    'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                'briefcase' =>
                                    'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                'office-building' =>
                                    'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                // Default icon as fallback
                                'default' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            ];
                        @endphp

                        @foreach ($userInfo as $info)
                            <div class="p-4 bg-gray-50 rounded-xl dark:bg-neutral-900/50">
                                <div class="flex gap-3 items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg dark:bg-blue-900/50">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $iconPaths[$info['icon']] ?? $iconPaths['default'] }}" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                            {{ $info['label'] }}
                                        </p>
                                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $info['value'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($user->parrain)
                            <div class="p-4 bg-gray-50 rounded-xl dark:bg-neutral-900/50">
                                <div class="flex gap-3 items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg dark:bg-blue-900/50">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                            Parrain
                                        </p>
                                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $parrain->name ?? 'N/A' }}                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Profil --}}
                        <button wire:click="LiaisonPromir" wire:loading.attr="disabled"
                            class="flex relative justify-center items-center px-5 py-2.5 font-semibold text-white rounded-xl shadow-md transition-all duration-300 ease-in-out focus:ring-2 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{
                                'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500': !@js($liaison_reussie),
                                'bg-green-600 hover:bg-green-700 focus:ring-green-500': @js($liaison_reussie)
                            }"
                            :disabled="@js($liaison_reussie)" wire:listen.live="liaisonReussie">
                            <span wire:loading.remove>
                                {{ $liaison_reussie ? 'Liaison approuvée à Promir' : 'Liaison avec Promir' }}
                            </span>

                            <span wire:loading class="flex gap-2 items-center">
                                <svg class="mr-2 w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Patientez, cela ne prendra que quelques instants...
                            </span>
                        </button>






                    </div>
                </div>

                {{-- Onglet Modifier profil --}}
                <div id="basic-tabs-2" class="hidden" role="tabpanel" aria-labelledby="basic-tabs-item-2">
                    <form wire:submit.prevent="updateProfile" class="mx-auto space-y-6 max-w-2xl">


                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 items-center sm:grid-cols-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nom complet
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="text" name="name" value="{{ $user->name }}"
                                        class="px-4 py-2 w-full bg-white rounded-xl border border-gray-300 transition-colors duration-200 dark:border-neutral-700 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 items-center sm:grid-cols-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nom d'utilisateur
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="text" name="username" value="{{ $user->username }}"
                                        class="px-4 py-2 w-full bg-white rounded-xl border border-gray-300 transition-colors duration-200 dark:border-neutral-700 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 items-center sm:grid-cols-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Téléphone
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="text" name="phonenumber" value="{{ $user->phone }}"
                                        class="px-4 py-2 w-full bg-white rounded-xl border border-gray-300 transition-colors duration-200 dark:border-neutral-700 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="reset"
                                class="px-6 py-2 text-gray-700 bg-gray-100 rounded-xl transition-colors duration-200 hover:bg-gray-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:text-gray-300">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-6 py-2 text-white bg-blue-500 rounded-xl shadow-lg transition-all duration-200 hover:bg-blue-600 hover:shadow-blue-500/50">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Onglet Sécurité --}}
                <div id="basic-tabs-3" class="hidden" role="tabpanel" aria-labelledby="basic-tabs-item-3">
                    <form wire:submit.prevent="updatePassword" class="mx-auto space-y-6 max-w-2xl">


                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 items-center sm:grid-cols-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Mot de passe actuel
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="password" name="current_password"
                                        class="px-4 py-2 w-full bg-white rounded-xl border border-gray-300 transition-colors duration-200 dark:border-neutral-700 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white"
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 items-center sm:grid-cols-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nouveau mot de passe
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="password" name="new_password"
                                        class="px-4 py-2 w-full bg-white rounded-xl border border-gray-300 transition-colors duration-200 dark:border-neutral-700 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white"
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 items-center sm:grid-cols-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Confirmer le mot de passe
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="password" name="new_password_confirmation"
                                        class="px-4 py-2 w-full bg-white rounded-xl border border-gray-300 transition-colors duration-200 dark:border-neutral-700 dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:text-white"
                                        placeholder="••••••••">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="reset"
                                class="px-6 py-2 text-gray-700 bg-gray-100 rounded-xl transition-colors duration-200 hover:bg-gray-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:text-gray-300">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-6 py-2 text-white bg-blue-500 rounded-xl shadow-lg transition-all duration-200 hover:bg-blue-600 hover:shadow-blue-500/50">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Gestion des onglets
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[data-hs-tab]');
        const contents = document.querySelectorAll('[role="tabpanel"]');

        // Cacher tous les contenus sauf le premier
        contents.forEach((content, index) => {
            if (index !== 0) {
                content.classList.add('hidden');
            }
        });

        // Ajouter les écouteurs d'événements sur les onglets
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Retirer la classe active de tous les onglets
                tabs.forEach(t => {
                    t.classList.remove('border-blue-600', 'text-blue-600');
                    t.classList.add('border-transparent', 'text-gray-500');
                });

                // Ajouter la classe active à l'onglet cliqué
                this.classList.remove('border-transparent', 'text-gray-500');
                this.classList.add('border-blue-600', 'text-blue-600');

                // Cacher tous les contenus
                contents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Afficher le contenu correspondant
                const target = this.getAttribute('data-hs-tab');
                document.querySelector(target).classList.remove('hidden');
            });
        });
    });

    // Votre script existant pour la prévisualisation d'image
    function previewImageAndSubmit(input) {
        const preview = document.getElementById('image-preview1');
        const file = input.files[0];
        const reader = new FileReader();
        const form = document.getElementById('photo-upload-form');

        reader.onloadend = function() {
            preview.src = reader.result;
            preview.classList.remove('hidden');
            form.submit();
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
        }
    }
</script>
