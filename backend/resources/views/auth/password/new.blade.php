@extends('layouts.app')

@section('title', 'Set New Password')
@section('header', 'Reset Password')

@section('content')
	<div class="max-w-md mx-auto" x-data="{ showNew:false, showConfirm:false }">
		<div class="bg-white p-6 rounded shadow">
			<h3 class="text-lg font-semibold mb-4">Type your new password</h3>
			<form action="{{ route('password.new.set') }}" method="POST" class="space-y-4">
				@csrf
				<input type="hidden" name="token" value="{{ $token }}">
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
	</div>
@endsection

