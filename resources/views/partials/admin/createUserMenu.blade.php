<div id="createUserMenu" class="hidden fixed inset-0 items-center justify-center z-50 bg-gray-900 bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <form action = "{{route('admin.users.create')}}"id="createUserForm" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-gray-700 font-medium">Username:</label>
                <input type="text" id="create-username" name="username"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="email" class="block text-gray-700 font-medium">Email:</label>
                <input type="email" id="create-email" name="email"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-medium">Password:</label>
                <input type="password" id="create-password" name="password"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium">Confirm Password:</label>
                <input type="password" id="create-password_confirmation" name="password_confirmation"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <button type="button" id="cancelCreateUserBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </button>
            <button type="submit" id="submitCreateUserBtn"  class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create
            </button>
        </form>
    </div>
</div>