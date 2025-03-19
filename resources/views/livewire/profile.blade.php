<div class="max-w-4xl mx-auto p-4">
    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-lg overflow-hidden">
        {{-- Banner --}}
        <div class="h-48 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10 relative">
            <div class="absolute inset-0 backdrop-blur-sm"></div>
        </div>

        {{-- Profile Header --}}
        <div class="relative px-6 sm:px-8 -mt-20">
            {{-- Photo de profil et informations principales --}}
            <div class="flex flex-col sm:flex-row items-center sm:items-end space-y-4 sm:space-y-0 sm:space-x-6">
                <form action="" method="post" enctype="multipart/form-data" id="photo-upload-form">
                    @csrf
                    @method('PUT')
                    <div class="relative">
                        <div
                            class="w-32 h-32 rounded-2xl overflow-hidden ring-4 ring-white dark:ring-neutral-700 shadow-lg">
                            <img id="img" src="{{ asset($user->photo) }}" class="w-full h-full object-cover"
                                alt="{{ $user->name }}" />
                            <input type="file" id="file-upload1" name="image" class="hidden"
                                onchange="previewImageAndSubmit(this)" />
                            <img id="image-preview1" class="absolute inset-0 w-full h-full object-cover hidden">
                        </div>
                        <label for="file-upload1"
                            class="absolute bottom-0 right-0 p-2 bg-blue-500 hover:bg-blue-600
                            text-white rounded-full shadow-lg cursor-pointer transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </label>
                    </div>
                </form>

                <div class="text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-blue-500 font-medium">{{ '@' . $user->username }}</p>
                </div>
            </div>

            {{-- Messages de notification --}}
            @if (session('success'))
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Navigation --}}
            <div class="mt-8 border-b border-gray-200 dark:border-neutral-700">
                <nav class="flex flex-wrap gap-2 sm:gap-8" aria-label="Tabs">
                    <button type="button"
                        class="hs-tab-active:text-blue-600 hs-tab-active:border-blue-600 py-4 px-1
                        inline-flex items-center gap-2 border-b-2 border-transparent text-sm font-medium
                        text-gray-500 hover:text-blue-600 transition-colors duration-200 whitespace-nowrap
                        dark:text-gray-400 dark:hover:text-blue-500 active"
                        id="basic-tabs-item-1" data-hs-tab="#basic-tabs-1" aria-controls="basic-tabs-1" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informations
                    </button>

                    <button type="button"
                        class="hs-tab-active:text-blue-600 hs-tab-active:border-blue-600 py-4 px-1
                        inline-flex items-center gap-2 border-b-2 border-transparent text-sm font-medium
                        text-gray-500 hover:text-blue-600 transition-colors duration-200 whitespace-nowrap
                        dark:text-gray-400 dark:hover:text-blue-500"
                        id="basic-tabs-item-2" data-hs-tab="#basic-tabs-2" aria-controls="basic-tabs-2" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier profil
                    </button>

                    <button type="button"
                        class="hs-tab-active:text-blue-600 hs-tab-active:border-blue-600 py-4 px-1
                        inline-flex items-center gap-2 border-b-2 border-transparent text-sm font-medium
                        text-gray-500 hover:text-blue-600 transition-colors duration-200 whitespace-nowrap
                        dark:text-gray-400 dark:hover:text-blue-500"
                        id="basic-tabs-item-3" data-hs-tab="#basic-tabs-3" aria-controls="basic-tabs-3" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
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
                        <div class="mb-8 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-4">
                                Double Verification
                            </h3>
                            <livewire:user-search-profile />
                        </div>
                    @endif

                    @if ($user->user_joint)
                        <div class="mb-8 p-6 bg-green-50 dark:bg-green-900/20 rounded-xl">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        @endphp

                        @foreach ($userInfo as $info)
                            <div class="p-4 bg-gray-50 dark:bg-neutral-900/50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M{{ $info['icon'] }}" />
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
                            <div class="p-4 bg-gray-50 dark:bg-neutral-900/50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none"
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
                                            {{ $parrain->name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Profil --}}
                        <button wire:click="LiaisonPromir" wire:loading.attr="disabled"
                            class="px-5 py-2.5
                              {{ $liaison_reussie ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }}
                              text-white font-semibold rounded-xl focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50
                              flex items-center justify-center transition-all duration-300 ease-in-out shadow-md
                              disabled:opacity-50 disabled:cursor-not-allowed relative"
                            {{ $liaison_reussie ? 'disabled' : '' }}>

                            <!-- Texte normal -->
                            <span wire:loading.remove>
                                {{ $liaison_reussie ? 'Liaison approuvée à Promir' : 'Liaison avec Promir' }}
                            </span>

                            <!-- Texte et spinner en chargement -->
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Patientez, cela ne prendra que quelques instants...
                            </span>
                        </button>


                    </div>
                </div>

                {{-- Onglet Modifier profil --}}
                <div id="basic-tabs-2" class="hidden" role="tabpanel" aria-labelledby="basic-tabs-item-2">
                    <form wire:submit.prevent="updateProfile" class="space-y-6 max-w-2xl mx-auto">


                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nom complet
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="text" name="name" value="{{ $user->name }}"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                        dark:text-white transition-colors duration-200">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nom d'utilisateur
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="text" name="username" value="{{ $user->username }}"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                        dark:text-white transition-colors duration-200">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Téléphone
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="text" name="phonenumber" value="{{ $user->phone }}"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                        dark:text-white transition-colors duration-200">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="reset"
                                class="px-6 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
                                dark:bg-neutral-800 dark:hover:bg-neutral-700
                                text-gray-700 dark:text-gray-300 transition-colors duration-200">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-6 py-2 rounded-xl bg-blue-500 hover:bg-blue-600
                                text-white shadow-lg hover:shadow-blue-500/50
                                transition-all duration-200">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Onglet Sécurité --}}
                <div id="basic-tabs-3" class="hidden" role="tabpanel" aria-labelledby="basic-tabs-item-3">
                    <form wire:submit.prevent="updatePassword" class="space-y-6 max-w-2xl mx-auto">


                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Mot de passe actuel
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="password" name="current_password"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                        dark:text-white transition-colors duration-200"
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nouveau mot de passe
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="password" name="new_password"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                        dark:text-white transition-colors duration-200"
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Confirmer le mot de passe
                                </label>
                                <div class="sm:col-span-2">
                                    <input type="password" name="new_password_confirmation"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                        dark:text-white transition-colors duration-200"
                                        placeholder="••••••••">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="reset"
                                class="px-6 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
                                dark:bg-neutral-800 dark:hover:bg-neutral-700
                                text-gray-700 dark:text-gray-300 transition-colors duration-200">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-6 py-2 rounded-xl bg-blue-500 hover:bg-blue-600
                                text-white shadow-lg hover:shadow-blue-500/50
                                transition-all duration-200">
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
