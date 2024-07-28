<div class="max-w-4xl mx-auto p-8 bg-white shadow-md rounded-md">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Succès!</strong>
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <h2 class="text-xl text-center font-semibold mb-4">Postuler pour être livreur</h2>

    <form wire:submit.prevent="submit" class="space-y-6">

        <div>
            <label for="experience" class="block text-sm font-medium text-gray-700">Expérience:</label>
            <select id="experience" wire:model="experience"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                
                <option value="moins de 1 an">Moins de 1 an</option>
                <option value="entre 1 et 5 ans">Entre 1 et 5 ans</option>
                <option value="5 ans et plus">5 ans et plus</option>
            </select>
            @error('experience')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>



        <div>
            <label for="license" class="block text-sm font-medium text-gray-700">Type de permis de conduire:</label>

            <select id="license" wire:model="license"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
             
                <option value="Permis A">Permis A</option>
                <option value="Permis B">Permis B</option>
                <option value="Permis C">Permis C</option>
            </select>
            @error('license')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="vehicle" class="block text-sm font-medium text-gray-700">Véhicule possédé:</label>
            <select id="vehicle" wire:model="vehicle"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              
                <option value="Moto">Moto</option>
                <option value="Fourgonette">Fourgonette</option>
                <option value="Camion">Camion</option>
            </select>
            @error('vehicle')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="matricule" class="block text-sm font-medium text-gray-700">Matricule du vehicule</label>
            <input id="matricule" type="text" wire:model="matricule" placeholder="Matrucule du vehicule"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @error('matricule')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="availability" class="block text-sm font-medium text-gray-700">Disponibilités:</label>
            <input type="text" id="availability" wire:model="availability"
                placeholder="Dites-nous votre disponiblité"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @error('availability')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>


        <div>
            <label for="zone" class="block text-sm font-medium text-gray-700">Zone de livraison:</label>
            <textarea id="zone" wire:model="zone"
                placeholder="Ajouter une plusieur zone de livraison et separez les avec un ';' "
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            @error('zone')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="comments" class="block text-sm font-medium text-gray-700">Questions ou commentaires: (facultatif)</label>
            <textarea id="comments" wire:model="comments"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Ajouter un commentaire"></textarea>
           
        </div>

        <div>

            <label for="identity" class="block text-sm font-medium text-gray-700">Piece d'identité:</label>
            <input type="file" id="identity" wire:model="identity"
                class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
            @error('identity')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="permis" class="block text-sm font-medium text-gray-700">Permis de conduire:</label>
            <input type="file" id="permis" wire:model="permis"
                class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
            @error('permis')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="assurance" class="block text-sm font-medium text-gray-700">Assurance du vehicule:</label>
            <input type="file" id="assurance" wire:model="assurance"
                class="mt-1 block w-full text-sm text-gray-900 bg-gray-50 rounded-md border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500">
            @error('assurance')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit"
            class=" py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Soumettre la candidature
        </button>
    </form>
</div>
