<div wire:poll.1500ms class="min-h-screen bg-gray-50/50">
    <div class="container mx-auto px-4 py-10">
        {{-- En-tête avec navigation --}}
        <div class="mb-10 max-w-4xl mx-auto">
            <h1 class="text-center font-bold text-3xl mb-8 text-gray-900">
                Liste des demandes
                <div class="mt-2 text-sm font-normal text-gray-500">Gérez toutes vos demandes en un seul endroit</div>
            </h1>
            
            <nav class="flex justify-center gap-4 flex-wrap">
                <x-tab-button 
                    wire:click="$set('activeTab', 'livraisons')"
                    :active="$activeTab === 'livraisons'"
                    icon="fas fa-truck"
                    label="Livraisons" />
                
                <x-tab-button 
                    wire:click="$set('activeTab', 'psaps')"
                    :active="$activeTab === 'psaps'"
                    icon="fas fa-headset"
                    label="PSAP" />
                
                <x-tab-button 
                    wire:click="$set('activeTab', 'deposits')"
                    :active="$activeTab === 'deposits'"
                    icon="fas fa-wallet"
                    label="Approvisionnement" />
                
                <x-tab-button 
                    wire:click="$set('activeTab', 'retrait')"
                    :active="$activeTab === 'retrait'"
                    icon="fas fa-money-bill-wave"
                    label="Demande de retrait" />
            </nav>
        </div>

        <div class="max-w-6xl mx-auto">
            {{-- Section Livraisons --}}
            @if ($activeTab === 'livraisons')
                <div class="space-y-6" x-show="true" x-transition>
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">Liste des demandes Livraisons</h2>
                        <span class="text-sm text-gray-500">{{ $livraisons->count() }} demande(s)</span>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60">
                        <div class="overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom & prénom</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Véhicule</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Expérience</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Zone</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">État</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse ($livraisons as $livraison)
                                            <tr class="hover:bg-blue-50/50 transition-colors duration-150">
                                                <td class="px-6 py-4">
                                                    <a href="{{ route('livraison.show', $livraison->id) }}" class="text-gray-600 hover:underline">
                                                        {{ $livraison->user->name }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4">{{ $livraison->vehicle }}</td>
                                                <td class="px-6 py-4">{{ $livraison->experience }}</td>
                                                <td class="px-6 py-4">{{ $livraison->zone }}</td>
                                                <td class="px-6 py-4">
                                                    <x-status-badge :status="$livraison->etat" />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-8 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <i class="fas fa-inbox text-4xl text-gray-400 mb-3"></i>
                                                        <p class="text-gray-500">Aucune demande de livraison disponible</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>

            {{-- Section PSAP --}}
            @elseif ($activeTab === 'psaps')
                <div class="space-y-6" x-show="true" x-transition>
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">Liste des demandes PSAP</h2>
                        <span class="text-sm text-gray-500">{{ $psaps->count() }} demande(s)</span>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60">
                        <div class="overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom & prénom</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Expérience</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Continent</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Localité</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">État</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse ($psaps as $psap)
                                            <tr class="hover:bg-blue-50/50 transition-colors duration-150">
                                                <td class="px-6 py-4">
                                                    <a href="{{ route('psap.show', $psap->id) }}" class="text-gray-600 hover:underline">
                                                        {{ $psap->user->name }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4">{{ $psap->experience }}</td>
                                                <td class="px-6 py-4">{{ $psap->continent }}</td>
                                                <td class="px-6 py-4">{{ $psap->localite }}</td>
                                                <td class="px-6 py-4">
                                                    <x-status-badge :status="$psap->etat" />
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-8 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <i class="fas fa-headset text-4xl text-gray-400 mb-3"></i>
                                                        <p class="text-gray-500">Aucune demande PSAP disponible</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>

            {{-- Section Dépôts --}}
            @elseif ($activeTab === 'deposits')
                <div class="space-y-6" x-show="true" x-transition>
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">Liste des demandes d'approvisionnement</h2>
                        <span class="text-sm text-gray-500">{{ $deposits->count() }} demande(s)</span>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60">
                        <div class="overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom de l'utilisateur</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">État</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Date de dépôt</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse ($deposits as $deposit)
                                            <tr class="hover:bg-blue-50/50 transition-colors duration-150">
                                                <td class="px-6 py-4">
                                                    <a href="{{ route('deposits.show', $deposit->id) }}" class="text-gray-600 hover:underline">
                                                        {{ $deposit->user->name ?? 'Utilisateur inconnu' }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="font-medium">{{ number_format($deposit->montant, 0, ',', ' ') }}</span>
                                                    <span class="text-gray-500 ml-1">FCFA</span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <x-status-badge :status="$deposit->statut" />
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-gray-500">{{ $deposit->created_at->diffForHumans() }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-8 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <i class="fas fa-wallet text-4xl text-gray-400 mb-3"></i>
                                                        <p class="text-gray-500">Aucune demande d'approvisionnement disponible</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>

            {{-- Section Retrait --}}
            @elseif ($activeTab === 'retrait')
                <div class="space-y-6" x-show="true" x-transition>
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">Liste des demandes de retrait</h2>
                        <span class="text-sm text-gray-500">0 demande(s)</span>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60">
                        <div class="overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50/50">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Nom de l'utilisateur</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Montant</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">État</th>
                                            <th scope="col" class="px-6 py-4 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Date de demande</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse ($retraits as $retrait)
                                            <tr class="hover:bg-blue-50/50 transition-colors duration-150">
                                                <td class="px-6 py-4">
                                                    <a href="{{ route('retrait.show', $retrait->id) }}" class="text-gray-600 hover:underline">
                                                        {{ $retrait->user->name ?? 'Utilisateur inconnu' }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="font-medium">{{ number_format($retrait->amount, 0, ',', ' ') }}</span>
                                                    <span class="text-gray-500 ml-1">FCFA</span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <x-status-badge :status="$retrait->status" />
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-gray-500">{{ $retrait->created_at->diffForHumans() }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-8 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <i class="fas fa-wallet text-4xl text-gray-400 mb-3"></i>
                                                        <p class="text-gray-500">Aucune demande de retrait disponible</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            @endif
        </div>
    </div>
</div>