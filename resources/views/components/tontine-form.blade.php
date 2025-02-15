{{-- resources/views/components/tontine-form.blade.php --}}
@props(['serverTime', 'errors' => []])

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100"
        data-server-time="{{ $serverTime }}">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 p-8">
            <h2 class="text-4xl font-bold text-center tracking-tight text-white">Nouvelle Tontine</h2>
            <p class="mt-2 text-center text-lg text-white">Créez votre épargne collaborative en quelques clics</p>
        </div>

        <form wire:submit.prevent="initiateTontine" class="p-8 space-y-8">
            {{-- Montant --}}
            <div class="space-y-2">
                <label for="amount" class="text-base font-medium text-gray-900 flex items-center gap-2">
                    <x-icons.currency class="w-5 h-5 text-indigo-500" />
                    Montant de cotisation
                </label>
                <div class="relative mt-1">
                    <input type="number" id="amount" wire:model.defer="amount"
                        class="block w-full pl-12 pr-4 py-4 text-lg border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-shadow duration-200 shadow-sm hover:shadow-md"
                        placeholder="Montant en FCFA" required>
                </div>
                @if ($errors['amount'])
                    <span class="text-sm text-red-500">{{ $errors['amount'] }}</span>
                @endif
            </div>

            {{-- Fréquence --}}
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-3 block">Fréquence de cotisation</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach (['quotidienne' => 'Quotidienne', 'hebdomadaire' => 'Hebdomadaire', 'mensuelle' => 'Mensuelle'] as $value => $label)
                        <label class="relative">
                            <input type="radio" name="frequency" wire:model.defer="frequency"
                                value="{{ $value }}" class="peer sr-only">
                            <div
                                class="w-full text-center p-3 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200
                                peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white peer-checked:shadow-md
                                hover:border-purple-300 hover:shadow-sm text-gray-700 bg-white">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>

                @if ($errors['frequency'])
                    <span class="text-sm text-red-500">{{ $errors['amount'] }}</span>
                @endif
            </div>

            {{-- Durée --}}
            <div class="space-y-2">
                <label class="text-base font-medium text-gray-900 flex items-center gap-2">
                    <x-icons.calendar class="w-5 h-5 text-indigo-500" />
                    <span id="durationLabel">Durée</span>
                </label>
                <div class="relative mt-1">
                    <input type="number" id="duration" wire:model.defer="duration"
                        class="block w-full px-4 py-4 text-lg border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-shadow duration-200 shadow-sm hover:shadow-md"
                        placeholder="Entrez la durée" required>
                </div>

                @if ($errors['duration'])
                    <span class="text-sm text-red-500">{{ $errors['amount'] }}</span>
                @endif
            </div>

            {{-- Gain Potentiel --}}
            <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-100">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <x-icons.currency class="w-5 h-5 text-indigo-500" />
                        <h3 class="text-lg font-semibold text-indigo-900">Gain Potentiel</h3>
                    </div>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-indigo-600">Montant total</p>
                            <p class="text-2xl font-bold text-indigo-700" id="potentialGain"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-indigo-600">Frais de service</p>
                            <p class="text-lg font-semibold text-indigo-700" id="fraisDeSevice">-</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-indigo-600">Date de fin</p>
                            <p class="text-lg font-semibold text-indigo-700" id="endDateDisplay">-</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Information Box --}}
            <div class="bg-purple-50 border border-purple-100 rounded-xl p-4">
                <div class="flex items-start">
                    <x-icons.information class="h-5 w-5 text-purple-400 flex-shrink-0" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-purple-800">Information importante</h3>
                        <div class="mt-2 text-sm text-purple-700 bg-purple-100 p-4 rounded-lg shadow-md">
                            <ul class="list-disc list-inside space-y-2">
                                <li><strong>Les frais de gestion</strong> seront prélevés à la fin de la tontine.</li>
                                <li>Vous devez disposer des fonds nécessaires sur votre compte COC pour commencer. <span
                                        class="font-semibold">Le montant de la première cotisation sera immédiatement
                                        gelé.</span> </li>
                                <li>Les paiements suivants seront automatiquement ajoutés au <span
                                        class="font-semibold">CEDD</span>.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <x-offre.alert-messages />

            {{-- Submit Button --}}
            <button wire:loading.attr="disabled" type="submit"
                class="w-full py-4 px-6 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-lg font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition-all duration-300 ease-in-out hover:-translate-y-1">
                <span wire:loading.remove>Lancer la Tontine</span>
                <span wire:loading.remove class="ml-2">→</span>
                <x-icons.spinner wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
                <span wire:loading>Traitement...</span>
            </button>
        </form>

        <script src="{{ asset('js/tontine.js') }}"></script>
    </div>
</div>
