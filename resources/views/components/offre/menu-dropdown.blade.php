@props(['produit'])

<div class="relative inline-block w-full">
    <div>
        <button type="button"
            class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            id="options-menu" aria-haspopup="true" aria-expanded="true" onclick="toggleDropdown()">
            Fonctionnalités
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div id="dropdown-menu"
        class="absolute z-10 mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden">
        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
            <div class="px-4 py-2">
                <div class="flex items-center">
                    <button wire:click="simple({{ $produit->id }})"
                        class="w-full mt-3 bg-green-500 text-white py-2 rounded-xl hover:bg-green-600 transition-colors"
                        type="button">
                        Faire une Offre Simple
                    </button>
                </div>
                <div class="flex items-center">
                    <button wire:click="negocie({{ $produit->id }})"
                        class="w-full mt-3 bg-yellow-300 text-white py-2 rounded-xl hover:bg-yellow-400 transition-colors"
                        type="button">
                        Faire une Offre Négociée
                    </button>
                </div>
                <div class="flex items-center">
                    <button wire:click="groupe({{ $produit->id }})"
                        class="w-full mt-3 bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700 transition-colors"
                        type="button">
                        Faire une Offre Groupée
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDropdown() {
        const dropdownMenu = document.getElementById('dropdown-menu');
        dropdownMenu.classList.toggle('hidden');
    }

    // Fermer le menu déroulant en cliquant à l'extérieur
    document.addEventListener('click', function(event) {
        const dropdownMenu = document.getElementById('dropdown-menu');
        const optionsMenu = document.getElementById('options-menu');

        if (!optionsMenu.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });
</script>
