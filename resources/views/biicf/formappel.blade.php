@extends('biicf.layout.navside')

@section('title', 'Formulaire de l\'Appel d\'offre')

@section('content')
    <div class="px-4">
        @if (session('success'))
            <div class="px-4 py-2 mb-4 text-green-800 bg-green-200 rounded-md">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="px-4 py-2 mb-4 text-red-800 bg-red-200 rounded-md">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('biicf.formstoreGroupe') }}" method="POST" enctype="multipart/form-data" id="mainForm"
            x-data="{ isSubmitting: false }" x-on:submit="isSubmitting = true">
            @csrf
            <div class="flex flex-col items-center justify-center w-full">
                <h1 class="mb-4 text-xl font-medium text-slate-700">Remplissez le formulaire</h1>
                <input type="hidden" name="lowestPricedProduct" value="{{ $lowestPricedProduct }}">
                <input type="hidden" name="reference" value="{{ $reference }}">
                <input type="hidden" name="appliedZoneValue" value="{{ $appliedZoneValue }}">
                <input type="hidden" name="type" value="{{ $type }}">
                @foreach ($prodUsers as $userId)
                    <input type="hidden" name="prodUsers[]" value="{{ $userId }}">
                @endforeach
                <div class="w-full p-2 mb-4 bg-white rounded-lg shadow-md lg:w-2/3">
                    <h1 class="font-medium text-md text-slate-900">Prix unitaire max</h1>
                    <p class="text-sm">{{ $lowestPricedProduct }} FCFA</p>
                </div>
                <div class="w-full mb-3 space-y-3 lg:w-2/3">
                    <input type="text"
                        class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Nom du produit" value="{{ $name }}" name="productName" required readonly>
                </div>
                <div class="w-full mb-3 space-y-3 lg:w-2/3">
                    <input type="number" required
                        class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Quantité" name="quantity">
                </div>
                <div class="w-full mb-3 space-y-3 lg:w-2/3">
                    <select name="payment" required
                        class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        <option value="" selected disabled>Payment</option>
                        <option value="Payment comptant">Payment comptant</option>
                        <option value="Avance partielle" disabled>Avance partielle</option>
                        <option value="A credit" disabled>A credit</option>
                        <option value="Vente a terme" disabled>Vente a terme</option>
                        <option value="Quotidiennement / garantie de prêt" disabled>Quotidiennement / garantie de prêt
                        </option>
                    </select>
                </div>
                <div class="w-full mb-3 space-y-3 lg:w-2/3">
                    @if ($type == 'Service')
                        <select id="livraisonSelect" name="Livraison" required
                            class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                            <option value="" selected disabled>Type de livraison</option>
                            <option value="Take Away">Take Away</option>
                            {{-- <option value="Reservation">Reservation</option> --}}
                        </select>
                    @else
                        <select id="livraisonSelect" name="Livraison" required
                            class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                            <option value="" selected disabled>Type de livraison</option>
                            <option value="Achat avec livreur">Achat avec livreur</option>
                            <option value="Take Away">Take Away</option>
                            {{-- <option value="Reservation">Reservation</option> --}}
                        </select>
                    @endif
                </div>
                <div date-rangepicker class="flex items-center w-full mb-3 overflow-auto lg:w-2/3">
                    <div class="relative w-1/2 mr-2">
                        <label for="datePickerStart">Au plus tôt</label>
                        <input type="date" id="datePickerStart" name="dateTot" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                            placeholder="Sélectionner la date de début">
                    </div>
                    <span class="flex items-center justify-center h-full mx-4 text-gray-500">à</span>
                    <div class="relative w-1/2 ml-2">
                        <label for="datePickerEnd" class="mb-1">Au plus tard</label>
                        <input type="date" id="datePickerEnd" name="dateTard" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                            placeholder="Sélectionner la date de fin">
                    </div>
                </div>

                <div id="timeFields" class="flex items-center w-full mb-3 overflow-auto lg:w-2/3" style="display: none;">
                    <!-- Heure de début -->
                    <div class="relative w-1/2 mr-2">
                        <label for="timePickerStart" class="block text-sm font-medium text-gray-700">Heure de début</label>
                        <input type="time" id="timePickerStart" name="timeStart"
                            class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- Heure de fin -->
                    <div class="relative w-1/2 mr-2">
                        <label for="timePickerEnd" class="block text-sm font-medium text-gray-700">Heure de fin</label>
                        <input type="time" id="timePickerEnd" name="timeEnd"
                            class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div> <br>
                    <p class="text-center">OU</p>
                </div>

                <!-- Sélecteur de période de la journée -->
                <div class="flex items-center w-full mb-3 overflow-auto lg:w-2/3">

                    <select id="dayPeriod" name="dayPeriod"
                        class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg pe-9 disabled:opacity-50 disabled:pointer-events-none">
                        <option value="" selected>Choisir la période de la journée</option>
                        <option value="Matin">Matin</option>
                        <option value="Après-midi">Après-midi</option>
                        <option value="Soir">Soir</option>
                        <option value="Nuit">Nuit</option>
                    </select>
                </div>
                <div class="w-full mb-3 space-y-3 lg:w-2/3">
                    <input type="text" required
                        class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Lieu de livraison" name="localite">
                </div>
                <div class="w-full mb-3 space-y-3 lg:w-2/3">
                    @if ($type == 'Service')
                    @else
                        <select name="specification" required
                            class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                            <option value="" selected disabled>choisir une specification</option>

                            @if (!empty($distinctSpecifications))
                                <option value="{{ implode(', ', $distinctSpecifications) }}">
                                    {{ implode(', ', $distinctSpecifications) }}</option>
                            @endif

                        </select>
                    @endif


                </div>
                <div class="flex items-center justify-between w-full mb-6 space-y-3 lg:w-2/3">
                    <h3>Ajouter un document (facultatif)</h3>
                    <div class="z-10 flex items-center justify-center w-20" id="floating_photo1">
                        <div class="relative w-full overflow-hidden rounded-md">
                            <label for="file-upload1"
                                class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer h-30 bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold">document</span>
                                    </p>
                                </div>
                            </label>
                            <input id="file-upload1" class="hidden rounded-md" type="file"
                                onchange="previewImage(this)" name="image">
                            <img id="image-preview1" class="absolute inset-0 hidden object-cover w-full h-full">
                            <button type="button" onclick="removeImage()" id="remove-button1"
                                class="absolute hidden w-5 h-5 text-red-600 bg-white rounded-full top-2 right-2">
                                <svg class="w-full" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center w-full space-y-4 lg:w-2/3">
                    <!-- Cancel Button -->
                    <button type="reset"
                        class="px-4 py-2 text-gray-700 transition duration-200 ease-in-out bg-gray-200 rounded-md hover:bg-gray-300">
                        Annuler
                    </button>

                    <!-- First Direct Section -->
                    <div class="flex flex-col items-center w-full space-y-2">
                        <p class="text-center text-gray-600">Pour vous envoyer directement aux fournisseurs de votre zone,
                            cliquez ici :</p>
                        <button type="submit" id="submitEnvoie"
                            class="flex items-center justify-center w-full px-4 py-2 text-white transition duration-200 ease-in-out bg-purple-700 rounded-md lg:w-auto hover:bg-purple-800"
                            x-bind:disabled="isSubmitting" x-text="isSubmitting ? 'Envoi...' : 'Direct'">
                        </button>
                    </div>

                    @if ($type == 'Service')
                        <p class="text-center text-gray-600">
                            Le type est un service il n'est pas possible de grouper avec les clients , Passez a offre direct
                        </p>
                    @elseif ($appliedZoneValue)
                        <!-- Second Grouper Section -->
                        <div class="flex flex-col items-center w-full space-y-2">
                            <p class="text-center text-gray-600">
                                Pour vous grouper avec d'autres acheteurs de votre zone, cliquez ici :
                            </p>
                            <button type="submit" id="submitGroupe"
                                class="flex items-center justify-center w-full px-4 py-2 text-white transition duration-200 ease-in-out bg-green-500 rounded-md lg:w-auto hover:bg-green-600"
                                x-bind:disabled="isSubmitting" x-text="isSubmitting ? 'Envoi...' : 'Groupé'">
                            </button>
                        </div>
                    @else
                        <!-- If $appliedZoneValue is not true, display this -->
                        <p class="text-center text-red-600">
                            Veuillez sélectionner une zone économique pour pouvoir vous grouper avec d'autres acheteurs.
                        </p>
                    @endif


                </div>

            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const livraisonSelect = document.getElementById('livraisonSelect');
            const dayPeriodSelect = document.getElementById('dayPeriod');
            const timeFields = document.getElementById('timeFields');


            function toggleDayPeriod() {
                if (livraisonSelect.value === 'Take Away') {
                    dayPeriodSelect.parentElement.style.display = 'flex';
                    timeFields.style.display = 'flex'; // Affiche les champs de temps

                } else {
                    dayPeriodSelect.parentElement.style.display = 'none';
                    timeFields.style.display = 'none'; // Cache les champs de temps

                }
            }

            // Initial check
            toggleDayPeriod();

            // Add event listener to handle changes
            livraisonSelect.addEventListener('change', toggleDayPeriod);
        });

        document.addEventListener("DOMContentLoaded", function() {
            const datePickerStart = document.getElementById("datePickerStart");
            const datePickerEnd = document.getElementById("datePickerEnd");

            // Calculer la date actuelle + 2 jours
            const today = new Date();
            today.setDate(today.getDate() + 2);
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');

            const minDate = `${yyyy}-${mm}-${dd}`;

            // Définir l'attribut min pour les deux sélecteurs de date
            datePickerStart.setAttribute("min", minDate);
            datePickerEnd.setAttribute("min", minDate);
        });

        function previewImage(input) {
            const preview = document.getElementById('image-preview1');
            const removeButton = document.getElementById('remove-button1');
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                removeButton.classList.remove('hidden');
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
                removeButton.classList.add('hidden');
            }
        }

        function removeImage() {
            const preview = document.getElementById('image-preview1');
            const removeButton = document.getElementById('remove-button1');
            const fileInput = document.getElementById('file-upload1');

            preview.src = '';
            preview.classList.add('hidden');
            removeButton.classList.add('hidden');
            fileInput.value = ''; // Clear the file input
        }

        document.getElementById('submitEnvoie').addEventListener('click', function() {
            const form = document.getElementById('mainForm');
            form.action = "{{ route('biicf.formstore') }}";
            form.submit();
        });

        document.getElementById('submitGroupe').addEventListener('click', function() {
            const form = document.getElementById('mainForm');
            form.action = "{{ route('biicf.formstoreGroupe') }}";
            form.submit();
        });
    </script>


@endsection
