<nav x-data="{ open: false }" class="bg-gray-800">
	@php
		$__cartItems = session('cart', []);
		$__cartCount = 0;
		foreach ($__cartItems as $__ci) {
			$__cartCount += (int) ($__ci['quantity'] ?? 0);
		}
	@endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl">
                        ByteSupply
                    </a>
                </div>

                <!-- Navigation Links -->
				<div class="hidden md:block">
					<div class="ml-10 flex items-baseline space-x-4">
						@if(auth('admin')->check())
							<a href="{{ route('admin.dashboard') }}" class="@if(request()->routeIs('admin.dashboard')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
							<a href="{{ route('admins.index') }}" class="@if(request()->routeIs('admins.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">Admins</a>
                            <a href="{{ route('buyers.index') }}" class="@if(request()->routeIs('buyers.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">Buyers</a>
                            <a href="{{ route('categories.index') }}" class="@if(request()->routeIs('categories.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">Categories</a>
                            <a href="{{ route('suppliers.index') }}" class="@if(request()->routeIs('suppliers.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">Suppliers</a>
                            <a href="{{ route('hardware-components.index') }}" class="@if(request()->routeIs('hardware-components.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">Hardware</a>
                            <a href="{{ route('orders.index') }}" class="@if(request()->routeIs('orders.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">Orders</a>
                        @endif

                        @if(auth('buyer')->check())
                            <a href="{{ route('shop') }}" class="@if(request()->routeIs('shop') || request()->routeIs('shop.index')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">Shop</a>
                            <a href="{{ route('shop.orders.index') }}" class="@if(request()->routeIs('shop.orders.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium">My Orders</a>
							<a href="{{ route('shop.cart') }}" class="@if(request()->routeIs('shop.cart')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium relative">
								Cart
								@if($__cartCount > 0)
									<span class="absolute -top-1 -right-1 inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">{{ $__cartCount }}</span>
								@endif
							</a>
                        @endif
						@if(auth('admin')->check() || auth('buyer')->check())
							<form method="POST" action="{{ route('auth.logout') }}" class="inline" id="logout-form-desktop">
								@csrf
								@if(auth('buyer')->check())
									<a href="{{ route('shop.settings') }}" class="@if(request()->routeIs('shop.settings')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif px-3 py-2 rounded-md text-sm font-medium mr-1">Settings</a>
								@endif
								<button type="button" onclick="document.getElementById('logout-form-desktop').submit()" class="text-sm text-gray-300 hover:text-white px-3 py-2">Logout</button>
							</form>
						@else
							@if(!request()->routeIs('login'))
								<a href="{{ route('login') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Login</a>
							@endif
						@endif
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex md:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars" x-show="!open"></i>
                    <i class="fas fa-times" x-show="open"></i>
                </button>
            </div>
        </div>
    </div>

	<!-- Mobile menu -->
	<div x-show="open" class="md:hidden">
		<div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
			@if(auth('admin')->check())
				<a href="{{ route('admin.dashboard') }}" class="@if(request()->routeIs('admin.dashboard')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">
					Dashboard
				</a>
				<a href="{{ route('admins.index') }}" class="@if(request()->routeIs('admins.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">Admins</a>
				<a href="{{ route('buyers.index') }}" class="@if(request()->routeIs('buyers.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">Buyers</a>
				<a href="{{ route('categories.index') }}" class="@if(request()->routeIs('categories.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">Categories</a>
				<a href="{{ route('suppliers.index') }}" class="@if(request()->routeIs('suppliers.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">Suppliers</a>
				<a href="{{ route('hardware-components.index') }}" class="@if(request()->routeIs('hardware-components.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">Hardware</a>
				<a href="{{ route('orders.index') }}" class="@if(request()->routeIs('orders.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">Orders</a>
			@endif

			@if(auth('buyer')->check())
				<a href="{{ route('shop') }}" class="@if(request()->routeIs('shop') || request()->routeIs('shop.index')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">Shop</a>
				<a href="{{ route('shop.orders.index') }}" class="@if(request()->routeIs('shop.orders.*')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">My Orders</a>
				<a href="{{ route('shop.cart') }}" class="@if(request()->routeIs('shop.cart')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium relative pr-8">
					Cart
					@if($__cartCount > 0)
						<span class="absolute top-1 right-3 inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">{{ $__cartCount }}</span>
					@endif
				</a>
				<a href="{{ route('shop.settings') }}" class="@if(request()->routeIs('shop.settings')) bg-gray-900 text-white @else text-gray-300 hover:bg-gray-700 hover:text-white @endif block px-3 py-2 rounded-md text-base font-medium">Settings</a>
			@endif

			@if(auth('admin')->check() || auth('buyer')->check())
				<form method="POST" action="{{ route('auth.logout') }}" class="px-3 py-2" id="logout-form-mobile">
					@csrf
					<button type="button" onclick="document.getElementById('logout-form-mobile').submit()" class="text-gray-300 hover:text-white">Logout</button>
				</form>
			@else
				@if(!request()->routeIs('login'))
					<a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Login</a>
				@endif
			@endif
		</div>
	</div>
</nav>
