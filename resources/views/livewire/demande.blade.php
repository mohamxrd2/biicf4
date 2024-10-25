<div wire:poll.15000ms>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <div class="mb-6 w-full">
        <h1 class=" text-center font-bold text-2xl">Liste de demande </h1>
    </div>

    <h2 class="text-xl font-semibold mb-4">Liste des demandes Livraisons</h2>

    <div class="flex flex-col border border-gray-200 rounded-xl mb-8">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nom & prénom
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Véhicule
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Expérience
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Disponibilité
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">État</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @if ($livraisons->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-sm text-center text-gray-800">
                                        Aucun élément
                                    </td>
                                </tr>
                            @else
                                @foreach ($livraisons as $livraison)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                            <a href="{{ route('livraison.show', $livraison->id) }}" class="hover:underline">
                                                {{ $livraison->user->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $livraison->vehicle }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $livraison->experience }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $livraison->availability }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            @if ($livraison->etat == 'En cours')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-yellow-800 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-200">{{ $livraison->etat }}</span>
                                            @elseif ($livraison->etat == 'Accepté')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100 dark:text-green-400 dark:bg-green-200">{{ $livraison->etat }}</span>
                                            @elseif ($livraison->etat == 'Refusé')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-red-800 bg-red-100 dark:text-red-400 dark:bg-red-200">{{ $livraison->etat }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-gray-800 bg-gray-100 dark:text-gray-400 dark:bg-gray-200">{{ $livraison->etat }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <h2 class="text-xl font-semibold mb-4">Liste des demandes PSAP</h2>

    <div class="flex flex-col border border-gray-200 rounded-xl mb-8">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nom & prénom
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Expérience
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Continent
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Localité
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">État</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @if ($psaps->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-sm text-center text-gray-800">
                                        Aucun élément
                                    </td>
                                </tr>
                            @else
                                @foreach ($psaps as $psap)
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                            <a href="{{ route('psap.show', $psap->id) }}" class="hover:underline">
                                                {{ $psap->user->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $psap->experience }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $psap->continent }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            {{ $psap->localite }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            @if ($psap->etat == 'En cours')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-yellow-800 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-200">{{ $psap->etat }}</span>
                                            @elseif ($psap->etat == 'Accepté')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-green-800 bg-green-100 dark:text-green-400 dark:bg-green-200">{{ $psap->etat }}</span>
                                            @elseif ($psap->etat == 'Refusé')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-red-800 bg-red-100 dark:text-red-400 dark:bg-red-200">{{ $psap->etat }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-none text-gray-800 bg-gray-100 dark:text-gray-400 dark:bg-gray-200">{{ $psap->etat }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-xl font-semibold mb-4">Liste des demandes d'approvisionnement</h2>

    




</div>
