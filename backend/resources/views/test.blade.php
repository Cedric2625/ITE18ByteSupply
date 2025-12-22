<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add & Delete Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 relative overflow-hidden">

    <!-- Subtle animated background shapes -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-10 left-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-72 h-72 bg-pink-300 opacity-20 rounded-full blur-3xl animate-ping"></div>
    </div>

    <!-- Glass-style container -->
    <div class="relative z-10 bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl p-10 max-w-5xl w-full flex flex-col md:flex-row gap-10 border border-white/20">

        <!-- Add Admin Form -->
        <div class="flex-1">
            <h2 class="text-3xl font-bold text-white mb-6 text-center">Add New Admin</h2>

            <form action="/api/addAdmin" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="username" class="block text-sm text-gray-200 mb-1">Admin Username</label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-white/30 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                </div>

                <div>
                    <label for="password_hash" class="block text-sm text-gray-200 mb-1">Password</label>
                    <input type="password" id="password_hash" name="password_hash" required
                        class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-white/30 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                </div>

                <div>
                    <label for="role" class="block text-sm text-gray-200 mb-1">Role</label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2 rounded-lg bg-white/20 text-white border border-white/30 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <option value="system_admin" class="text-gray-800">System Admin</option>
                        <option value="admin" class="text-gray-800">Admin</option>
                        <option value="supply_admin" class="text-gray-800">Supply Admin</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                    ➕ Add Admin
                </button>
            </form>
        </div>

        <!-- Divider line for small screens -->
        <div class="hidden md:block w-px bg-white/30"></div>
        <div class="block md:hidden border-t border-white/30"></div>

        <!-- Delete Admin Form -->
        <div class="flex-1">
            <h2 class="text-3xl font-bold text-white mb-6 text-center">Delete Admin</h2>

            <form action="/api/deleteAdmin/3" method="POST" class="space-y-5">
                @csrf
                @method('DELETE')
                <div>
                    <label for="admin_id" class="block text-sm text-gray-200 mb-1">Admin ID to Delete</label>
                    <input type="number" id="admin_id" name="admin_id" placeholder="Enter Admin ID" required
                        class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-white/30 focus:ring-2 focus:ring-red-400 focus:outline-none">
                </div>

                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                    🗑️ Delete Admin
                </button>
            </form>
        </div>
    </div>

</body>
</html>
