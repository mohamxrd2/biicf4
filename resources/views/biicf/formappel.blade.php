@extends('biicf.layout.navside')

@section('title', 'Formulaire de l\'Appel d\'offre')

@section('content')
    <div class="px-4">
        @if (session('success'))
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded-md mb-4">
                {{ session('error') }}
            </div>
        @endif
        <form action="{{ route('biicf.formstore') }}" method="POST" enctype="multipart/form-data" id="mainForm"
            x-data="{ isSubmitting: false }" x-on:submit="isSubmitting = true">
            @csrf
            <div class="w-full flex flex-col justify-center items-center">
                <h1 class="font-medium text-xl text-slate-700 mb-4">Remplissez le formulaire</h1>
                <input type="hidden" name="lowestPricedProduct" value="{{ $lowestPricedProduct }}">
                <input type="text" name="appliedZoneValue" value="{{ $appliedZoneValue }}">
                @foreach ($prodUsers as $userId)
                    <input type="hidden" name="prodUsers[]" value="{{ $userId }}">
                @endforeach
                <div class="lg:w-2/3 w-full bg-white rounded-lg p-2 shadow-md mb-4">
                    <h1 class="text-md font-medium text-slate-900">Prix unitaire max</h1>
                    <p class="text-sm">{{ $lowestPricedProduct }} FCFA</p>
                </div>
                <div class="lg:w-2/3 w-full space-y-3 mb-3">
                    <input type="text"
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Nom du produit" value="{{ $name }}" name="productName" required readonly>
                </div>
                <div class="lg:w-2/3 w-full space-y-3 mb-3">
                    <input type="number" required
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Quantité" name="quantity">
                </div>
                <div class="lg:w-2/3 w-full space-y-3 mb-3">
                    <select name="payment" required
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        <option value="" selected disabled>Payment</option>
                        <option value="Payment comptant">Payment comptant</option>
                        <option value="Avance partielle" disabled>Avance partielle</option>
                        <option value="A credit" disabled>A credit</option>
                        <option value="Vente a terme" disabled>Vente a terme</option>
                        <option value="Quotidiennement / garantie de prêt" disabled>Quotidiennement / garantie de prêt
                        </option>
                    </select>
                </div>
                <div class="lg:w-2/3 w-full space-y-3 mb-3">
                    <select name="Livraison" required
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        <option value="" selected disabled>Type de livraison</option>
                        <option value="moto">Achat avec livreur</option>
                        <option value="voiture">Take Away</option>
                        <option value="voiture">Reservation</option>
                    </select>
                </div>
                <div date-rangepicker class="overflow-auto flex items-center lg:w-2/3 w-full mb-3">
                    <div class="w-1/2 mr-2 relative">
                        <label for="datePickerStart">Au plus tôt</label>
                        <input type="date" id="datePickerStart" name="dateTot" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                            placeholder="Sélectionner la date de début">
                    </div>
                    <span class="mx-4 text-gray-500 items-center h-full justify-center flex">à</span>
                    <div class="w-1/2 ml-2 relative">
                        <label for="datePickerEnd" class="mb-1">Au plus tard</label>
                        <input type="date" id="datePickerEnd" name="dateTard" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                            placeholder="Sélectionner la date de fin">
                    </div>
                </div>
                <div class="lg:w-2/3 w-full space-y-3 mb-3">
                    <input type="text" required
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                        placeholder="Lieu de livraison" name="localite">
                </div>
                <div class="lg:w-2/3 w-full space-y-3 mb-3">
                    <select name="specification" required
                        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        <option value="" selected disabled>choisir une specification</option>

                        @if (!empty($distinctSpecifications))
                            <option value="{{ implode(', ', $distinctSpecifications) }}">
                                {{ implode(', ', $distinctSpecifications) }}</option>
                        @endif

                    </select>


                </div>
                <div class="lg:w-2/3 flex justify-between items-center w-full space-y-3 mb-6">
                    <h3>Ajouter un document (facultatif)</h3>
                    <div class="flex items-center justify-center w-20 z-10" id="floating_photo1">
                        <div class="overflow-hidden rounded-md relative w-full">
                            <label for="file-upload1"
                                class="flex flex-col items-center justify-center w-full h-30 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
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
                            <input id="file-upload1" class="hidden rounded-md" type="file" onchange="previewImage(this)"
                                name="image">
                            <img id="image-preview1" class="absolute inset-0 w-full h-full object-cover hidden">
                            <button type="button" onclick="removeImage()" id="remove-button1"
                                class="text-red-600 bg-white w-5 h-5 rounded-full absolute top-2 right-2 hidden">
                                <svg class="w-full" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lg:w-2/3 w-full flex flex-col items-center space-y-4">
                    <!-- Cancel Button -->
                    <button type="reset"
                        class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition duration-200 ease-in-out">
                        Annuler
                    </button>

                    <!-- First Action Section -->
                    <div class="w-full flex flex-col items-center space-y-2">
                        <p class="text-center text-gray-600">Pour vous envoyer directement aux fournisseurs de votre zone,
                            cliquez ici :</p>
                        <button type="submit" id="submitEnvoie"
                            class="w-full lg:w-auto px-4 py-2 rounded-md bg-purple-700 text-white hover:bg-purple-800 transition duration-200 ease-in-out flex items-center justify-center"
                            x-bind:disabled="isSubmitting" x-text="isSubmitting ? 'Envoi...' : 'Envoyer'">
                        </button>
                    </div>

                    <!-- Second Action Section -->
                    <div class="w-full flex flex-col items-center space-y-2">
                        <p class="text-center text-gray-600">Pour vous grouper avec d'autres acheteurs de votre zone,
                            cliquez ici :</p>
                        <button type="submit" id="f"
                            class="w-full lg:w-auto px-4 py-2 rounded-md bg-green-500 text-white hover:bg-green-600 transition duration-200 ease-in-out flex items-center justify-center"
                            x-bind:disabled="isSubmitting" x-text="isSubmitting ? 'Envoi...' : 'Groupé'">
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
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
            document.getElementById('mainForm').action = "{{ route('biicf.formstore') }}";
        });

        document.getElementById('submitGroupe').addEventListener('click', function() {
            document.getElementById('mainForm').action = "{{ route('biicf.formstoreGroupe') }}";
        });
    </script>
@endsection
