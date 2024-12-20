<div id="createUserMenu" class="hidden fixed inset-0 self-center w-full h-full items-center justify-center z-50 bg-gray-900 bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <form action = "{{route('admin.users.create')}}"id="createUserForm" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-gray-700 font-medium">Username:</label>
                <input type="text" id="create-username" name="username"
                    class="mt-1 block w-full p-2 border rounded-md"
                    required>
            </div>
            <div>
                <label for="email" class="block text-gray-700 font-medium">Email:</label>
                <input type="email" id="create-email" name="email"
                    class="mt-1 block w-full p-2 border rounded-md"
                    required>
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-medium">Password:</label>
                <input type="password" id="create-password" name="password"
                    class="mt-1 block w-full p-2 border rounded-md"
                    required>
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium">Confirm Password:</label>
                <input type="password" id="create-password_confirmation" name="password_confirmation"
                    class="mt-1 block w-full p-2 border rounded-md"
                    required>
            </div>
            <div class = "flex justify-end space-x-2">
                <button type="button" id="cancelCreateUserBtn" class="px-4 py-2 w-20 bg-gray-700 text-white font-semibold rounded-3xl hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" id="submitCreateUserBtn"  class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>