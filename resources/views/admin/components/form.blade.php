<form action="{{ $action }}" id="{{ $formId }}" class="{{ $formClass }}" style="display: none;" method="POST">
    @csrf
    @method($method)

    <h1 class="text-xl text-center mb-3">{{ $formTitle }}</h1>

    <div class="space-y-3 mb-3 w-full">
        <input type="number" id="quantityInput" name="quantité"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
            placeholder="Quantité" data-min="{{ $qteProdMin }}" data-max="{{ $qteProdMax }}"
            oninput="updatemontantTotal()" required>
    </div>

    <div class="space-y-3 mb-3 w-full">
        <input type="text" id="specificite" name="specificite"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
            placeholder="Specificité" required>
    </div>

    <div class="space-y-3 mb-3 w-full">
        <input type="text" id="locationInput" name="localite"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
            placeholder="Localité" required>
    </div>

    <input type="text" name="userTrader" value="{{ $userTrader }}">
    <input type="text" name="nameProd" value="{{ $nameProd }}">
    <input type="text" name="userSender" value="{{ $userSender }}">
    <input type="text" name="photoProd" value="{{ $photoProd }}">
    <input type="text" name="idProd" value="{{ $idProd }}">

    <div class="flex justify-between px-4 mb-3 w-full">
        <p class="font-semibold text-sm text-gray-500">Prix total:</p>
        <p class="text-sm text-purple-600" id="montantTotal">0 FCFA</p>
        <input type="text" name="montantTotal" id="montant_total_input" >
    </div>

    <p id="errorMessage" class="text-sm text-center text-red-500 hidden">Erreur</p>

    <div class="w-full text-center mt-3">
        <button type="reset"
            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-200 text-black hover:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none">
            Annulé
        </button>
        <button type="submit" id="submitButton"
            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
            disabled>Envoyé</button>
    </div>
</form>
