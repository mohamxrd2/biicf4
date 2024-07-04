<div>
    @if (session('success'))
        <div class="bg-green-500 text-white font-bold rounded-lg border shadow-lg p-3 mt-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- Afficher les messages d'erreur -->
    @if (session('error'))
        <div class="bg-red-500 text-white font-bold rounded-lg border shadow-lg p-3 mt-3">
            {{ session('error') }}
        </div>
    @endif
    <form wire:submit.prevent="AchatDirectForm" id="formAchatDirect"
        class="mt-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md" style="display: none;" method="POST">
        @csrf
        <h1 class="text-xl text-center mb-3">Achat direct</h1>

        <div class="space-y-3 mb-3 w-full">
            <input type="number" id="quantityInput" name="quantité" wire:model.defer="quantité"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Quantité" data-min="{{ $produit->qteProd_min }}" data-max="{{ $produit->qteProd_max }}"
                oninput="updateMontantTotalDirect()" required>
        </div>

        <div class="space-y-3 mb-3 w-full">
            <input type="text" id="locationInput" name="localite" wire:model.defer="localite"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Lieu de livraison" required>
        </div>

        <div class="space-y-3 mb-3 w-full">
            <input type="text" id="specificite" name="specificite" wire:model.defer="specificite"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Specificité (Facultatif)">
        </div>

        <input type="hidden" name="userTrader" wire:model.defer="userTrader" value="{{ $produit->user->id }}">
        <input type="hidden" name="nameProd" wire:model.defer="nameProd" value="{{ $produit->name }}">
        <input type="hidden" name="userSender" wire:model.defer="userSender" value="{{ $userId }}">
        <input type="hidden" name="message" wire:model.defer="message" value="Un utilisateur veut acheter ce produit">
        <input type="hidden" name="photoProd" wire:model.defer="photoProd" value="{{ $produit->photoProd1 }}">
        <input type="hidden" name="idProd" wire:model.defer="idProd" value="{{ $produit->id }}">
        <input type="hidden" name="prix" wire:model.defer="prix" value="{{ $produit->prix }}">

        <div class="flex justify-between px-4 mb-3 w-full">
            <p class="font-semibold text-sm text-gray-500">Prix total:</p>
            <p class="text-sm text-purple-600" id="montantTotal">0 FCFA</p>
            <input type="hidden" name="montantTotal" id="montant_total_input">
        </div>

        <p id="errorMessage" class="text-sm text-center text-red-500 hidden">Erreur</p>

        <div class="w-full text-center mt-3">
            <button type="reset"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-200 text-black hover:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none">Annulé</button>
            <button type="submit" id="submitButton"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                wire:loading.attr="disabled" disabled>

                <span wire:loading.remove>Envoyer</span>
                <span wire:loading>Envoi en cours...</span>
            </button>
        </div>
    </form>
    <form wire:submit.prevent="AchatGroupeForm"
        class="mt-4 flex flex-col p-4 bg-gray-50 border border-gray-200 rounded-md" id="formAchatGroup"
        style="display: none;" method="POST">
        @csrf
        <h1 class="text-xl text-center mb-3">Achat groupé</h1>


        <div>
            <p class="text-center text-md font-medium text-gray-700 mb-3">Nombre de participants: <span
                    class="text-md  text-purple-800">{{ $nbreAchatGroup }}</span></p>
        </div>

        <div class="space-y-3 mb-3 w-full">
            <input type="number" id="quantityInput1" name="quantité" wire:model.defer="quantité"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Quantité" data-min="{{ $produit->qteProd_min }}" data-max="{{ $produit->qteProd_max }}"
                oninput="updateMontantTotalGroup()" required>
        </div>

        <div class="space-y-3 mb-3 w-full">
            <input type="text" id="locationInput1" name="localite" wire:model.defer="localite"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Lieu de livraison" required>
        </div>

        <div class="space-y-3 mb-3 w-full">
            <input type="text" id="specificite1" name="specificite" wire:model.defer="specificite"
                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-purple-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                placeholder="Specificité (Facultatif)">
        </div>

        <input type="hidden" wire:model.defer="userTrader" name="userTrader" value="{{ $produit->user->id }}">
        <input type="hidden" wire:model.defer="nameProd" name="nameProd" value="{{ $produit->name }}">
        <input type="hidden" wire:model.defer="userSender" name="userSender" value="{{ $userId }}">
        <input type="hidden" wire:model.defer="message" name="message"
            value="Un utilisateur veut acheter ce produit">
        <input type="hidden" wire:model.defer="photoProd" name="photoProd" value="{{ $produit->photoProd1 }}">
        <input type="hidden" wire:model.defer="idProd" name="idProd" value="{{ $produit->id }}">
        <input type="hidden" name="prix" wire:model.defer="prix" value="{{ $produit->prix }}">


        <div class="flex justify-between px-4 mb-3 w-full">
            <p class="font-semibold text-sm text-gray-500">Prix total:</p>
            <p class="text-sm text-purple-600" id="montantTotal1">0 FCFA</p>
            <input type="hidden" name="montantTotal" id="montant_total_input1">
        </div>

        <p id="errorMessage1" class="text-sm text-center text-red-500 hidden">Erreur</p>

        <div class="w-full text-center mt-3">
            <button type="reset"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gray-200 text-black hover:bg-gray-300 disabled:opacity-50 disabled:pointer-events-none">Annulé
            </button>
            <button type="submit" id="submitButton1"
                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50 disabled:pointer-events-none"
                wire:loading.attr="disabled" disabled>

                <span wire:loading.remove>Envoyer</span>
                <span wire:loading>Envoi en cours...</span>
            </button>
        </div>


        @if ($nbreAchatGroup > 0 && isset($userId) && isset($produit->user->id) && $userId != $produit->user->id)
            <div id="countdown-container" class="flex flex-col justify-center items-center mt-4">
                <span class="mb-2">Temps restant pour cet achat groupé</span>
                <div id="countdown"
                    class="flex items-center gap-2 text-3xl font-semibold text-red-500 bg-red-100 p-3 rounded-xl w-auto">
                    <div>-</div>:
                    <div>-</div>:
                    <div>-</div>:
                    <div>-</div>
                </div>
            </div>
        @endif
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAchatDirect = document.getElementById('btnAchatDirect');
        const btnAchatGroup = document.getElementById('btnAchatGroup');
        const formAchatDirect = document.getElementById('formAchatDirect');
        const formAchatGroup = document.getElementById('formAchatGroup');

        btnAchatDirect.addEventListener('click', function() {
            if (formAchatDirect.style.display === 'none' || formAchatDirect.style.display === '') {
                formAchatDirect.style.display = 'block';
                formAchatGroup.style.display = 'none';
            } else {
                formAchatDirect.style.display = 'none';
            }
        });

        btnAchatGroup.addEventListener('click', function() {
            if (formAchatGroup.style.display === 'none' || formAchatGroup.style.display === '') {
                formAchatGroup.style.display = 'block';
                formAchatDirect.style.display = 'none';
            } else {
                formAchatGroup.style.display = 'none';
            }
        });
    });

    function toggleVisibility() {
        const contentDiv = document.getElementById('toggleContent');

        if (contentDiv.classList.contains('hidden')) {
            contentDiv.classList.remove('hidden');
            // Forcing reflow to enable transition
            contentDiv.offsetHeight;
            contentDiv.classList.add('show');
        } else {
            contentDiv.classList.remove('show');
            contentDiv.addEventListener('transitionend', () => {
                contentDiv.classList.add('hidden');
            }, {
                once: true
            });
        }
    }

    // Fonction pour mettre à jour le montant total pour l'achat direct
    function updateMontantTotalDirect() {
        const quantityInput = document.getElementById('quantityInput');
        const price = parseFloat(document.querySelector('[data-price]').getAttribute('data-price'));
        const minQuantity = parseInt(quantityInput.getAttribute('data-min'));
        const maxQuantity = parseInt(quantityInput.getAttribute('data-max'));
        const quantity = parseInt(quantityInput.value);
        const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
        const montantTotalElement = document.getElementById('montantTotal');
        const errorMessageElement = document.getElementById('errorMessage');
        const submitButton = document.getElementById('submitButton');
        const montantTotalInput = document.getElementById('montant_total_input');

        const userBalance = {{ $userWallet->balance }};

        if (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity) {
            errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = '0 FCFA';
            submitButton.disabled = true;
        } else if (montantTotal > userBalance) {
            errorMessageElement.innerText =
                `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            submitButton.disabled = true;
        } else {
            errorMessageElement.classList.add('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input
            submitButton.disabled = false;
        }
    }

    // Fonction pour mettre à jour le montant total pour l'achat groupé
    function updateMontantTotalGroup() {
        const quantityInput = document.getElementById('quantityInput1');
        const price = parseFloat(document.querySelector('[data-price]').getAttribute('data-price'));
        const minQuantity = parseInt(quantityInput.getAttribute('data-min'));
        const maxQuantity = parseInt(quantityInput.getAttribute('data-max'));
        const quantity = parseInt(quantityInput.value);
        const montantTotal = price * (isNaN(quantity) ? 0 : quantity);
        const montantTotalElement = document.getElementById('montantTotal1');
        const errorMessageElement = document.getElementById('errorMessage1');
        const submitButton = document.getElementById('submitButton1');
        const montantTotalInput = document.getElementById('montant_total_input1');

        const userBalance = {{ $userWallet->balance }};

        if (isNaN(quantity) || quantity === 0 || quantity < minQuantity || quantity > maxQuantity) {
            errorMessageElement.innerText = `La quantité doit être comprise entre ${minQuantity} et ${maxQuantity}.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = '0 FCFA';
            submitButton.disabled = true;
        } else if (montantTotal > userBalance) {
            errorMessageElement.innerText =
                `Le fond est insuffisant. Votre solde est de ${userBalance.toLocaleString()} FCFA.`;
            errorMessageElement.classList.remove('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            submitButton.disabled = true;
        } else {
            errorMessageElement.classList.add('hidden');
            montantTotalElement.innerText = `${montantTotal.toLocaleString()} FCFA`;
            montantTotalInput.value = montantTotal; // Met à jour l'input montant_total_input
            submitButton.disabled = false;
        }
    }

    document.addEventListener('livewire:init', function() {
        Livewire.on('start-timer', () => {
            // Convertir la date de départ en objet Date JavaScript
            const startDate = new Date();

            // Ajouter 5 jours à la date de départ
            startDate.setMinutes(startDate.getMinutes() + 1);

            // Mettre à jour le compte à rebours à intervalles réguliers
            const countdownTimer = setInterval(updateCountdown, 1000);

            function updateCountdown() {
                // Obtenir la date et l'heure actuelles
                const currentDate = new Date();

                // Calculer la différence entre la date cible et la date de départ en millisecondes
                const difference = startDate.getTime() - currentDate.getTime();

                // Convertir la différence en jours, heures, minutes et secondes
                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                // Afficher le compte à rebours dans l'élément HTML avec l'id "countdown"
                const countdownElement = document.getElementById('countdown');
                countdownElement.innerHTML = `
             <div>${days}j</div>:
             <div>${hours}h</div>:
             <div>${minutes}m</div>:
            <div>${seconds}s</div>
              `;

                // Arrêter le compte à rebours lorsque la date cible est atteinte
                if (difference <= 0) {
                    clearInterval(countdownTimer);
                    countdownElement.innerHTML = "Temps écoulé !";
                }
            }
            setTimeout(() => {
                Livewire.dispatch('sendNotification');
            }, 60000); // 1 minute
        });
    });
</script>
