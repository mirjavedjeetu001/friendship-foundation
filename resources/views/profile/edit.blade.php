<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(Auth::user()->email !== 'alliedgroup@gmail.com')
            <!-- Edit Full Member Information Link -->
            <div class="p-4 sm:p-8 bg-gradient-to-r from-indigo-500 to-purple-600 shadow sm:rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <h2 class="text-lg font-bold">Edit Your Full Information</h2>
                        <p class="text-indigo-100 text-sm mt-1">Update your personal details, NID, nominee, banking information and more</p>
                    </div>
                    <a href="{{ route('profile.member.edit') }}" class="px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-indigo-50 transition shadow-lg">
                        Edit Full Profile
                    </a>
                </div>
            </div>
            @endif

            <!-- Avatar Upload Section -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Profile Picture') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Upload a profile picture to personalize your account.') }}
                            </p>
                        </header>

                        <div class="mt-6 flex items-center gap-6">
                            <!-- Current Avatar -->
                            <div class="shrink-0">
                                <img class="h-24 w-24 object-cover rounded-full border-4 border-indigo-100 dark:border-indigo-900" 
                                     src="{{ $user->avatar_url }}" 
                                     alt="{{ $user->name }}">
                            </div>

                            <div class="flex-1">
                                <!-- Upload Form -->
                                <form method="post" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <div>
                                        <input type="file" 
                                               name="avatar" 
                                               id="avatar" 
                                               accept="image/*"
                                               class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-lg file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-indigo-50 file:text-indigo-700
                                                      dark:file:bg-indigo-900 dark:file:text-indigo-300
                                                      hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800
                                                      cursor-pointer">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG or GIF (Max 2MB)</p>
                                        @error('avatar')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex gap-3">
                                        <button type="submit" 
                                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            {{ __('Upload') }}
                                        </button>
                                    </div>
                                </form>

                                @if($user->avatar)
                                <!-- Remove Avatar -->
                                <form method="post" action="{{ route('profile.avatar.remove') }}" class="mt-3">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" 
                                            class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 underline">
                                        {{ __('Remove Picture') }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>

                        @if (session('status') === 'avatar-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                               class="mt-4 text-sm text-green-600 dark:text-green-400">{{ __('Profile picture updated.') }}</p>
                        @endif

                        @if (session('status') === 'avatar-removed')
                            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                               class="mt-4 text-sm text-green-600 dark:text-green-400">{{ __('Profile picture removed.') }}</p>
                        @endif
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
