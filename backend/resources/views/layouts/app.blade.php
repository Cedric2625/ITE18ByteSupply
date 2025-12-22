<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ByteSupply') }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Define confirmModal function before Alpine.js loads
        function confirmModal() {
            return {
                open: false,
                message: '',
                details: '',
                pendingForm: null,
                init() {
                    const self = this;
                    
                    // Listen for form submissions with data-confirm attribute
                    function handleSubmit(e) {
                        // Find the form element
                        let form = e.target;
                        
                        // If target is not a form, find the parent form
                        if (form && form.tagName !== 'FORM') {
                            form = form.closest('form');
                        }
                        
                        if (!form) return;
                        
                        // Check if form has data-confirm attribute
                        const confirmMsg = form.getAttribute('data-confirm');
                        
                        if (!confirmMsg) return;
                        
                        // Allow deletion even if there are associated components
                        
                        // Prevent default form submission
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        
                        // Store form and show modal
                        self.message = confirmMsg;
                        self.details = form.getAttribute('data-details') || '';
                        self.pendingForm = form;
                        self.open = true;
                    }
                    
                    // Attach event listener with multiple strategies to ensure it works
                    // Strategy 1: Attach immediately if DOM is ready
                    if (document.readyState === 'complete' || document.readyState === 'interactive') {
                        document.addEventListener('submit', handleSubmit, true);
                    }
                    
                    // Strategy 2: Attach when DOM is fully loaded
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', () => {
                            document.addEventListener('submit', handleSubmit, true);
                        });
                    }
                    
                    // Strategy 3: Attach after a short delay to ensure everything is ready
                    setTimeout(() => {
                        document.addEventListener('submit', handleSubmit, true);
                    }, 100);
                    
                    // Strategy 4: Use window load event as final fallback
                    window.addEventListener('load', () => {
                        document.addEventListener('submit', handleSubmit, true);
                    });
                    
                    // Also handle button clicks directly as a fallback for delete buttons
                    document.addEventListener('click', function(e) {
                        // Check if clicking on delete icon or button
                        const target = e.target;
                        const isDeleteIcon = target.classList.contains('fa-trash') || target.closest('.fa-trash');
                        const button = isDeleteIcon ? target.closest('button[type="submit"]') : target.closest('button[type="submit"]');
                        
                        if (button) {
                            const form = button.closest('form');
                            if (form && form.getAttribute('data-confirm')) {
                                // Allow deletion - no disabled check needed
                                // The form submit event will handle showing the modal
                            }
                        }
                    }, true);
                },
                approve() {
                    if (this.pendingForm) {
                        const form = this.pendingForm;
                        const formData = new FormData(form);
                        const action = form.getAttribute('action');
                        const method = form.querySelector('input[name="_method"]')?.value || 'POST';
                        
                        this.open = false;
                        this.pendingForm = null;
                        this.message = '';
                        this.details = '';
                        
                        // Submit the form directly
                        setTimeout(() => {
                            if (form && form.parentNode) {
                                // Create a hidden form to submit
                                const hiddenForm = document.createElement('form');
                                hiddenForm.method = 'POST';
                                hiddenForm.action = action;
                                hiddenForm.style.display = 'none';
                                
                                // Add CSRF token
                                const csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = formData.get('_token');
                                hiddenForm.appendChild(csrfInput);
                                
                                // Add method override if needed
                                if (method !== 'POST') {
                                    const methodInput = document.createElement('input');
                                    methodInput.type = 'hidden';
                                    methodInput.name = '_method';
                                    methodInput.value = method;
                                    hiddenForm.appendChild(methodInput);
                                }
                                
                                document.body.appendChild(hiddenForm);
                                hiddenForm.submit();
                            }
                        }, 100);
                    }
                },
                cancel() {
                    this.open = false;
                    this.pendingForm = null;
                    this.message = '';
                    this.details = '';
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Page Heading -->
    <header class="bg-white shadow">
		@php
			$forceHideBack = request()->routeIs('admin.dashboard') || request()->routeIs('shop') || request()->routeIs('login') || request()->routeIs('auth.register.form');
		@endphp
		<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
			<div class="flex items-center gap-4" x-data="{ showBack: false }" x-init="showBack = {{ $forceHideBack ? 'false' : 'true' }} && (window.history.length > 1) && !(document.referrer.includes('/login') || document.referrer.includes('/register'))">
				<a x-show="showBack" href="javascript:void(0)" onclick="if (document.referrer) { window.history.back(); } else { window.location.assign('{{ route('dashboard') }}'); }" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
					<i class="fas fa-arrow-left mr-2"></i> Back
				</a>
				<h1 class="text-3xl font-bold text-gray-900">
					@yield('header')
				</h1>
			</div>
		</div>
    </header>

    <!-- Flash Messages -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative max-w-7xl mx-auto mt-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <span class="sr-only">Close</span>
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative max-w-7xl mx-auto mt-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <span class="sr-only">Close</span>
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Page Content -->
    <main class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} ByteSupply. All rights reserved.
            </p>
        </div>
    </footer>

    @stack('scripts')

    @php
        // Only show modal on admin pages - check both route names and paths
        $isAdminPage = request()->is('admin/*') || 
                      request()->is('admins*') || 
                      request()->is('buyers*') || 
                      request()->is('categories*') || 
                      request()->is('suppliers*') || 
                      request()->is('hardware-components*') || 
                      request()->is('orders*') ||
                      request()->is('selected-components*') ||
                      request()->routeIs('categories.*') ||
                      request()->routeIs('suppliers.*') ||
                      request()->routeIs('hardware-components.*') ||
                      request()->routeIs('orders.*') ||
                      request()->routeIs('admins.*') ||
                      request()->routeIs('buyers.*');
    @endphp

    @if($isAdminPage)
    <!-- Global confirm modal using Alpine.js (Admin pages only) -->
    <div x-data="confirmModal()" 
         x-show="open" 
         x-cloak
         x-transition
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;"
         @click.self="cancel()"
    >
        <div 
            class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 mx-4 z-[10000]" 
            @click.stop
        >
            <h3 class="text-lg font-semibold mb-2">Confirm action</h3>
            <p class="text-gray-700 mb-1" x-text="message"></p>
            <p class="text-gray-500 text-sm mb-4" x-text="details"></p>
            <div class="flex justify-end space-x-2">
                <button 
                    @click="cancel()" 
                    type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors"
                >
                    No
                </button>
                <button 
                    @click="approve()" 
                    type="button"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
                >
                    Yes
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
