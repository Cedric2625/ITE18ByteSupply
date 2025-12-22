@extends('layouts.app')

@section('title', 'Verify Code')
@section('header', 'Reset Password')

@section('content')
	<div class="max-w-md mx-auto">
		<div class="bg-white p-6 rounded shadow">
			<h3 class="text-lg font-semibold mb-4">Enter the 6-digit code</h3>
			<p class="text-sm text-gray-600 mb-4">We sent a code to <strong>{{ $email }}</strong>. Check your inbox.</p>
			<form action="{{ route('password.otp.verify') }}" method="POST" class="space-y-4">
				@csrf
				<input type="hidden" name="email" value="{{ $email }}">
				<div>
					<input name="code" maxlength="6" minlength="6" class="mt-1 block w-full border rounded p-2 text-center tracking-widest text-xl" placeholder="______" required>
					@error('code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
				</div>
				<button class="bg-indigo-600 text-white px-4 py-2 rounded">Confirm Reset Password</button>
			</form>
		</div>
	</div>
@endsection

