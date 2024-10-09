<div>
    <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mx-auto max-w-5xl">
                <div class="gap-4 sm:flex sm:items-center sm:justify-between">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Mes Notifications</h2>

                    <div class="mt-6 gap-4 space-y-4 sm:mt-0 sm:flex sm:items-center sm:justify-end sm:space-y-0">
                        <div>
                            <label for="order-type"
                                class="sr-only mb-2 block text-sm font-medium text-gray-900 dark:text-white">Select order
                                type</label>
                            <select id="order-type"
                                class="block w-full min-w-[8rem] rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500">
                                <option selected>Status *</option>
                                <option value="pre-order">Pre-order</option>
                                <option value="transit">In transit</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <span class="inline-block text-gray-500 dark:text-gray-400"> from </span>

                        <div>
                            <label for="duration"
                                class="sr-only mb-2 block text-sm font-medium text-gray-900 dark:text-white">Select
                                duration</label>
                            <select id="duration"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500">
                                <option selected>this week</option>
                                <option value="this month">this month</option>
                                <option value="last 3 months">the last 3 months</option>
                                <option value="lats 6 months">the last 6 months</option>
                                <option value="this year">this year</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flow-root sm:mt-8">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($notifications as $notification)
                            @php
                                // Récupérer l'ID de l'utilisateur depuis les données de la notification
                                $userId = $notification->data['user_id'];
                                // Optionnel : si tu veux faire d'autres actions avec l'utilisateur
                                $userDetails = App\Models\User::find($userId);
                                $userNumber = $userDetails->phone;

                                // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
                                $userInPromir = App\Models\UserPromir::where('numero', $userNumber)->exists();

                                if ($userInPromir) {
                                // Vérifier si un score de crédit existe pour cet utilisateur
                                  $crediScore = App\Models\CrediScore::where('id_user', $userInPromir)->first();
                                }

                                $demandeId = $notification->data['demande_id'];

                                $demandeCredit = App\Models\DemandeCredi::where('demande_id', $demandeId)->first();

                            @endphp
                            <div class="flex flex-wrap items-center gap-y-4 py-6">
                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Demande ID:</dt>
                                    <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="hover:underline">#{{ $notification->data['demande_id'] }}</a>
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1">
                                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Date fin:</dt>
                                    <dd class="mt-1.5 text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $demandeCredit->date_fin }}
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
                                        {{ $demandeCredit->type_financement }}
                                    </dd>
                                </dl>

                                <dl class="w-1/2 sm:w-1/4 lg:w-auto lg:flex-1 ml-3">
                                    <dt class="text-base font-medium text-gray-500 dark:text-gray-400">Status:</dt>
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
                                </dl>

                                <div
                                    class="w-full grid sm:grid-cols-2 lg:flex lg:w-64 lg:items-center lg:justify-end gap-4">
                                    <!-- Affichage conditionnel basé sur la réponse -->
                                    @if ($notification->reponse == 'refuse')
                                        <span class="text-sm text-gray-500">Refusé</span>
                                    @else
                                        <!-- Bouton "Refuser" -->
                                        <button type="button" wire:click="refuse('{{ $notification->id }}')"
                                            class="w-full rounded-lg border border-red-700 px-3 py-2 text-center text-sm font-medium text-red-700 hover:bg-red-700 hover:text-white focus:outline-none focus:ring-4 focus:ring-red-300 dark:border-red-500 dark:text-red-500 dark:hover:bg-red-600 dark:hover:text-white dark:focus:ring-red-900 lg:w-auto">
                                            <span wire:loading.remove>Refuser</span>

                                            <!-- Loader qui s'affiche pendant le chargement -->
                                            <span wire:loading class="ml-2">
                                                <svg class="animate-spin h-5 w-5 text-red-700"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z"></path>
                                                </svg>
                                            </span>
                                        </button>


                                        <button data-modal-target="extralarge-{{ $notification->id }}"
                                            data-modal-toggle="extralarge-{{ $notification->id }}"
                                            class="w-full inline-flex justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 lg:w-auto">
                                            View details
                                        </button>
                                    @endif
                                </div>

                                <!-- Extra Large Modal -->
                                <div id="extralarge-{{ $notification->id }}" tabindex="-1"
                                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                    <div class="relative w-full max-w-7xl max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                            <!-- Modal header -->
                                            <div class="bg-gray-100">
                                                <div class="container mx-auto p-6">
                                                    <!-- Header -->
                                                    <div class="flex justify-between items-center mb-6">
                                                        <h1 class="text-3xl font-semibold text-gray-800">Détails de la
                                                            demande de crédit</h1>
                                                        <button
                                                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2"
                                                            data-modal-hide="extralarge-modal">Retour à la
                                                            liste</button>
                                                    </div>

                                                    <!-- Card de détails du client -->
                                                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                                                        <h2 class="text-xl font-bold mb-4 text-gray-800">Informations
                                                            sur le client</h2>
                                                        <div class="grid grid-cols-3 gap-4">
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Nom du client:</p>
                                                                <p class="text-gray-800">{{ $userDetails->name }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Email:</p>
                                                                <p class="text-gray-800">{{ $userDetails->email }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Numéro de
                                                                    téléphone:</p>
                                                                <p class="text-gray-800">{{ $userDetails->phone }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Cote de Crédit</p>
                                                                <p class="text-gray-800">{{ $crediScore->ccc }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Adresse:</p>
                                                                <p class="text-gray-800">
                                                                    {{ $userDetails->country }},{{ $userDetails->ville }},{{ $userDetails->departe }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Card de détails de la demande de crédit -->
                                                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                                                        <h2 class="text-xl font-bold mb-4 text-gray-800">Informations
                                                            sur la demande de crédit</h2>
                                                        <div class="grid grid-cols-3 gap-4">
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Montant demandé:
                                                                </p>
                                                                <p class="text-gray-800">
                                                                    {{ $notification->data['montant'] }} FCFA</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Durée du crédit:
                                                                </p>
                                                                <p class="text-gray-800">{{ $demandeCredit->duree }}
                                                                    mois</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Taux du crédit:
                                                                </p>
                                                                <p class="text-gray-800">{{ $demandeCredit->taux }} %
                                                                    </p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Date debut:
                                                                </p>
                                                                <p class="text-gray-800">{{ $demandeCredit->date_debut }}
                                                                    </p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Date fin:
                                                                </p>
                                                                <p class="text-gray-800">{{ $demandeCredit->date_fin }}
                                                                    </p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">Type de crédit:
                                                                </p>
                                                                <p class="text-gray-800">
                                                                    {{ $demandeCredit->type_financement }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">heure debut:
                                                                </p>
                                                                <p class="text-gray-800">{{ $demandeCredit->heure_debut }}
                                                                    </p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-600 font-medium">heure fin:
                                                                </p>
                                                                <p class="text-gray-800">{{ $demandeCredit->heure_fin }}
                                                                    </p>
                                                            </div>

                                                            <div>
                                                                <p class="text-gray-600 font-medium">Motif du crédit:
                                                                </p>
                                                                <p class="text-gray-800">
                                                                    {{ $demandeCredit->objet_financement }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Actions -->
                                                    <div class="flex justify-end space-x-4">
                                                        <button wire:click = "sendCredit"
                                                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5"
                                                            data-modal-hide="extralarge-modal">Approuver</button>
                                                        <button
                                                            class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5"
                                                            data-modal-hide="extralarge-modal">Rejeter</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        @endforeach
                    </div>
                </div>


                {{-- <nav class="mt-6 flex items-center justify-center sm:mt-8" aria-label="Page navigation example">
                    <ul class="flex h-8 items-center -space-x-px text-sm">
                        <li>
                            <a href="#"
                                class="ms-0 flex h-8 items-center justify-center rounded-s-lg border border-e-0 border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <span class="sr-only">Previous</span>
                                <svg class="h-4 w-4 rtl:rotate-180" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m15 19-7-7 7-7" />
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex h-8 items-center justify-center border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex h-8 items-center justify-center border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</a>
                        </li>
                        <li>
                            <a href="#" aria-current="page"
                                class="z-10 flex h-8 items-center justify-center border border-primary-300 bg-primary-50 px-3 leading-tight text-primary-600 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">3</a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex h-8 items-center justify-center border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">...</a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex h-8 items-center justify-center border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">100</a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex h-8 items-center justify-center rounded-e-lg border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <span class="sr-only">Next</span>
                                <svg class="h-4 w-4 rtl:rotate-180" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m9 5 7 7-7 7" />
                                </svg>
                            </a>
                        </li>
                    </ul>
                </nav> --}}
            </div>


    </section>
</div>
