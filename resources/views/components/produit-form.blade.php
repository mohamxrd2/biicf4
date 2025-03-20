<div>
    <h1 class="mb-8 text-xl font-bold text-center">Ajout de Produit</h1>

    <div class="mb-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-700">Informations sur le Conditionnement</h2>
        <div class="col-span-2 grid lg:grid-cols-2 sm:grid-cols-2  gap-6">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Type de Conditionnement</label>
                <input type="text" wire:model='conditionnement' x-model="conditionnement" :disabled="locked"
                    class="w-full p-2 border border-gray-300 rounded-md" placeholder="Ex : sac, sachet...">
                @error('conditionnement')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Dimensions</label>
                <textarea wire:model="format" :disabled="locked" class="w-full p-2 border border-gray-300 rounded-md"
                    placeholder="">
                </textarea>

                @error('format')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>


        </div>
    </div>

    <div class="mb-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-700">Détails du Produit</h2>
        <div class="grid col-span-2  lg:grid-cols-2 sm:grid-cols-2  gap-6">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Particularité</label>
                <input type="text" wire:model='particularite' :disabled="locked"
                    class="w-full p-2 border border-gray-300 rounded-md"
                    placeholder="Précisez la particularité du produit">
                @error('particularite')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Origine</label>
                <select wire:model='origine' :disabled="locked"
                    class="w-full p-2 border border-gray-300 rounded-md">
                    <option>Choisissez une origine</option>
                    <option>Locale</option>
                    <option>Importé</option>
                </select>
                @error('origine')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-700">Quantités</h2>
        <div class="grid col-span-2  lg:grid-cols-2 sm:grid-cols-2  gap-6">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Quantité Minimale<span
                        class="text-red-500">*</span></label>
                <input type="text" wire:model='qteProd_min' class="w-full p-2 border border-gray-300 rounded-md"
                    placeholder="Ex : 10">
                @error('qteProd_min')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Quantité Maximale<span
                        class="text-red-500">*</span></label>
                <input type="text" wire:model='qteProd_max' class="w-full p-2 border border-gray-300 rounded-md"
                    placeholder="Ex : 100">
                @error('qteProd_max')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div>
        <h2 class="mb-4 text-lg font-semibold text-gray-700">Prix(FCFA) par <span x-text="conditionnement"></span>
            <span class="text-red-500">*</span>
        </h2>
        <div>
            <input type="text" wire:model='prix' placeholder="Ex : 10000fcfa"
                class="w-full p-2 border border-gray-300 rounded-md">
            @error('prix')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
