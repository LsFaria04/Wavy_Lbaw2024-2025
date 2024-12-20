<div id="createTopicMenu" class="hidden fixed inset-0 items-center justify-center z-50 bg-gray-900 bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <form action = "{{url('/topics/add')}}" method = "POST" id ="createTopicForm" class="space-y-4">
            @csrf
            <div>
                <label for="topicName" class="block text-gray-700 font-medium">Topic Name:</label>
                <input type="text" id="create-Topic" name="topicname"
                    class="mt-1 block w-full p-2 border rounded-md"
                    required>
            </div>
            <div class = "flex justify-end space-x-2">
                <button type="button" id="cancelCreateTopicBtn" class="px-4 py-2 w-20 bg-gray-700 text-white font-semibold rounded-3xl hover:bg-gray-800">
                    Cancel
                </button>
                <button type="submit" id="submitCreateTopicBtn" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>