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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom ou raison sociale *</label>
                            <input type="text" name="name"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                            <input type="text" name="last-name"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Nom d'utilisateur et Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'utilisateur *</label>
                            <input type="text" name="username"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe *</label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
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
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe
                                *</label>
                            <div class="relative">
                                <input type="password" name="repeat-password" id="repeat-password"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    required>
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
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Type d'acteur et Type d'investisseur -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type d'acteur *</label>
                            <select name="user_type"
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
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type d'investisseur *</label>
                            <select name="invest_type"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Bank/IFD">Bank/IFD</option>
                                <option value="Pgm Public/Para-Public">Pgm Public/Para-Public</option>
                                <option value="Fonds d'investissement">Fonds d'investissement</option>
                                <option value="Particulier">Particulier</option>
                            </select>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Proportion à investir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proportion à investir *</label>
                        <select name="investisement"
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
                        <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div id="step2" class="hidden space-y-6">
                    <!-- Téléphone et Pays -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone *</label>
                            <input type="tel" id="phone" name="phone"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pays *</label>
                            <select id="address-country" name="country"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">Sélectionnez un pays</option>
                            </select>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Continent et Sous-région -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Continent</label>
                            <input type="text" id="continent" name="continent"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sous-région</label>
                            <input type="text" id="subregion" name="sous_region"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50" readonly>
                        </div>
                    </div>

                    <!-- Département et Ville -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Département *</label>
                            <input type="text" name="departement"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                            <input type="text" name="ville"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>
                    </div>

                    <!-- Commune et Code de parrainage -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Commune *</label>
                            <input type="text" name="commune"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <span class="hidden text-red-500 text-sm mt-1 error-message"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Code de parrainage</label>
                            <input type="number" name="parrain"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Optionnel">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('signupForm');
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');
            const submitBtn = document.getElementById('submitBtn');
            let currentStep = 1;

            // Validation des champs
            function validateStep1() {
                const inputs = step1.querySelectorAll('input[required], select[required]');
                let isValid = true;

                inputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                        showError(input, 'Ce champ est requis');
                    } else {
                        hideError(input);
                    }
                });

                return isValid;
            }

            function showError(input, message) {
                const errorSpan = input.nextElementSibling;
                errorSpan.textContent = message;
                errorSpan.classList.remove('hidden');
                input.classList.add('border-red-500');
            }

            function hideError(input) {
                const errorSpan = input.nextElementSibling;
                errorSpan.classList.add('hidden');
                input.classList.remove('border-red-500');
            }

            // Navigation entre les étapes
            nextBtn.addEventListener('click', () => {
                if (currentStep === 1 && validateStep1()) {
                    step1.classList.add('hidden');
                    step2.classList.remove('hidden');
                    prevBtn.classList.remove('hidden');
                    nextBtn.classList.add('hidden');
                    submitBtn.classList.remove('hidden');
                    currentStep = 2;
                    updateProgress();
                }
            });

            prevBtn.addEventListener('click', () => {
                if (currentStep === 2) {
                    step2.classList.add('hidden');
                    step1.classList.remove('hidden');
                    prevBtn.classList.add('hidden');
                    nextBtn.classList.remove('hidden');
                    submitBtn.classList.add('hidden');
                    currentStep = 1;
                    updateProgress();
                }
            });

            function updateProgress() {
                const steps = document.querySelectorAll('.step-active');
                const lines = document.querySelectorAll('.step-line');

                steps.forEach((step, index) => {
                    if (index < currentStep) {
                        step.classList.add('bg-blue-600');
                        step.classList.remove('bg-gray-200');
                    } else {
                        step.classList.remove('bg-blue-600');
                        step.classList.add('bg-gray-200');
                    }
                });

                lines.forEach((line, index) => {
                    if (index < currentStep - 1) {
                        line.classList.add('bg-blue-600');
                        line.classList.remove('bg-gray-200');
                    } else {
                        line.classList.remove('bg-blue-600');
                        line.classList.add('bg-gray-200');
                    }
                });
            }
        });

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }

        // Validation en temps réel des mots de passe
        document.getElementById('repeat-password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const repeatPassword = this.value;
            const errorSpan = this.nextElementSibling.nextElementSibling;

            if (password !== repeatPassword) {
                errorSpan.textContent = 'Les mots de passe ne correspondent pas';
                errorSpan.classList.remove('hidden');
                this.classList.add('border-red-500');
            } else {
                errorSpan.classList.add('hidden');
                this.classList.remove('border-red-500');
            }
        });
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
