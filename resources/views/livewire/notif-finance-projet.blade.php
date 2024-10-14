<div wire:poll.live>


    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach ($notifications as $notification)
            @php
                // Récupérer l'ID de l'utilisateur depuis les données de la notification
                $userId = $notification->data['user_id'];

                // Vérifier si l'utilisateur existe dans la table "users"
$userDetails = App\Models\User::find($userId);

if ($userDetails) {
    // Récupérer le numéro de téléphone de l'utilisateur
                    $userNumber = $userDetails->phone;

                    // Vérifier si le numéro de téléphone existe dans la table "user_promir"
                    $userInPromir = App\Models\UserPromir::where('numero', $userNumber)->first();

                    if ($userInPromir) {
                        // Récupérer le score de crédit de l'utilisateur
        $crediScore = App\Models\CrediScore::where('id_user', $userInPromir->id)->first();
                    }
                }

            @endphp

            <div class="flex flex-wrap items-center gap-y-4 py-6">
                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Projet ID:</dt>
                    <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                        <a href="#" class="hover:underline">#{{ $notification->data['projet_id'] }}</a>
                    </dd>
                </dl>

                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Date fin:</dt>
                    <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                        {{ $notification->data['duree'] }}
                    </dd>
                </dl>

                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Montant:</dt>
                    <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                        {{ $notification->data['montant'] }} FCFA
                    </dd>
                </dl>

                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1 mr-3">
                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">type:</dt>
                    <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                        {{ $notification->data['type_financement'] }}
                    </dd>
                </dl>

                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1 ml-3">
                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Status:</dt>
                    @if ($notification->reponse == 'approved')
                        <dd
                            class="me-2 mt-1.5 inline-flex items-center rounded bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                            </svg>
                            confirmer
                        </dd>
                    @elseif ($notification->reponse == 'refuser')
                        <dd
                            class="me-2 mt-1.5 inline-flex items-center rounded bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                            </svg>
                            refuser
                        </dd>
                    @else
                        <dd
                            class="me-2 mt-1.5 inline-flex items-center rounded bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                            <svg class="me-1 h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                            </svg>
                            En Attente
                        </dd>
                    @endif
                </dl>

                <div class="w-full grid sm:grid-cols-2 lg:flex lg:w-64 lg:items-center lg:justify-end gap-4">
                    <!-- Affichage conditionnel basé sur la réponse -->

                    <a href="{{ route('detailcreditprojet', $notification->id) }}"
                        data-modal-toggle="extralarge-{{ $notification->id }}"
                        class="w-full inline-flex justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 lg:w-auto">
                        Voir details
                    </a>
                </div>
            </div>
        @endforeach
    </div>



</div>
