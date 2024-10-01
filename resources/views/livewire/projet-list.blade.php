<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}

    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Liste des projets</h1>
    
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-md sm:rounded-lg">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                          Nom du Projet
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                          Montant
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                          Type de Financement
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                          Utilisateur
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                          Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                          Action
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      @forelse($projets as $projet)
                      <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 hover:underline">
                            <a href="{{ route('projet.details', $projet->id) }}"  >
                                {{ $projet->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                          {{ number_format($projet->montant, 0, ',', ' ') }} CFA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                          {{ $projet->type_financement }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                          {{ $projet->demandeur->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                          <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $projet->statut == 'en attente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($projet->statut) }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <button type="button" class="text-red-600 hover:text-red-800 font-semibold focus:outline-none">
                            Supprimer
                          </button>
                        </td>
                      </tr>
                      @empty

                      <tr>
                        <td colspan="6" class="px-6 py-4 text-center">
                            <div class="flex flex-col justify-center items-center h-40 w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun Projet</h1>
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
          
    </div>
    
</div>
