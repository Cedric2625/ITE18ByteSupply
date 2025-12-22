@extends('layouts.app')

@section('title', 'Settings')

@section('header', 'Account Settings')

@section('content')
	<div class="space-y-6">
		<div class="bg-white shadow rounded-lg p-6">
			<h2 class="text-lg font-semibold mb-4">Profile</h2>
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<div>
					<p class="text-sm text-gray-500">Name</p>
					<p class="text-base font-medium text-gray-900">{{ $buyer->buyer_name ?? $buyer->name }}</p>
				</div>
				<div>
					<p class="text-sm text-gray-500">Email</p>
					<p class="text-base font-medium text-gray-900">{{ $buyer->email }}</p>
				</div>
				<div>
					<p class="text-sm text-gray-500">Account Created</p>
					<p class="text-base font-medium text-gray-900">{{ optional($buyer->created_at)->format('F j, Y g:i A') }}</p>
				</div>
			</div>
		</div>
	</div>
@endsection


