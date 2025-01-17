<div class="p-6 bg-gray-100 border rounded-lg shadow-sm max-w-md">
    <h2 class="text-lg font-bold mb-4">{{ $title }}</h2>

    <!-- Date de retrait -->
    <div class="mb-4">
        <label for="{{ $dateId }}" class="block text-sm font-medium text-gray-700 mb-2">{{ $dateLabel }}</label>
        <div class="relative">
            <input type="date" id="{{ $dateId }}" name="{{ $dateModel }}" wire:model="{{ $dateModel }}"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>
    <label class="block text-lg font-semibold text-gray-800 mb-4">Choisir soit Heure de début soit Période *</label>
    <!-- Heure de retrait -->
    <div class="mb-4">
        <label for="{{ $timeId }}" class="block text-sm font-medium text-gray-700 mb-2">Heure de retrait</label>
        <div class="relative">
            <input type="time" id="{{ $timeId }}" name="{{ $timeModel }}" wire:model="{{ $timeModel }}"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>

    <!-- Période -->
    <div>
        <label for="{{ $periodId }}" class="block text-sm font-medium text-gray-700 mb-2">Période de retrait</label>
        <select id="{{ $periodId }}" name="{{ $periodModel }}" wire:model="{{ $periodModel }}"
            class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none">
            <option value="" selected>Choisir la période</option>
            <option value="Matin">Matin</option>
            <option value="Après-midi">Après-midi</option>
            <option value="Soir">Soir</option>
            <option value="Nuit">Nuit</option>
        </select>
    </div>
</div>
