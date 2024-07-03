<div>
    @if ($notification->type === 'App\Notifications\AchatGroupBiicf')

        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">

            <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                </span>{{ $notification->data['nameProd'] }}</h2>
            <p class="mb-3"><strong>Quantité:</strong> {{ $notification->data['quantité'] }}</p>
            <p class="mb-3"><strong>Prix totale:</strong> {{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa</p>
            @php
                $prixArtiche = $notification->data['montantTotal'] ?? 0;
                $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
            @endphp

            <p class="mb-3"><strong>Somme reçu :</strong> {{ number_format($sommeRecu, 2) }} Fcfa</p>

            <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                class="mb-3 text-blue-700 hover:underline flex">
                Voir le produit
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>
            <p class="my-3 text-sm text-gray-500">Vous aurez debité 10% sur le prix de la marchandise</p>


            <div class="flex gap-2">


                @if ($notification->reponse)
                    <div class="w-full bg-gray-300 border p-2 rounded-md">
                        <p class="text-md font-medium text-center">Réponse envoyée</p>
                    </div>
                @else
                    <form wire:submit.prevent="accepterAGrouper">
                        @csrf
                        @foreach ($notification->data['userSender'] as $index => $userId)
                            <input type="hidden" name="userSender[]" value="{{ $userId }}"
                                wire:model="userSender.{{ $index }}">
                        @endforeach

                        <input type="text" name="montantTotal" value="{{ $notification->data['montantTotal'] }}"
                            wire:model="montantTotal">
                        <input type="text" name="idProd" value="{{ $notification->data['idProd'] }}"
                            wire:model="idProd">
                        <input type="text" name="message"
                            value="commande de produit en cours /Préparation à la livraison" wire:model="messageA">
                        <input type="text" name="notifId" value="{{ $notification->id }}" wire:model="notifId">

                        <!-- Bouton accepter -->
                        <button id="btn-accepter" type="submit"
                            class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">
                            Accepter
                        </button>
                    </form>

                    <form wire:submit.prevent="refuserAGrouper">
                        @csrf

                        @foreach ($notification->data['userSender'] as $index => $userId)
                            <input type="hidden" name="userSender[]" value="{{ $userId }}"
                                wire:model="userSender.{{ $index }}">
                        @endforeach

                        <input type="text" name="montantTotal" value="{{ $notification->data['montantTotal'] }}"
                            wire:model="montantTotal">
                        <input type="text" name="idProd" value="{{ $notification->data['idProd'] }}"
                            wire:model="idProd">
                        <input type="text" name="message" value="Achat refuser / Produits plus disponible"
                            wire:model="messageR">
                        <input type="text" name="notifId" value="{{ $notification->id }}" wire:model="notifId">

                        <button id="btn-refuser" type="submit"
                            class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">
                            Refuser
                        </button>
                    </form>

                @endif

            </div>

        </div>
    @elseif ($notification->type === 'App\Notifications\AchatBiicf')
        <div class="flex flex-col bg-white p-4 rounded-xl border justify-center">
            <h2 class="text-xl font-medium mb-4"><span class="font-semibold">Titre:
                </span>{{ $notification->data['nameProd'] }}</h2>
            <p class="mb-3"><strong>Quantité:</strong> {{ $notification->data['quantité'] }}</p>
            <p class="mb-3"><strong>Localité:</strong> {{ $notification->data['localite'] }}</p>
            <p class="mb-3"><strong>Spécificité:</strong> {{ $notification->data['specificite'] }}</p>
            <p class="mb-3"><strong>Prix d'artiche:</strong> {{ $notification->data['montantTotal'] ?? 'N/A' }} Fcfa
            </p>

            @php
                $prixArtiche = $notification->data['montantTotal'] ?? 0;
                $sommeRecu = $prixArtiche - $prixArtiche * 0.1;
            @endphp

            <p class="mb-3"><strong>Somme reçu :</strong> {{ number_format($sommeRecu, 2) }} Fcfa</p>
            <a href="{{ route('biicf.postdet', $notification->data['idProd']) }}"
                class="mb-3 text-blue-700 hover:underline flex">
                Voir le produit
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                </svg>
            </a>

            <p class="my-3 text-sm text-gray-500">Vous aurez debité 10% sur le prix de la marchandise
            </p>
            <div class="flex gap-2">
                @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                    <div class="w-full bg-gray-300 border p-2 rounded-md">
                        <p class="text-md font-medium text-center">Reponse envoyé</p>

                    </div>
                @else
                    <button wire:click="accepter" id="btn-accepter" type="submit"
                        class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700"
                        wire:loading.attr="disabled">
                        Accepter
                    </button>

                    <div wire:loading wire:target="accepter">
                        En cours...
                    </div>


                    <button wire:click="refuser" id="btn-refuser" type="submit"
                        class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                @endif
            </div>
        </div>



    @endif
</div>
