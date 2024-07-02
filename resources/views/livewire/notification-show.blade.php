<div wire:poll.150ms>
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
                    <form id="form-accepter" action="{{ route('achatG.accepter') }}" method="POST">
                        @csrf
                        @foreach ($notification->data['userSender'] as $userId)
                            <input type="hidden" name="userSender[]" value="{{ $userId }}">
                        @endforeach

                        <input type="hidden" name="montantTotal" value="{{ $notification->data['montantTotal'] }}">
                        <input type="hidden" name="idProd" value="{{ $notification->data['idProd'] }}">
                        <input type="hidden" name="message"
                            value="commande de produit en cours /Préparation à la livraison">
                        <input type="hidden" name="notifId" value="{{ $notification->id }}">

                        <!-- Bouton accepter -->
                        <button id="btn-accepter" type="submit"
                            class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">
                            Accepter
                        </button>
                    </form>

                    <form id="form-accepter" action="{{ route('achatG.refuser') }}" method="POST">
                        @csrf

                        @foreach ($notification->data['userSender'] as $userId)
                            <input type="hidden" name="userSender[]" value="{{ $userId }}">
                        @endforeach

                        <input type="hidden" name="montantTotal" value="{{ $notification->data['montantTotal'] }}">
                        <input type="hidden" name="message" value="refus de produit">
                        <input type="hidden" name="idProd" value="{{ $notification->data['idProd'] }}">
                        <input type="hidden" name="notifId" value="{{ $notification->id }}">

                        <button id="btn-refuser" type="submit"
                            class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">
                            Refuser
                        </button>
                    </form>

                @endif

            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const prixTradeInput = document.getElementById('prixTrade');
                    const submitBtn = document.getElementById('submitBtnAppel');
                    const prixTradeError = document.getElementById('prixTradeError');

                    prixTradeInput.addEventListener('input', function() {
                        const prixTradeValue = parseFloat(prixTradeInput.value);
                        const lowestPricedProduct = parseFloat('{{ $notification->data['lowestPricedProduct'] }}');

                        if (prixTradeValue > lowestPricedProduct) {
                            submitBtn.disabled = true;
                            prixTradeError.textContent = 'Le prix ne doit pas dépasser ' + lowestPricedProduct;
                            prixTradeError.classList.remove('hidden');
                        } else {
                            submitBtn.disabled = false;
                            prixTradeError.textContent = '';
                            prixTradeError.classList.add('hidden');
                        }
                    });

                    // Convertir la date de départ en objet Date JavaScript
                    const startDate = new Date("{{ $oldestCommentDate }}");

                    // Ajouter 5 heures à la date de départ
                    startDate.setMinutes(startDate.getMinutes() + 1);

                    // Mettre à jour le compte à rebours à intervalles réguliers
                    const countdownTimer = setInterval(updateCountdown, 1000);

                    function updateCountdown() {
                        // Obtenir la date et l'heure actuelles
                        const currentDate = new Date();

                        // Calculer la différence entre la date cible et la date de départ en millisecondes
                        const difference = startDate.getTime() - currentDate.getTime();

                        // Convertir la différence en jours, heures, minutes et secondes
                        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                        // Afficher le compte à rebours dans l'élément HTML avec l'id "countdown"
                        const countdownElement = document.getElementById('countdown');
                        countdownElement.innerHTML = `
                            <div>${hours}h</div>:
                            <div>${minutes}m</div>:
                            <div>${seconds}s</div>
                        `;

                        // Arrêter le compte à rebours lorsque la date cible est atteinte
                        if (difference <= 0) {
                            clearInterval(countdownTimer);
                            countdownElement.innerHTML = "Temps écoulé !";
                            prixTradeInput.disabled = true; // Désactiver le champ input
                            document.getElementById('submitBtnAppel').hidden = true;
                            prixTradeError.textContent =
                                `Le fournisseur avec le prix le plus bas est {{ $lowPriceUserName }} avec {{ $lowPriceAmount }} FCFA!`;
                            prixTradeError.classList.remove('hidden');
                        }

                    }
                });
            </script>
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

            <p class="my-3 text-sm text-gray-500">Vous aurez debité 10% sur le prix de la marchandise</p>
            <div class="flex gap-2">
                @if ($notification->reponse == 'accepte' || $notification->reponse == 'refuser')
                    <div class="w-full bg-gray-300 border p-2 rounded-md">
                        <p class="text-md font-medium text-center">Reponse envoyé</p>

                    </div>
                @else
                    <form action="{{ route('achatD.accepter') }}" method="POST">
                        @csrf
                        <input type="hidden" name="userSender" value="{{ $notification->data['userSender'] }}">
                        <input type="hidden" name="montantTotal" value="{{ $notification->data['montantTotal'] }}">
                        <input type="hidden" name="message"
                            value="commande de produit en cours /Préparation a la livraison">

                        <input type="hidden" name="notifId" value="{{ $notification->id }}">



                        <!-- Bouton accepter -->

                        <button id="btn-accepter" type="submit"
                            class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">Accepter</button>

                    </form>

                    <form action="{{ route('achatD.refuser') }}" method="POST">
                        @csrf
                        <input type="hidden" name="montantTotal" value="{{ $notification->data['montantTotal'] }}">
                        <input type="hidden" name="userSender" value="{{ $notification->data['userSender'] }}">
                        <input type="hidden" name="message" value="refus de produit">

                        <input type="hidden" name="notifId" value="{{ $notification->id }}">

                        <button id="btn-refuser" type="submit"
                            class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Refuser</button>
                    </form>
                @endif

            </div>
        </div>
    


    @endif
</div>

