<div>
    <!-- Image du projet -->
    <div class="flex flex-col justify-center items-center text-center bg-gray-200 p-4 rounded-lg mb-6">
        <h1 class="text-lg font-bold">FINANCEMENT D'UN PROJET</h1>
    </div>

    <div class="flex flex-col md:flex-row mb-8 w-full overflow-hidden">
        <!-- Images Section -->

        <div class="container mx-auto py-8 space-y-12">

            <!-- Section Projet Images -->
            <div class="flex flex-col w-full md:space-x-8 items-center">
                <!-- Main Image -->
                <div class="relative max-w-md lg:max-w-lg mx-auto shadow-lg rounded-lg overflow-hidden">
                    <img id="mainImage"
                        class="w-full object-cover transition duration-300 ease-in-out transform hover:scale-105"
                        src="{{ asset($images[0]) }}" alt="Main Product Image" />
                </div>


                <!-- Thumbnail Images -->
                <div class="flex justify-center space-x-4">
                    @foreach ($images as $image)
                        @if ($image)
                            <!-- Vérifie si l'image existe -->
                            <img onclick="changeImage('{{ asset($image) }}')"
                                class="w-20 h-20 object-cover cursor-pointer border-2 border-gray-200 rounded-lg transition-transform duration-200 ease-in-out transform hover:scale-105 hover:border-gray-400"
                                src="{{ asset($image) }}" alt="Thumbnail">
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Description</h2>
                <p class="text-gray-800">
                    {{ $projet->description }}
                </p>

            </div>

            {{-- Description du  projet --}}





            <!-- Client Information -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Client Information</h2>
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-600 font-medium">Client Name:</p>
                        <p class="text-gray-800">{{ $userDetails->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Email:</p>
                        <p class="text-gray-800">{{ $userDetails->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Phone Number:</p>
                        <p class="text-gray-800">{{ $userDetails->phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Credit Score:</p>
                        <p class="text-gray-800">{{ $crediScore->ccc }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Address:</p>
                        <p class="text-gray-800">
                            {{ $userDetails->country }}, {{ $userDetails->ville }}, {{ $userDetails->departe }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Credit Request Information -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Credit Request Information</h2>
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-600 font-medium">Requested Amount:</p>
                        <p class="text-gray-800">{{ $notification->data['montant'] }} FCFA</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Credit Duration:</p>
                        <p class="text-gray-800">{{ $joursRestants }} months</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-medium">Credit Rate:</p>
                        <p class="text-gray-800">
                            {{ $projet->taux ?? 'Rate not available' }} %
                        </p>
                    </div>


                </div>
            </div>
        </div>


        <!-- Contenu du projet -->
        <div class="md:px-4 flex flex-col w-full md:w-1/2 py-4">
            <!-- Catégorie du projet -->
            <div class="flex items-center mb-2">

                <span class="ml-2 text-xl capitalize font-semibold text-slate-700">{{ $projet->name }}</span>
            </div>



            <!-- Informations de progression -->

            <div class="mt-4">


                <div
                    class="grid grid-cols-2  gap-4 text-sm border border-gray-300 rounded-lg p-6 shadow-md text-gray-600 mt-4 w-full justify-between">
                    <div class="flex flex-col text-center">
                        <span
                            class="font-semibold text-lg">{{ number_format($notification->data['montant'], 0, ',', ' ') }}
                            FCFA</span>
                        <span class="text-gray-500 text-sm">Montant</span>
                    </div>

                    <!-- Jours Restants -->
                    <div class="flex flex-col text-center">
                        <span
                            class="font-semibold text-lg">{{ $demandeCredit->taux ?? ($projet->taux ?? 'Rate not available') }}%</span>
                        <span class="text-gray-500 text-sm">Taux</span>
                    </div>
                </div>



                <div class="flex py-2 mt-2 items-center">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                        <img class="h-full w-full border-2 border-white rounded-full dark:border-gray-800 object-cover"
                            src="{{ asset($userDetails->photo) }}" alt="">
                    </div>
                    <div class="ml-2 text-sm font-semibold">
                        <span class="font-medium text-gray-500 mr-2">De</span>{{ $userDetails->name }}
                    </div>
                </div>
            </div>
            <div class="mt-4">
                @if (session()->has('success'))
                    <p class="bg-green-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('success') }}</p>
                @endif
                @if (session()->has('error'))
                    <p class="bg-red-500 text-white p-4 rounded-md mt-2 mb-6">{{ session('error') }}</p>
                @endif
                

            </div>

        </div>

    </div>

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }
    </script>
</div>
