@extends('layouts.app')

@section('title', 'Login')

@section('header', 'Login')

@section('content')
	<div class="max-w-md mx-auto" x-data="{ role: '{{ old('role','buyer') }}', showPassword: false }">
		@if(session('error'))
			<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
				<span class="block sm:inline">{{ session('error') }}</span>
			</div>
		@endif

		@if(session('success'))
			<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
				<span class="block sm:inline">{{ session('success') }}</span>
			</div>
		@endif

		<form action="{{ route('auth.login') }}" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow">
            @csrf

            <div>
                <x-label for="role" value="Login as" />
                <div class="mt-2 flex items-center space-x-4">
                    <label class="inline-flex items-center">
						<input type="radio" name="role" value="buyer" class="form-radio" x-model="role" {{ old('role','buyer')==='buyer' ? 'checked' : '' }}>
                        <span class="ml-2">Customer</span>
                    </label>
                    <label class="inline-flex items-center">
						<input type="radio" name="role" value="admin" class="form-radio" x-model="role" {{ old('role')==='admin' ? 'checked' : '' }}>
                        <span class="ml-2">Admin</span>
                    </label>
                </div>
            </div>

			<div>
				<label for="username" class="block text-sm font-medium text-gray-700">
					<span x-text="role === 'admin' ? 'Username' : 'Email'"></span>
				</label>
				<input
					id="username"
					name="username"
					:type="role === 'admin' ? 'text' : 'email'"
					:placeholder="role === 'admin' ? 'Username' : 'Email'"
					class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
					required
					value="{{ old('username', request('email')) }}"
				/>
				@error('username')
					<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

            <div>
                <x-label for="password" value="Password" />
				<div class="relative">
					<input
						id="password"
						name="password"
						:type="showPassword ? 'text' : 'password'"
						class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
						required
					/>
					<button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility">
						<i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
					</button>
				</div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

			<div class="flex items-center justify-between">
				<a href="{{ route('auth.register.form') }}" class="text-sm text-indigo-600 hover:text-indigo-800" x-show="role === 'buyer'">Don't have an account? Sign In</a>
				<x-button>Login</x-button>
			</div>

			<div class="flex items-center justify-between">
				<a href="{{ route('password.forgot') }}" class="text-sm text-gray-600 hover:text-gray-800">Forgot Password?</a>
				<a href="{{ route('oauth.google.login') }}" class="inline-flex items-center bg-white border px-3 py-2 rounded shadow text-sm" x-show="role === 'buyer'">
					<i class="fab fa-google text-red-500 mr-2"></i> Sign in with Google
				</a>
			</div>
        </form>
    </div>
@endsection


