<div wire:poll.15000ms class="lg:flex 2xl:gap-6 gap-6  mx-auto" id="js-oversized" >
    <div class="flex-1 mx-auto  ">
        <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6">


            @include('admin.components.chartcard', [
                'bgcolor' => 'black',
                'title' => 'Budget',
                'tooltip' => 'Budget totale',
                'amount' => '5,572,540 FCFA',
                'chart' => '12.5',
                'svgPath' =>
                    '<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>',
            ])
            @include('admin.components.chartcard', [
                'bgcolor' => 'white',
                'title' => 'Client',
                'tooltip' => 'Nombre totale client',
                'amount' => $totalClients,
                'chart' => '1.5',
                'svgPath' => '<svg class="flex-shrink-0 size-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24"
                                                                                                                                                                                                                                                                                                                                                                                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                                                                                                                                                                                                                                                                                                                                                                            stroke-linecap="round" stroke-linejoin="round">
                                                                                                                                                                                                                                                                                                                                                                                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                                                                                                                                                                                                                                                                                                                                                                            <circle cx="9" cy="7" r="4" />
                                                                                                                                                                                                                                                                                                                                                                                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                                                                                                                                                                                                                                                                                                                                                                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                                                                                                                                                                                                                                                                                                                                                                        </svg>',
            ])
            <!-- End Card -->

            @include('admin.components.chartcard', [
                'bgcolor' => 'white',
                'title' => 'Produits',
                'tooltip' => 'Nombre totale produit',
                'amount' => $totalProducts,
                'chart' => '11.5',
                'svgPath' => '<svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400"
                                                                                                                                                                                                                                                                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                                                                                                                                                                                                                                                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                                                                                                                                                                                                                                                    stroke-linejoin="round">

                                                                                                                                                                                                                                                                                                    <path d="M5 22h14" />

                                                                                                                                                                                                                                                                                                    <path d="M5 2h14" />

                                                                                                                                                                                                                                                                                                    <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22" />

                                                                                                                                                                                                                                                                                                    <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2" />

                                                                                                                                                                                                                                                                                                    <</svg>',
            ])

            @include('admin.components.chartcard', [
                'bgcolor' => 'white',
                'title' => 'Services',
                'tooltip' => 'Nombre totale services',
                'amount' => $totalServices,
                'chart' => '11.5',
                'svgPath' => '<svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                                                                                                                                                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                                                                                                                                                                                                                                                                                              </svg>',
            ])

        </div>


        <div class="mt-10 relative overflow-x-auto  sm:rounded-lg">

            <div class="flex justify-between w-full mb-4 ">
                <p class="text-xl font-bold">Client recent</p>

                <a href="{{ route('admin.client') }}" class="font-bold text-blue-500">Voir plus

                </a>
            </div>

            <table class="w-full text-sm border text-left  rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700  uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>

                        <th scope="col" class="px-6 py-3">Nom</th>
                        <th scope="col" class="px-6 py-3">Téléphone</th>
                        <th scope="col" class="px-6 py-3">Agent</th>
                        <th scope="col" class="px-6 py-3">Statut</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($totalClients == 0)
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center">
                                <div class="flex flex-col justify-center items-center h-40 w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun utilisateur
                                    </h1>
                                </div>
                            </td>
                        </tr>
                    @else
                        @foreach ($users as $user)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                    <a href="{{ route('client.show', ['username' => $user->username]) }}"
                                        class="flex items-center">
                                        <img class="w-10 h-10 rounded-full" src="{{ asset($user->photo) }}"
                                            alt="">
                                        <div class="ml-3">
                                            <div class="text-base font-semibold">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->username }}</div>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4">{{ $user->phone }}</td>

                                <td class="px-6 py-4">
                                    @if ($user->admin)
                                        <a href="{{ route('agent.show', ['username' => $user->admin->username]) }}"
                                            class="flex items-center">
                                            {{ $user->admin->name ?? 'N/A' }}
                                        </a>
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="bg-{{ $user->last_seen >= now()->subMinutes(2) ? 'green' : 'red' }}-500 text-white py-1 px-3 rounded-md">
                                        {{ $user->last_seen >= now()->subMinutes(2) ? 'Online' : 'Offline' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex">
                                        <a href="#" data-hs-overlay="#hs-delete-{{ $user->id }}"
                                            class="mr-2 font-medium text-red-600 dark:text-blue-500">
                                            <button type="submit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>


        </div>
    </div>
    <div class="flex-2  lg:w-[350px] ">

        <div class="lg:space-y-4 lg:pb-8 sm:grid-cols-2 max-lg:gap-6 sm:mt-10 lg:mt-0"
            uk-sticky="media: 1024; end: #js-oversized; offset: 80">

            <a href="{{ route('clients.create') }}"
                class="w-full p-5 bg-black border flex items-center rounded-2xl hover:bg-gray-900 mb-4">
                <div class="rounded-full w-8 h-8 bg-gray-200 flex items-center justify-center mr-5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <p class="font-bold text-white">Ajouter</p>
                    <p class="text-sm text-white">Ajouter un client</p>
                </div>
            </a>
            <a href="{{ route('admin.agent') }}"
                class="w-full p-5 bg-white border flex items-center rounded-2xl hover:bg-gray-50 mb-4">
                <div class="rounded-full w-8 h-8 bg-gray-200 flex items-center justify-center mr-5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <p class="font-bold">Ajouter</p>
                    <p class="text-sm">Ajouter un agent</p>
                </div>
            </a>



            <div class="flex flex-col  p-5 bg-white border   rounded-2xl w-full ">
                <p class="font-bold text-left mb-3">Nombre d'agent</p>
                <div class="flex items-center">
                    <h1 class="font-bold text-2xl mr-4">{{ $agentCount }}</h1>

                    <div class="flex -space-x-4 rtl:space-x-reverse">

                        @if ($agentCount == 0)
                            <p class="text-gray-600">Aucun agent</p>
                        @else
                            @foreach ($agents->take(5) as $agent)
                                <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800"
                                    src="{{ asset($agent->photo) }}" alt="">
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="w-full border flex flex-col rounded-2xl p-5 ">

                <div class="flex items-center justify-between w-full mb-2">
                    <h3 class="text-md font-bold">Transactions</h3>

                    <a href="{{ route('admin.porte-feuille') }}" class=" font-bold text-blue-500 ">voir
                        plus</a>

                </div>


                <div class="flex items-center justify-between w-full p-3 rounded-md hover:bg-gray-100">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-md bg-gray-200 p-2 mr-2 flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                            </svg>


                        </div>
                        <p class="text-sm font-medium text-gray-900">Charle</p>

                    </div>

                    <p class="text-sm font-blod text-green-400 ">+ $1000</p>
                </div>
                <div class="flex items-center justify-between w-full p-3 rounded-md hover:bg-gray-100">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-md bg-gray-200 p-2 mr-2 flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>



                        </div>
                        <p class="text-sm font-medium text-gray-900">Charle</p>

                    </div>
                    <p class="text-sm font-medium text-red-400">- $1000</p>
                </div>
                <div class="flex items-center justify-between w-full p-3 rounded-md hover:bg-gray-100">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-md bg-gray-200 p-2 mr-2 flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                            </svg>


                        </div>
                        <p class="text-sm font-medium text-gray-900">Charle</p>

                    </div>
                    <p class="text-sm font-medium text-green-400">+ $1000</p>
                </div>
                <div class="flex items-center justify-between w-full p-3 rounded-md hover:bg-gray-100">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-md bg-gray-200 p-2 mr-2 flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                            </svg>


                        </div>
                        <p class="text-sm font-medium text-gray-900">Charle</p>

                    </div>
                    <p class="text-sm font-medium text-green-400">+ $1000</p>
                </div>
                <div class="flex items-center justify-between w-full p-3 rounded-md hover:bg-gray-100">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-md bg-gray-200 p-2 mr-2 flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Charle</p>

                    </div>
                    <p class="text-sm font-medium text-red-400">- $1000</p>
                </div>



            </div>
        </div>
    </div>
</div>
