<div class="flex flex-col space-y-4">
    <!-- Titre -->
    <p class="text-lg font-medium text-gray-700">Joindre un utilisateur pour les retraits à double authentification</p>

    <!-- Champ de recherche -->
    @if (session()->has('message'))
    <div class="text-green-600 text-sm mt-2">{{ session('message') }}</div>
@endif

@if (session()->has('error'))
    <div class="text-red-600 text-sm mt-2">{{ session('error') }}</div>
@endif
    <div class="relative">
        <input type="text" wire:model.live="search" id="recipient" placeholder="Recherchez un utilisateur"
            class="py-3 px-4 block w-full border border-gray-300 rounded-lg shadow-sm text-sm text-gray-700 focus:ring-indigo-500 focus:border-indigo-500"
        >

        <!-- Erreur -->
        @error('user_id')
            <span class="text-sm text-red-600">{{ $message }}</span>
        @enderror

        <!-- Suggestions -->
        @if (!empty($search) && !empty($users))
            <div class="absolute z-10 bg-white border border-gray-300 rounded-lg shadow-lg w-full mt-1">
                @foreach ($users as $user)
                    <div class="cursor-pointer py-2 px-4 hover:bg-gray-100 flex items-center space-x-3"
                        wire:click="selectUser('{{ $user->id }}', '{{ $user->username }}')">
                        <img class="w-8 h-8 rounded-full" src="{{ asset($user->photo) }}" alt="Photo de profil">
                        <div class="text-sm text-gray-800">
                            <span class="font-medium">{{ $user->username }}</span>
                            <span class="text-gray-500">({{ $user->name }})</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bouton Ajouter -->
    <div class="text-right">
        <button wire:click="addUser" class="inline-flex items-center px-6 py-2 text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg shadow focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-8H6a1 1 0 110-2h3V6a1 1 0 112 0v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3z" clip-rule="evenodd" />
            </svg>
            Ajouter
        </button>
    </div>

    <!-- Message de confirmation -->


    <script>
        // Écouter l'événement de rafraîchissement
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('user-added', () => {
                // Rafraîchir la page ou effectuer d'autres actions
                location.reload();
            });
        });
    </script>
   
</div>
