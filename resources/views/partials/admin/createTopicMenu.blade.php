<div id="createTopicMenu" class="hidden fixed inset-0 items-center justify-center z-50 bg-gray-900 bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <form action = "{{url('/topics/add')}}" method = "POST" id ="createTopicForm" class="space-y-4">
            @csrf
            <div>
                <label for="topicName" class="block text-gray-700 font-medium">Topic Name:</label>
                <input type="text" id="create-Topic" name="topicname"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <button type="button" id="cancelCreateTopicBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </button>
            <button type="submit" id="submitCreateTopicBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create
            </button>
        </form>
    </div>
</div>