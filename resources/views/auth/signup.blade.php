<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | BIICF</title>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.21/css/intlTelInput.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.0/cdn.min.js" defer></script>

</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-3xl bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Créer votre compte</h1>
                <p class="text-gray-600 mt-2">Complétez les informations suivantes pour vous inscrire</p>
            </div>

            <!-- Stepper Progress -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="w-full flex items-center">
                        <div class="relative flex flex-col items-center flex-1">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold step-active">
                                1
                            </div>
                            <p class="text-sm font-medium text-gray-700 mt-2">Informations personnelles</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 step-line"></div>
                        <div class="relative flex flex-col items-center flex-1">
                            <div
                                class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold">
                                2
                            </div>
                            <p class="text-sm font-medium text-gray-500 mt-2">Localisation & Contact</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="signupForm" action="{{ route('biicf.signup') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Step 1 -->
                <div id="step1" class="space-y-6">
                    <!-- Nom et Prénom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input label="Nom ou raison sociale *" name="name" :errors="$errors" required />
                        <x-input label="Prénom *" name="last-name" :errors="$errors" required />
                    </div>

                    <!-- Nom d'utilisateur et Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input label="Nom d'utilisateur *" name="username" :errors="$errors" required />
                        <x-input label="Email *" type="email" name="email" :errors="$errors" required />
                    </div>

                    <!-- Mot de passe -->
                    <div x-data="{
                        password: '',
                        repeatPassword: '',
                        get passwordsMatch() {
                            return this.password === this.repeatPassword;
                        },
                        get showError() {
                            return this.repeatPassword.length > 0 && !this.passwordsMatch;
                        }
                    }" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe *</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" x-model="password"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <button type="button"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    onclick="togglePassword('password')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe
                                *</label>
                            <div class="relative">
                                <input type="password" name="repeat-password" id="repeat-password"
                                    x-model="repeatPassword"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': showError }" required>
                                <button type="button"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    onclick="togglePassword('repeat-password')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <span x-show="showError" x-transition class="text-red-500 text-sm mt-1 block">
                                Les mots de passe ne correspondent pas
                            </span>
                        </div>

                    </div>

                    <!-- Type d'acteur et Type d'investisseur -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type d'acteur *</label>
                            <select name="user_type" value="{{ old('user_type') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Personne physique">Personne physique</option>
                                <option value="Personne morale">Personne morale</option>
                                <option value="Service public">Service public</option>
                                <option value="Institution">Institution</option>
                                <option value="Organisme">Organisme</option>
                                <option value="Communauté">Communauté</option>
                                <option value="Menage">Ménage</option>
                            </select>
                            @error('user_type')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type d'investisseur *</label>
                            <select name="invest_type" value="{{ old('invest_type') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Bank/IFD">Bank/IFD</option>
                                <option value="Pgm Public/Para-Public">Pgm Public/Para-Public</option>
                                <option value="Fonds d'investissement">Fonds d'investissement</option>
                                <option value="Particulier">Particulier</option>
                            </select>
                            @error('invest_type')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Proportion à investir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proportion à investir *</label>
                        <select name="investisement" value="{{ old('investisement') }}"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">Choisissez une tranche (FCFA)</option>
                            <option value="1-500.000">1-500.000</option>
                            <option value="500.001-1.000.000">500.001-1.000.000</option>
                            <option value="1.000.001-5.000.000">1.000.001-5.000.000</option>
                            <option value="5.000.001-10.000.000">5.000.001-10.000.000</option>
                            <option value="10.000.001-50.000.000">10.000.001-50.000.000</option>
                            <option value="50.000.001 et plus">50.000.001 et plus</option>
                        </select>
                        @error('investisement')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div id="step2" class="hidden space-y-6">
                    <!-- Téléphone et Pays -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('phone')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pays *</label>
                            <select id="address-country" name="country" value="{{ old('country') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">Sélectionnez un pays</option>
                            </select>
                            @error('country')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Continent et Sous-région -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Continent</label>
                            <input type="text" id="continent" name="continent" value="{{ old('continent') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50" readonly>
                            @error('continent')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sous-région</label>
                            <input type="text" id="subregion" name="sous_region"
                                value="{{ old('sous_region') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50" readonly>
                            @error('continent')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Département et Ville -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Département *</label>
                            <input type="text" name="departement" value="{{ old('departement') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('departement')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                            <input type="text" name="ville" value="{{ old('ville') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('ville')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Commune et Code de parrainage -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Commune *</label>
                            <input type="text" name="commune" value="{{ old('commune') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('commune')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Code de parrainage</label>
                            <input type="number" name="parrain" value="{{ old('parrain') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Optionnel">
                            @error('parrain')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-8">
                    <button type="button" id="prevBtn"
                        class="hidden px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Retour
                    </button>

                    <button type="button" id="nextBtn"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Suivant
                    </button>

                    <button type="submit" id="submitBtn"
                        class="hidden px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Créer mon compte
                    </button>
                </div>
            </form>

            <p class="text-center mt-6 text-gray-600">
                Déjà inscrit?
                <a href="{{ route('biicf.login') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Connectez-vous
                </a>
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.21/js/intlTelInput.min.js"></script>
    <script src="{{ asset('js/inscription.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation du téléphone
            const phoneInput = document.querySelector("#phone");
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "CI",
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.21/js/utils.js"
            });

            // Remplissage du sélecteur de pays
            const countrySelect = document.querySelector("#address-country");
            const countryData = window.intlTelInputGlobals.getCountryData();

            countryData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.iso2;
                option.textContent = country.name;
                countrySelect.appendChild(option);
            });

            // Synchronisation du pays sélectionné
            countrySelect.value = iti.getSelectedCountryData().iso2;

            // Mise à jour des informations régionales
            async function updateRegionInfo(countryCode) {
                try {
                    const response = await fetch(`https://restcountries.com/v3.1/alpha/${countryCode}`);
                    const data = await response.json();
                    if (data && data[0]) {
                        document.getElementById('continent').value = data[0].region || '';
                        document.getElementById('subregion').value = data[0].subregion || '';
                    }
                } catch (error) {
                    console.error('Erreur lors de la récupération des informations:', error);
                }
            }

            // Événements de changement
            phoneInput.addEventListener('countrychange', () => {
                const countryData = iti.getSelectedCountryData();
                countrySelect.value = countryData.iso2;
                updateRegionInfo(countryData.iso2);
            });

            countrySelect.addEventListener('change', () => {
                iti.setCountry(countrySelect.value);
                updateRegionInfo(countrySelect.value);
            });

            // Validation du formulaire
            document.querySelector('form').addEventListener('submit', function(e) {
                if (!iti.isValidNumber()) {
                    e.preventDefault();
                    const errorMsg = document.querySelector("#phone").nextElementSibling;
                    errorMsg.textContent = "Numéro de téléphone invalide";
                    errorMsg.classList.remove('hidden');
                    return false;
                }

                e.preventDefault(); // Empêche l'envoi du formulaire pour test

                // Récupérer le numéro complet (avec indicatif)
                const fullPhoneNumber = iti.getNumber();

                // Ajouter le numéro complet au champ ou l'envoyer à la base de données
                phoneInput.value = fullPhoneNumber;

                // Envoyer le formulaire
                e.target.submit();

            });

            // Initialisation des données régionales
            updateRegionInfo(iti.getSelectedCountryData().iso2);
        });
    </script>
</body>

</html>
