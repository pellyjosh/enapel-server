@extends('layouts.app')

@section('title', 'License Required')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white rounded-2xl shadow-lg max-w-lg w-full p-10 text-center">

            {{-- Icon --}}
            <div class="flex items-center justify-center w-20 h-20 rounded-full bg-red-100 mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">License Required</h1>

            @if (session('license_message'))
                <p class="text-red-500 font-medium mb-4">{{ session('license_message') }}</p>
            @endif

            <p class="text-gray-500 mb-6 text-sm leading-relaxed">
                This terminal does not have a valid active license. Please enter your license key,
                or contact your system administrator.
            </p>

            {{-- License Key Configuration Form --}}
            <form method="POST" action="{{ route('license.configure') }}" class="mb-6">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="license_key" placeholder="XXXXXXXXXX-XXXXX"
                        value="{{ old('license_key', config('license.key')) }}"
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none font-mono uppercase"
                        required />
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                        Activate
                    </button>
                </div>
                @error('license_key')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                @enderror
            </form>

            <p class="text-xs text-gray-400">
                Don't have a license?
                <a href="{{ config('license.cloud_url', 'https://cloud.enapel.com') }}/registration" target="_blank"
                    class="text-blue-500 hover:underline">
                    Register on enapel-cloud →
                </a>
            </p>

            <div class="mt-6 pt-6 border-t border-gray-100 text-xs text-gray-400 space-y-1">
                <p>Terminal ID: <span class="font-mono">{{ config('license.terminal_id', 'Not set') }}</span></p>
                @if (session('license_reason'))
                    <p>Reason: <span class="font-mono">{{ session('license_reason') }}</span></p>
                @endif
            </div>

        </div>
    </div>
@endsection
