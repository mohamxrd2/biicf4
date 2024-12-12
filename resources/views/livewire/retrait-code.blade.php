<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
    {{-- Stop trying to control. --}}
    <div class="space-y-6">
        <div class="border-b pb-4">
            <h2 class="text-2xl font-semibold text-gray-800">
                Code de confirmation de retrait
            </h2>
            <p class="text-gray-600 mt-2">
                {{ $demandeur->name }}  à fait une demande de retrait
            </p>
        </div>

        <div class="flex p-3 bg-gray-50 rounded-xl">
            <p class="text-gray-600 font-medium">Montant du retrait : </p>
            <p class="text-gray-800 font-bold ml-2">{{ $amount }} CFA</p>

        </div>

        <div class="text-center p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Code de retrait</h3>
            <p class="text-4xl font-bold text-blue-700">{{ $codeRetrait }} </p>
        </div>

    </div>
</div>
