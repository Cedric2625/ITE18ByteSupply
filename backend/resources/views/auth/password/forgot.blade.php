@extends('layouts.app')

@section('title', 'Reset Password')
@section('header', 'Reset Password')

@section('content')
	<div class="grid grid-cols-1 md:grid-cols-2 gap-8" x-data="{ showOld:false, showNew:false, showConfirm:false }">
		<!-- Option A: Use old password -->
		<div class="bg-blue-100 p-6 rounded">
			<h3 class="text-lg font-semibold mb-4">Reset using your old password</h3>
			<form action="{{ route('password.change.old') }}" method="POST" class="space-y-4">
				@csrf
				<div>
					<label class="block text-sm font-medium text-gray-700">Email (Customer)</label>
					<input name="email" type="email" class="mt-1 block w-full border rounded p-2" required value="{{ old('email') }}">
					@error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">Old password</label>
					<div class="relative">
						<input name="old_password" :type="showOld ? 'text' : 'password'" class="mt-1 block w-full pr-10 border rounded p-2" required>
						<button type="button" @click="showOld = !showOld" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700" aria-label="Toggle old password visibility">
							<i class="fas" :class="showOld ? 'fa-eye-slash' : 'fa-eye'"></i>
						</button>
					</div>
					@error('old_password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">New password</label>
					<div class="relative">
						<input name="password" :type="showNew ? 'text' : 'password'" class="mt-1 block w-full pr-10 border rounded p-2" required>
						<button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700" aria-label="Toggle new password visibility">
							<i class="fas" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
						</button>
					</div>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">Confirm password</label>
					<div class="relative">
						<input name="password_confirmation" :type="showConfirm ? 'text' : 'password'" class="mt-1 block w-full pr-10 border rounded p-2" required>
						<button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700" aria-label="Toggle confirm password visibility">
							<i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
						</button>
					</div>
				</div>
				<button class="bg-indigo-600 text-white px-4 py-2 rounded">Confirm Reset Password</button>
			</form>
		</div>

		<!-- Option B: Sign in with Google (email OTP) -->
		<div class="bg-blue-100 p-6 rounded">
			<h3 class="text-lg font-semibold mb-4">Or sign in with Google</h3>
			<p class="text-sm text-gray-600 mb-4">Enter the Gmail connected to your account. We’ll send a 6-digit code to continue.</p>
			<div class="space-y-4">
				<p class="text-sm text-gray-600">Choose your Google account. We'll send a 6-digit code to that Gmail.</p>
				<a href="{{ route('oauth.google.reset') }}" class="bg-white border px-4 py-2 rounded shadow inline-flex items-center">
					<i class="fab fa-google text-red-500 mr-2"></i> Sign in with Google
				</a>
			</div>
		</div>
	</div>
@endsection
*** End Patch
