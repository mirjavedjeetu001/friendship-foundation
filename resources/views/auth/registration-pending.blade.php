<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Pending - {{ $appSettings->app_name ?? 'Allied Group' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 p-8">
            <div class="w-20 h-20 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-4">Registration Submitted!</h1>
            <p class="text-gray-400 mb-6">Your registration is pending approval. You will receive an email notification once your account is approved by the administrator.</p>
            <div class="bg-gray-700/50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-300">Please keep your email inbox checked. The approval process usually takes 1-2 business days.</p>
            </div>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-500 transition">Back to Login</a>
        </div>
        <div class="text-center mt-6">
            <p class="text-gray-500 text-xs">Developed by <span class="font-medium text-gray-400">Mir Javed Jeetu</span> | <a href="tel:01811480222" class="text-gray-400 hover:text-indigo-400">01811480222</a></p>
        </div>
    </div>
</body>
</html>
