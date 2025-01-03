<div>
    <label for="zone_economique" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Zone économique
    </label>
    <div class="relative">
        <select wire:model.live="zoneEconomique" id="zone_economique" required
            class="block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors">
            <option value="proximite">Proximité</option>
            <option value="locale">Locale</option>
            <option value="departementale">Départementale</option>
            <option value="nationale">Nationale</option>
            <option value="sous_regionale">Sous-régionale</option>
            <option value="continentale">Continentale</option>
        </select>
    </div>
</div>
