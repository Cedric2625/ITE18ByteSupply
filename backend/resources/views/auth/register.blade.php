@extends('layouts.app')

@section('title', 'Register')

@section('header')
	Register
@endsection

@section('content')
	<form method="POST" action="{{ route('auth.register') }}" class="space-y-6" x-data="{ showPassword: false, showConfirm:false }">
		@csrf
		
		<!-- Success Message -->
		@if (session('success'))
			<div class="bg-green-100 border-2 border-green-400 text-green-800 px-4 py-3 rounded-md shadow-md">
				<div class="flex items-center">
					<i class="fas fa-check-circle mr-2 text-lg"></i>
					<span class="font-semibold">{{ session('success') }}</span>
				</div>
			</div>
		@endif

		<!-- Error Message -->
		@if (session('error'))
			<div class="bg-red-100 border-2 border-red-400 text-red-800 px-4 py-3 rounded-md shadow-md">
				<div class="flex items-center">
					<i class="fas fa-exclamation-circle mr-2 text-lg"></i>
					<span class="font-semibold">{{ session('error') }}</span>
				</div>
			</div>
		@endif

		<div>
			<label class="block text-sm font-medium text-gray-700">Name</label>
			<input type="text" name="buyer_name" value="{{ old('buyer_name') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
			@error('buyer_name')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700">Email</label>
			<input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
			@error('email')
				<p class="mt-1 text-sm text-red-600">
					@if (str_contains($message, 'taken') || str_contains($message, 'already'))
						This email is already registered. Please login instead or use a different email.
					@else
						{{ $message }}
					@endif
				</p>
			@enderror
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700">Password</label>
			<div class="relative">
				<input
					:type="showPassword ? 'text' : 'password'"
					name="password"
					class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
					required
				>
				<button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility">
					<i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
				</button>
			</div>
			@error('password')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700">Confirm Password</label>
			<div class="relative">
				<input
					:type="showConfirm ? 'text' : 'password'"
					name="password_confirmation"
					class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
					required
				>
				<button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700" aria-label="Toggle confirm password visibility">
					<i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
				</button>
			</div>
			@error('password_confirmation')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>
		<div class="flex items-center justify-between">
			<button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md">Register</button>
			<a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">Back to Login</a>
		</div>
	</form>
@endsection


