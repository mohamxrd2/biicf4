@extends('admin.layout.navside')

@section('title', 'Statistique')

@section('content')

    <div class="flex justify-between">

        <p class="text-2xl text-slate-700 mb-4">Ratache <span class="text-blue-500 capitalize">{{ $user->name }}</span> à un
            agent</p>

        <div>
            <label for="table-search" class="sr-only">Search</label>
            <div class="relative mr-2">
                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" id="searchInput" onkeyup="searchTable()"
                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Rechercher...">
            </div>
        </div>



    </div>

    @if (session('success'))
        <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif




    <table class="w-full mt-5 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 ">

        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>

                <th scope="col" class="px-6 py-3">
                    Nom & prenom
                </th>
                <th scope="col" class="px-6 py-3">
                    Telephone
                </th>

                <th scope="col" class="px-6 py-3">
                    Nombre de client
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
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                class="w-12 h-12 text-gray-500 dark:text-gray-400">
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
                            <a href="{{ route('agent.show', ['username' => $agent->username]) }}" class="flex items-center">

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
                            <form action="{{ route('update.admin', ['username' => $user->username]) }}" method="POST">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="admin_id" value="{{ $agent->id }}">
                                <button type="submit" class="p-1 rounded-md {{ $user->admin && $user->admin->id === $agent->id ? 'bg-gray-200 text-black' : 'bg-blue-500 text-white' }}">
                                    {{ $user->admin && $user->admin->id === $agent->id ? 'Sélectionné' : 'Sélectionner' }}
                                </button>
                                
                            </form>

                        </td>
                    </tr>
                @endforeach


            @endif


        </tbody>
    </table>
    <div id="noResultMessage" class="h-20 flex justify-center items-center" style="display: none;">Aucun résultat
        trouvé.</div>




    <script src="{{ asset('js/search.js') }}"></script>

@endsection
