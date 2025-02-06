<div>
    <h1 class="mb-8 text-xl font-bold text-center">Ajout du Service</h1>

    <div class="mb-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-700">Informations Générales</h2>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Spécialité</label>
                <input type="text" wire:model='specialite' :disabled="locked"
                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Ex : Coiffure, Réparation...">
                @error('specialite')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Expérience dans le domaine<span
                        class="text-red-500">*</span></label>
                <select wire:model='qualification' :disabled="locked"
                    class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="" disabled selected>Choisissez une option</option>
                    <option value="Moins de 1 an">Moins de 1 an</option>
                    <option value="De 1 à 5 ans">De 1 à 5 ans</option>
                    <option value="De 5 à 10 ans">De 5 à 10 ans</option>
                    <option value="Plus de 10 ans">Plus de 10 ans</option>
                </select>
                @error('qualification')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mt-6">
            <label class="block mb-2 text-sm font-medium text-gray-700">Résumé court</label>
            <textarea wire:model='descrip' :disabled="locked" class="w-full p-2 border border-gray-300 rounded-md"
                placeholder="Décrivez brièvement votre service (en quelques phrases)"></textarea>
            @error('descrip')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="mb-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-700">Détails du Service</h2>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Durée estimée</label>
                <input type="text" wire:model='Duree' :disabled="locked" x-model='Duree'
                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Ex : 10 min, 1 heure, 1 jour">
                @error('Duree')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Disponibilités</label>
                <input type="text" wire:model='disponibilite' :disabled="locked"
                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Ex : Lundi au Vendredi, 9h-18h">
                @error('disponibilite')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Lieu d'intervention</label>
                <select wire:model='lieu_intervention' :disabled="locked"
                    class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="" disabled selected>Précisez le lieu</option>
                    <option value="magasin">En magasin</option>
                    <option value="domicile">À domicile</option>
                </select>
                @error('lieu_intervention')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div>
        <h2 class="mb-4 text-lg font-semibold text-gray-700">Prix(FCFA) pour <span x-text="Duree"></span>
            <span class="text-red-500">*</span>
        </h2>
        <div>
            <input type="text" wire:model='prix' class="w-full p-2 border border-gray-300 rounded-md"
                placeholder="Ex : 50 FCFA">
            @error('prix')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
