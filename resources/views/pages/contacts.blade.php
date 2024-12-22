@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container mx-auto py-12 px-4">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Get in Touch with Wavy</h1>
        <p class="text-lg text-gray-600 mt-2">Send us a message, ask a question, or share your thoughts. We’re here to help!</p>
    </div>
    <div id="messageContainer" class="fixed mx-auto right-1/2 top-6 flex items-center">
        <!--Used to append messages with JS -->
    </div>

    <!-- Contact Form -->
    <section class="mb-12">
        <form class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-lg font-semibold text-gray-700">Your Name</label>
                <input type="text" id="name" name="name" placeholder="John Doe" required class="w-full mt-2 px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-lg font-semibold text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" placeholder="johndoe@example.com" required class="w-full mt-2 px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200">
            </div>
            <div class="mb-4">
                <label for="messageContacts" class="block text-lg font-semibold text-gray-700">Message</label>
                <textarea id="messageContacts" name="message" rows="4" placeholder="How can we assist you?" required
                    class="w-full mt-2 px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200"
                    style="resize: vertical; min-height: 50px;"></textarea>
            </div>
            <button type="button" id = "submit" onclick="contactEmail()" class="w-full px-4 py-2 bg-blue-500 text-white text-lg font-semibold rounded-lg hover:bg-blue-600 transition duration-300">Send Message</button>
        </form>
    </section>
</div>
@endsection
