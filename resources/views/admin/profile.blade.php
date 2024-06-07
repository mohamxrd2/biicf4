@extends('admin.layout.navside')

@section('title', 'Profile')

@section('content')

    <div class="w-full p-6 bg-white rounded-lg shadow-md dark:bg-neutral-800">
        @if (session('success'))
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded-md mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="flex md:gap-8 gap-4 items-center md:p-8 p-6 md:pb-4">
            <form action="{{ route('admin.updateProfilePhoto', ['admin' => $admin->id]) }}" method="post"
                enctype="multipart/form-data" id="photo-upload-form">
                @csrf
                @method('PUT')
                <div class="relative md:w-20 md:h-20 w-12 h-12 shrink-0">
                    <label for="file-upload1" class="cursor-pointer">
                        <img id="img" src="{{ asset($admin->photo) }}"
                            class="object-cover w-full h-full rounded-full" alt="User photo" />
                        <input type="file" id="file-upload1" name="image" class="hidden"
                            onchange="previewImageAndSubmit(this)" />
                        <img id="image-preview1" class="absolute inset-0 w-full h-full object-cover rounded-full hidden">
                    </label>
                    <label for="file-upload1"
                        class="md:p-1 p-0.5 rounded-full bg-slate-600 md:border-4 border-white absolute -bottom-2 -right-2 cursor-pointer dark:border-slate-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="md:w-4 md:h-4 w-3 h-3 fill-white">
                            <path d="M12 9a3.75 3.75 0 100 7.5A3.75 3.75 0 0012 9z" />
                            <path fill-rule="evenodd"
                                d="M9.344 3.071a49.52 49.52 0 015.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a3 3 0 01-3 3h-15a3 3 0 01-3-3V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 001.11-.71l.822-1.315a2.942 2.942 0 012.332-1.39zM6.75 12.75a5.25 5.25 0 1110.5 0 5.25 5.25 0 01-10.5 0zm12-1.5a.75.75 0 100-1.5.75.75 0 000 1.5z"
                                clip-rule="evenodd" />
                        </svg>
                    </label>
                </div>
            </form>




            <div class="flex-1">
                <h3 class="md:text-xl text-base font-semibold text-black dark:text-white">{{ $admin->name }}</h3>
                <p class="text-sm text-blue-600 mt-1 font-normal">{{ '@' . $admin->username }}</p>
            </div>


        </div>
        <div class="border-b border-gray-200 px-4 dark:border-neutral-700">
            <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                <button type="button"
                    class="hs-tab-active:font-semibold hs-tab-active:border-blue-600  hs-tab-active:text-blue-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-blue-500 active"
                    id="basic-tabs-item-1" data-hs-tab="#basic-tabs-1" aria-controls="basic-tabs-1" role="tab">
                    Information personnel
                </button>
                <button type="button"
                    class="hs-tab-active:font-semibold hs-tab-active:border-blue-600 hs-tab-active:text-blue-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-blue-500"
                    id="basic-tabs-item-2" data-hs-tab="#basic-tabs-2" aria-controls="basic-tabs-2" role="tab">
                    Modifier profile
                </button>
                <button type="button"
                    class="hs-tab-active:font-semibold hs-tab-active:border-blue-600 hs-tab-active:text-blue-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm whitespace-nowrap text-gray-500 hover:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-blue-500"
                    id="basic-tabs-item-3" data-hs-tab="#basic-tabs-3" aria-controls="basic-tabs-3" role="tab">
                    Modifier mot de passe
                </button>
            </nav>
        </div>

        <div class="mt-3 p-4">
            <div id="basic-tabs-1" role="tabpanel" aria-labelledby="basic-tabs-item-1">
                <div class="flex my-5 gap-10">
                    <h5 class="w-40 text-right text-gray-500 text-xs dark:text-white/80">Nom</h5>
                    <div class=" text-blue-500 text-xs">
                        {{ $admin->name }}
                    </div>
                </div>
                <div class="flex  my-5 gap-10">
                    <h5 class="w-40 text-right text-gray-500 text-xs dark:text-white/80">Username</h5>
                    <div class=" text-blue-500 text-xs">
                        {{ $admin->username }}
                    </div>
                </div>

                <div class="flex my-5 gap-10">
                    <h5 class="w-40 text-right text-gray-500 text-xs dark:text-white/80">Numero de telephone</h5>
                    <div class=" text-blue-500 text-xs">
                        {{ $admin->phonenumber }}
                    </div>
                </div>
            </div>
            <div id="basic-tabs-2" class="hidden" role="tabpanel" aria-labelledby="basic-tabs-item-2">
                <form method="POST" action="{{ route('admin.updateProfile', ['admin' => $admin->id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="md:flex items-center gap-10 my-4">
                        <label class="md:w-32 text-right text-gray-500 text-xs dark:text-white/80"> Nom </label>
                        <div class="max-w-sm space-y-3">
                            <input type="text" name="name" value="{{ $admin->name }}"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                        </div>
                    </div>

                    <div class="md:flex items-center gap-10 my-4">
                        <label class="md:w-32 text-right text-gray-500 text-xs dark:text-white/80"> Username </label>
                        <div class="max-w-sm space-y-3">
                            <input type="text" name="username" value="{{ $admin->username }}"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                        </div>
                    </div>

                    <div class="md:flex items-center gap-10 my-4">
                        <label class="md:w-32 text-right text-gray-500 text-xs dark:text-white/80"> Téléphone </label>
                        <div class="max-w-sm space-y-3">
                            <input type="text" name="phonenumber" value="{{ $admin->phonenumber }}"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-5">
                        <button type="reset" class="button px-6 py-1 bg-gray-200 rounded-md"> Annuler</button>
                        <button type="submit" class="button px-6 py-1 bg-blue-500 text-white rounded-md">
                            Enregistrer</button>
                    </div>
                </form>
            </div>

            <div id="basic-tabs-3" class="hidden" role="tabpanel" aria-labelledby="basic-tabs-item-3">
                <form method="POST" action="{{ route('admin.updatePassword', ['admin' => $admin->id]) }}">
                    @csrf
                    @method('PUT')

                    <div id="basic-tabs-3" role="tabpanel" aria-labelledby="basic-tabs-item-3">
                        <div class="md:flex items-center gap-10 my-4">
                            <label class="md:w-40 text-right text-xs dark:text-white/80"> Mot de passe actuel </label>
                            <div class="max-w-sm space-y-3">
                                <input type="password" name="current_password" placeholder="******"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            </div>
                        </div>

                        <div class="md:flex items-center gap-10 my-4">
                            <label class="md:w-40 text-right text-xs dark:text-white/80"> Nouveau mot de passe</label>
                            <div class="max-w-sm space-y-3">
                                <input type="password" name="new_password" placeholder="******"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            </div>
                        </div>

                        <div class="md:flex items-center gap-10 my-4">
                            <label class="md:w-40 text-right text-xs dark:text-white/80"> Confirmer mot de passe </label>
                            <div class="max-w-sm space-y-3">
                                <input type="password" name="new_password_confirmation" placeholder="******"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-5">
                            <button type="reset" class="button px-6 py-1 bg-gray-200 rounded-md"> Annuler</button>
                            <button type="submit" class="button px-6 py-1 bg-blue-500 text-white rounded-md">
                                Enregistrer</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script>
        function previewImageAndSubmit(input) {
            const preview = document.getElementById('image-preview1');
            const file = input.files[0];
            const reader = new FileReader();
            const form = document.getElementById('photo-upload-form');

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                form.submit();  // Submit the form after the image is loaded
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        }

        function removeImage() {
            const preview = document.getElementById('image-preview1');
            const fileInput = document.getElementById('file-upload1');

            preview.src = '';
            preview.classList.add('hidden');
            fileInput.value = ''; // Clear the file input
        }

        function validateForm() {
            const fileInput = document.getElementById('file-upload1');
            if (!fileInput.value) {
                alert('Please select an image before submitting.');
                return false;
            }
            return true;
        }
    </script>




@endsection
