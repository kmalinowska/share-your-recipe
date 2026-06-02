<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User dashboard & Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-3 lg:gap-8 space-y-6 lg:space-y-0">

                <div class="space-y-6 lg:col-span-1">
                    <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg border border-base-200">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg border border-base-200">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg border border-base-200">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg border border-base-200">
                        @include('profile.partials.my-recipes-form')
                    </div>

                    <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg border border-base-200">
                        @include('profile.partials.my-comments-form')
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
