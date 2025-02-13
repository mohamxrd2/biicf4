<div class="min-h-screen p-8">
    @if ($tontineStart)
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <!-- Header avec un design plus moderne -->
                <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 p-8">
                    <h2 class="text-4xl font-bold  text-center tracking-tight text-white">Nouvelle Tontine</h2>
                    <p class=" mt-2 text-center text-lg text-white">Créez votre épargne collaborative en quelques clics
                    </p>
                </div>

                <form wire:submit.prevent="initiateTontine" class="p-8 space-y-8">
                    <!-- Montant avec design amélioré -->
                    <div class="space-y-2">
                        <label for="amount" class="text-base font-medium text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Montant de cotisation
                        </label>
                        <div class="relative mt-1">
                            <input type="number" id="amount" wire:model.debounce.500ms="amount"
                                class="block w-full pl-12 pr-4 py-4 text-lg border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-shadow duration-200 shadow-sm hover:shadow-md"
                                placeholder="Montant en FCFA" required>
                        </div>
                        @error('amount')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fréquence avec badges -->
                    <div>
                        <label class="text-sm font-semibold text-gray-700 mb-3 block">Fréquence de cotisation</label>
                        <div class="grid grid-cols-3 gap-3">
                            <!-- Option Quotidienne -->
                            <label class="relative">
                                <input type="radio" name="frequency" wire:model.defer="frequency" value="quotidienne"
                                    class="peer sr-only">
                                <div
                                    class="w-full text-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200
                                    peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white peer-checked:shadow-md
                                    hover:border-purple-300 hover:shadow-sm text-gray-700 bg-white">
                                    Quotidienne
                                </div>
                            </label>

                            <!-- Option Hebdomadaire -->
                            <label class="relative">
                                <input type="radio" name="frequency" wire:model.defer="frequency" value="hebdomadaire"
                                    class="peer sr-only">
                                <div
                                    class="w-full text-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200
                                    peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white peer-checked:shadow-md
                                    hover:border-purple-300 hover:shadow-sm text-gray-700 bg-white">
                                    Hebdomadaire
                                </div>
                            </label>

                            <!-- Option Mensuelle -->
                            <label class="relative">
                                <input type="radio" name="frequency" wire:model.defer="frequency" value="mensuelle"
                                    class="peer sr-only">
                                <div
                                    class="w-full text-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200
                                    peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white peer-checked:shadow-md
                                    hover:border-purple-300 hover:shadow-sm text-gray-700 bg-white">
                                    Mensuelle
                                </div>
                            </label>
                        </div>

                        @error('frequency')
                            <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date de fin avec calendrier moderne -->
                    <div class="space-y-2">
                        <label class="text-base font-medium text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span id="durationLabel">
                                <!-- Le label changera dynamiquement -->
                                Durée
                            </span>
                        </label>
                        <div class="relative mt-1">
                            <input type="number" id="duration"
                                class="block w-full px-4 py-4 text-lg border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-shadow duration-200 shadow-sm hover:shadow-md"
                                placeholder="Entrez la durée" required>
                        </div>
                    </div>

                    <!-- Section Gain Potentiel -->
                    <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100">
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-indigo-900">Gain Potentiel</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-indigo-600">Montant total</p>
                                    <p class="text-2xl font-bold text-indigo-700" id="potentialGain">0 FCFA</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-indigo-600">Date de fin</p>
                                    <p class="text-lg font-semibold text-indigo-700" id="endDateDisplay">-</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Information Box -->
                    <div class="bg-purple-50 border border-purple-100 rounded-xl p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-purple-800">Information importante</h3>
                                <div class="mt-2 text-sm text-purple-700">
                                    <p>Le premier paiement couvre les frais de gestion.</p>
                                    <p>Les paiements suivants seront automatiquement ajoutés au CEDD.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-4 px-6 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-lg font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition-all duration-300 ease-in-out hover:-translate-y-1">
                        Lancer la Tontine
                        <span class="ml-2">→</span>
                    </button>
                </form>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const amountInput = document.getElementById("amount");
                        const durationInput = document.getElementById("duration");
                        const durationLabel = document.getElementById("durationLabel");
                        const potentialGainDisplay = document.getElementById("potentialGain");
                        const endDateDisplay = document.getElementById("endDateDisplay");
                        const amountPerContributionDisplay = document.getElementById("amountPerContribution");

                        const FREQUENCY_DAYS = {
                            quotidienne: 1,
                            hebdomadaire: 7,
                            mensuelle: 30
                        };

                        const DURATION_LABELS = {
                            quotidienne: "Nombre de jours",
                            hebdomadaire: "Nombre de semaines",
                            mensuelle: "Nombre de mois"
                        };

                        const DURATION_PLACEHOLDERS = {
                            quotidienne: "Entrez le nombre de jours",
                            hebdomadaire: "Entrez le nombre de semaines",
                            mensuelle: "Entrez le nombre de mois"
                        };

                        function formatDate(date) {
                            const options = {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            };
                            return date.toLocaleDateString('fr-FR', options);
                        }

                        function calculateEndDate(duration, frequency) {
                            const today = new Date();
                            const durationInDays = duration * FREQUENCY_DAYS[frequency];
                            const endDate = new Date(today);
                            endDate.setDate(today.getDate() + durationInDays);
                            return endDate;
                        }

                        function calculatePotentialGain() {
                            const amount = parseFloat(amountInput.value) || 0;
                            const duration = parseInt(durationInput.value) || 0;
                            const frequency = document.querySelector('input[name="frequency"]:checked')?.value;

                            if (!amount || !duration || !frequency) {
                                potentialGainDisplay.textContent = "0 FCFA";
                                endDateDisplay.textContent = "-";
                                amountPerContributionDisplay.textContent = "0 FCFA";
                                return;
                            }

                            // Calculer la date de fin
                            const endDate = calculateEndDate(duration, frequency);

                            // Calculer le gain potentiel
                            const potentialGain = duration * amount;

                            // Mettre à jour l'affichage
                            potentialGainDisplay.textContent = new Intl.NumberFormat('fr-FR').format(potentialGain) + " FCFA";
                            endDateDisplay.textContent = formatDate(endDate);
                            amountPerContributionDisplay.textContent = new Intl.NumberFormat('fr-FR').format(amount) + " FCFA";
                        }

                        function updateDurationLabel() {
                            const frequency = document.querySelector('input[name="frequency"]:checked')?.value;
                            if (frequency) {
                                durationLabel.textContent = DURATION_LABELS[frequency];
                                durationInput.placeholder = DURATION_PLACEHOLDERS[frequency];
                            }
                        }

                        // Event listeners
                        amountInput.addEventListener("input", calculatePotentialGain);
                        durationInput.addEventListener("input", calculatePotentialGain);

                        document.querySelectorAll('input[name="frequency"]').forEach((input) => {
                            input.addEventListener("change", () => {
                                updateDurationLabel();
                                calculatePotentialGain();
                            });
                        });

                        // Initial setup
                        updateDurationLabel();
                        calculatePotentialGain();
                    });
                </script>

            </div>
        </div>
    @else
        <!-- Affichage de la tontine active -->
        <div class="max-w-3xl mx-auto ">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Tontines en cours</h2>
            <div
                class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-bold text-gray-900">Tontine #2989</h3>
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Active</span>
                            </div>
                            <div class="mt-2 space-y-1">
                                <p class="text-gray-600">Montant: <span class="font-semibold">278,000 FCFA</span></p>
                                <p class="text-gray-600">Fréquence: <span class="font-semibold">Hebdomadaire</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Date de début</p>
                            <p class="font-bold text-gray-900">12 Mai 2024</p>
                            <p class="text-sm text-gray-500 mt-2">Date de fin</p>
                            <p class="font-bold text-gray-900">12 Mai 2025</p>
                        </div>
                    </div>

                    <!-- Barre de progression -->
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Progression</span>
                            <span class="font-medium text-indigo-600">65%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full relative group-hover:shadow-lg transition-all duration-300"
                                style="width: 65%">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent
                                    transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500">Cotisations effectuées</p>
                            <p class="text-lg font-bold text-gray-900">15/24</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Montant collecté</p>
                            <p class="text-lg font-bold text-indigo-600">180,000 FCFA</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Prochain paiement</p>
                            <p class="text-lg font-bold text-gray-900">19 Fév 2025</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end mt-6 gap-3">
                        <button
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center gap-2 group">
                            <svg class="w-4 h-4 text-gray-500 group-hover:text-gray-700" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Détails
                        </button>
                        <button
                            class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-2 group">
                            <svg class="w-4 h-4 text-indigo-500 group-hover:text-indigo-700" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Effectuer un paiement
                        </button>
                    </div>
                </div>
            </div>

        </div>
    @endif

    <!-- Historique des tontines -->
    <div class="max-w-3xl mx-auto mt-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Historique des tontines</h2>
            <div class="flex gap-2">
                <select
                    class="px-4 py-2 border border-gray-200 rounded-lg text-gray-600 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all">Toutes les tontines</option>
                    <option value="active">Tontines actives</option>
                    <option value="completed">Tontines terminées</option>
                </select>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Carte de tontine -->
            <div
                class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('detail-tontine') }}"
                                    class="text-xl font-bold text-gray-900">Tontine #2989</a>
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Active</span>
                            </div>
                            <div class="mt-2 space-y-1">
                                <p class="text-gray-600">Montant: <span class="font-semibold">278,000 FCFA</span></p>
                                <p class="text-gray-600">Fréquence: <span class="font-semibold">Hebdomadaire</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Date de début</p>
                            <p class="font-bold text-gray-900">12 Mai 2024</p>
                            <p class="text-sm text-gray-500 mt-2">Date de fin</p>
                            <p class="font-bold text-gray-900">12 Mai 2025</p>
                        </div>
                    </div>

                    <!-- Barre de progression -->
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Progression</span>
                            <span class="font-medium text-indigo-600">65%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full relative group-hover:shadow-lg transition-all duration-300"
                                style="width: 65%">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent
                                transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500">Cotisations effectuées</p>
                            <p class="text-lg font-bold text-gray-900">15/24</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Montant collecté</p>
                            <p class="text-lg font-bold text-indigo-600">180,000 FCFA</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Prochain paiement</p>
                            <p class="text-lg font-bold text-gray-900">19 Fév 2025</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end mt-6 gap-3">
                        <button
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center gap-2 group">
                            <svg class="w-4 h-4 text-gray-500 group-hover:text-gray-700" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Détails
                        </button>
                        <button
                            class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-2 group">
                            <svg class="w-4 h-4 text-indigo-500 group-hover:text-indigo-700" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Effectuer un paiement
                        </button>
                    </div>
                </div>
            </div>

            <!-- Carte de tontine (Terminée) -->
            <div
                class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 group opacity-75">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('detail-tontine') }}"
                                    class="text-xl font-bold text-gray-900">Tontine #2988</a>
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">Terminée</span>
                            </div>
                            <div class="mt-2 space-y-1">
                                <p class="text-gray-600">Montant: <span class="font-semibold">150,000 FCFA</span></p>
                                <p class="text-gray-600">Fréquence: <span class="font-semibold">Mensuelle</span></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Date de début</p>
                            <p class="font-bold text-gray-900">1 Jan 2024</p>
                            <p class="text-sm text-gray-500 mt-2">Date de fin</p>
                            <p class="font-bold text-gray-900">1 Avr 2024</p>
                        </div>
                    </div>

                    <!-- Barre de progression -->
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Progression</span>
                            <span class="font-medium text-green-600">100%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-green-500 to-green-400 rounded-full"
                                style="width: 100%"></div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500">Cotisations effectuées</p>
                            <p class="text-lg font-bold text-gray-900">3/3</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Montant total collecté</p>
                            <p class="text-lg font-bold text-green-600">150,000 FCFA</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">État</p>
                            <p class="text-lg font-bold text-green-600">Complétée</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end mt-6">
                        <button
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center gap-2 group">
                            <svg class="w-4 h-4 text-gray-500 group-hover:text-gray-700" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Voir le récapitulatif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
