<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - {{ config('app.name', 'Allied Group') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Verify OTP</h1>
            <p class="text-gray-400 text-sm mt-1">Enter the 6-digit code sent to your email</p>
            <p class="text-indigo-400 text-sm mt-2">{{ session('password_reset_email') }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 p-6 sm:p-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-900/50 border border-green-700 text-green-400 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-900/50 border border-red-700 text-red-400 rounded-lg text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-6" id="otpForm">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-4 text-center">Enter OTP</label>
                    <div class="flex justify-center gap-2" id="otpInputs">
                        @for($i = 0; $i < 6; $i++)
                            <input type="text" maxlength="1" 
                                class="otp-input bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                data-index="{{ $i }}" inputmode="numeric" pattern="[0-9]">
                        @endfor
                    </div>
                    <input type="hidden" name="otp" id="otpHidden">
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    Verify OTP
                </button>
            </form>

            <div class="mt-6 text-center space-y-3">
                <p class="text-gray-400 text-sm">Didn't receive the code?</p>
                <form method="POST" action="{{ route('password.otp.resend') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">
                        Resend OTP
                    </button>
                </form>
                <div>
                    <a href="{{ route('password.otp.request') }}" class="text-sm text-gray-500 hover:text-gray-400">
                        ← Try different email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');
            const hiddenInput = document.getElementById('otpHidden');
            const form = document.getElementById('otpForm');

            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    // Only allow numbers
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    if (this.value && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    updateHiddenInput();
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                    pastedData.split('').forEach((char, i) => {
                        if (inputs[i]) {
                            inputs[i].value = char;
                        }
                    });
                    updateHiddenInput();
                    if (pastedData.length === 6) {
                        inputs[5].focus();
                    }
                });
            });

            function updateHiddenInput() {
                let otp = '';
                inputs.forEach(input => {
                    otp += input.value;
                });
                hiddenInput.value = otp;
            }

            form.addEventListener('submit', function(e) {
                updateHiddenInput();
                if (hiddenInput.value.length !== 6) {
                    e.preventDefault();
                    alert('Please enter complete 6-digit OTP');
                }
            });

            // Focus first input
            inputs[0].focus();
        });
    </script>
</body>
</html>
