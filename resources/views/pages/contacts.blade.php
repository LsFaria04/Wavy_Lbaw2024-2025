@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container mx-auto py-12 px-4">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Contact Us</h1>
        <p class="text-lg text-gray-600 mt-2">We’re here to help. Get in touch with us for any questions or support.</p>
    </div>

    <!-- Support Contact -->
    <section class="mb-8 bg-gray-100 p-6 rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Customer Support</h2>
        <p class="text-lg text-gray-700">
            Have issues or inquiries? Reach out to our support team via email or phone for quick assistance.
        </p>
        <ul class="list-none mt-4 text-lg text-gray-700">
            <li><strong>Email:</strong> <a href="mailto:support@wavy.com" class="text-blue-500 hover:underline">support@wavy.com</a></li>
            <li><strong>Phone:</strong> +123 456 789</li>
        </ul>
    </section>

    <!-- Business Inquiries -->
    <section class="mb-8">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Business Inquiries</h2>
        <p class="text-lg text-gray-700">
            For partnerships or other business-related inquiries, feel free to contact our team.
        </p>
        <ul class="list-none mt-4 text-lg text-gray-700">
            <li><strong>Email:</strong> <a href="mailto:business@wavy.com" class="text-blue-500 hover:underline">business@wavy.com</a></li>
        </ul>
    </section>

    <!-- Feedback -->
    <section class="mb-8 bg-gray-100 p-6 rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Feedback</h2>
        <p class="text-lg text-gray-700">
            Your opinion matters! Send us your feedback to help us improve Wavy.
        </p>
        <a href="{{ route('feedback') }}" class="inline-block px-6 py-3 text-white bg-blue-500 rounded-full hover:bg-blue-600 transition duration-300">Give Feedback</a>
    </section>
</div>
@endsection
