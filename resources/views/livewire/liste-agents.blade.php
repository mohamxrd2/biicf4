<div wire:poll.10ms>

    <table class="w-full mt-5 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 ">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>

                <th scope="col" class="px-6 py-3">
                    Nom
                </th>
                <th scope="col" class="px-6 py-3">
                    Telephone
                </th>

                <th scope="col" class="px-6 py-3">
                    Client
                </th>
                <th scope="col" class="px-6 py-3">
                    Status
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
        </thead>

        <tbody>

            @if ($totalAgents == 0)
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center">
                        <div class="flex flex-col justify-center items-center h-40 w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" class="w-12 h-12 text-gray-500 dark:text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <h1 class="text-xl text-gray-500 dark:text-gray-400">Aucun Agent</h1>
                        </div>
                    </td>
                </tr>
            @else
                @foreach ($agents as $agent)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                        <th scope="row"
                            class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                            <a href="{{ route('agent.show', ['username' => $agent->username]) }}"
                                class="flex items-center">

                                <img class="w-10 h-10 rounded-full" src="{{ asset($agent->photo) }}" alt="Jese image">
                                <div class="ps-3">
                                    <div class="text-base font-semibold">{{ $agent->name }}</div>
                                    <div class="font-normal text-gray-500">{{ $agent->username }}</div>
                                </div>

                            </a>
                        </th>
                        <td class="px-6 py-4">
                            {{ $agent->phonenumber }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $agent->userCount }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-{{ $agent->last_seen >= now()->subMinutes(2) ? 'green' : 'red' }}-500 text-white py-1 px-3 rounded-md">
                                {{ $agent->last_seen >= now()->subMinutes(2) ? 'Online' : 'Offline' }}
                            </span>

                        </td>
                        <td class="px-6 py-4">

                            <div class="flex">
                                <div>
                                    <form action="{{ route('admin.agent.isban', $agent->id) }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" title="{{ $agent->isban ? 'Débloquer' : 'Bloquer' }}">
                                            @if ($agent->isban)
                                                <div class="text-yellow-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </button>
                                    </form>
                                </div>

                                <a href="#" data-hs-overlay="#hs-delete-{{ $agent->id }}"
                                    class="font-medium text-red-600 dark:text-blue-500 mr-2">

                                    <button type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </a>
                                <!-- Overlay -->

                                <!-- End Overlay -->
                            </div>
                        </td>

                    </tr>
                @endforeach


            @endif


        </tbody>
    </table>

    <div class="my-5 flex justify-end">
        {{ $agents->links('vendor.livewire.tailwind') }}
    </div>

</div>
</div>

</div>
