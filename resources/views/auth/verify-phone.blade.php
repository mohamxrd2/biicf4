<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vérification</title>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">
        @if ($errors->any())
            <div class="m-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                <strong class="font-bold">Attention!</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li class="list-disc list-inside">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">
            <h2 class="text-2xl font-bold mb-6">OTP Verification</h2>
            <p class="text-gray-500 mb-4">Code has been sent to ****45</p>

            <!-- Laravel Form for OTP verification -->
            <form method="POST" action="{{ route('verify.phone.code') }}">
                @csrf

                <!-- OTP input fields (each input for one digit) -->
                <div class="flex justify-center space-x-2 sm:space-x-4 mb-6">
                    <input id="verification_code_1" type="text" maxlength="1" name="verification_code[]"
                        class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required />
                    <input id="verification_code_2" type="text" maxlength="1" name="verification_code[]"
                        class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required />
                    <input id="verification_code_3" type="text" maxlength="1" name="verification_code[]"
                        class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required />
                    <input id="verification_code_4" type="text" maxlength="1" name="verification_code[]"
                        class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required />
                    <input id="verification_code_5" type="text" maxlength="1" name="verification_code[]"
                        class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required />
                    <input id="verification_code_6" type="text" maxlength="1" name="verification_code[]"
                        class="w-10 h-10 sm:w-12 sm:h-12 text-xl sm:text-2xl text-center border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required />
                </div>

                <!-- Hidden field for phone number -->
                <input type="hidden" name="phone" value="{{ $phone ?? old('phone') }}">

                <!-- Error message -->
                @error('verification_code')
                    <span class="text-red-500 text-sm block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <p class="text-gray-500 mb-4">Didn’t get the OTP? <a
                        href="{{ route('resend.otp', ['phone' => $phone]) }}"
                        class="text-purple-500 font-semibold">Resend</a></p>


                <!-- Submit button -->
                <div class="mt-3">
                    <button type="submit"
                        class="w-full bg-purple-500 text-white py-2 px-4 rounded-lg hover:bg-purple-600 transition duration-300">
                        Vérifier
                    </button>
                </div>
            </form>

            <!-- JavaScript for auto focus on next input -->
            <script>
                const inputs = document.querySelectorAll('input[name="verification_code[]"]');

                inputs.forEach((input, index) => {
                    input.addEventListener('input', () => {
                        if (input.value.length === 1 && index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    });

                    // Permet de passer au champ précédent avec la touche Backspace
                    input.addEventListener('keydown', (event) => {
                        if (event.key === "Backspace" && input.value === '' && index > 0) {
                            inputs[index - 1].focus();
                        }
                    });
                });
            </script>


        </div>
    </div>
</body>

</html>
