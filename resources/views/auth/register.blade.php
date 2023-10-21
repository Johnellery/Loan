@php
    $branches = \App\Models\Branch::all();
@endphp



@include('layouts.header')

<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('storage/dashboardpage/logo.png') }}" alt="Bisikleta" class="logo h-8 w-auto">
                    <span class="text-yellow-500 text-xl ml-2">Bisikleta Bike Shop</span>
                </a>
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mt-4">
                <x-label for="first" value="First name" />
                <x-input id="first" class="block mt-1 w-full" type="text" name="first" :value="old('first')" required autofocus autocomplete="first" />
            </div>
            <div class="mt-4">
                <x-label for="middle" value="Middle name" />
                <x-input id="middle" class="block mt-1 w-full" type="text" name="middle" :value="old('middle')" required autofocus autocomplete="middle" />
            </div>
            <div class="mt-4">
                <x-label for="last" value="Last name" />
                <x-input id="last" class="block mt-1 w-full" type="text" name="last" :value="old('last')" required autofocus autocomplete="last" />
            </div>

            <div class="mt-4">
                <x-label for="name" value="User name" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="Email Address" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>
            <div class="mt-4">
                <x-label for="branch_id" value="Branch" />
                <select id="branch_id" name="branch_id" class="block mt-1 w-full">
                    <option value="">Select a Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="hidden">
                <input type="hidden" name="role_id" value="4">
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

