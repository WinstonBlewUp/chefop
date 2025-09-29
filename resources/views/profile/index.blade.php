{{-- resources/views/profile/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Informations de profil --}}
            <div class="p-6 bg-white shadow sm:rounded-lg">
                @include('partials.update-profile-information-form', ['user' => $user ?? auth()->user()])
            </div>

            {{-- Changer le mot de passe --}}
            <div class="p-6 bg-white shadow sm:rounded-lg">
                @include('partials.update-password-form')
            </div>

            {{-- Supprimer le compte --}}
            <div class="p-6 bg-white shadow sm:rounded-lg">
                @include('partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>