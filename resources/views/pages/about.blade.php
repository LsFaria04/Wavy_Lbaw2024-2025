@extends('layouts.app')

@section('title', 'About Wavy')

@section('content')
<div class="container mx-auto py-12 px-4">
    <div class="text-center mb-8">
        <h2 class="text-4xl font-bold text-gray-800">About Wavy</h2>
        <p class="text-lg text-gray-600 mt-2">Discover our mission, values, and what makes us unique.</p>
    </div>

    <!-- Vision & Purpose -->
    <section class="mb-8 bg-gray-100 p-6 rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Our Vision</h2>
        <p class="text-lg text-gray-700">
            Wavy is a social network built around personalization. Unlike other platforms, we let you define the content that interests you. By selecting topics that resonate with you, we create a more meaningful and engaging online experience.
        </p>
    </section>

    <!-- What is Wavy? -->
    <section class="mb-8">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">What is Wavy?</h2>
        <p class="text-lg text-gray-700">
            Wavy is a web-based social network where you can connect with others, create and share content, and explore a tailored experience based on your interests. Whether you're staying in touch with friends or discovering new topics, Wavy is designed to empower you.
        </p>
    </section>

    <!-- Core Values -->
    <section class="mb-8 bg-gray-100 p-6 rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Our Core Values</h2>
        <ul class="list-disc pl-5 text-lg text-gray-700">
            <li><strong>Personalization:</strong> Your social experience, your way.</li>
            <li><strong>Control:</strong> Manage your content and privacy settings with ease.</li>
            <li><strong>Community:</strong> Connect with users who share your interests in a safe, moderated space.</li>
        </ul>
    </section>

    <!-- Why Wavy? -->
    <section class="mb-8">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Why Choose Wavy?</h2>
        <p class="text-lg text-gray-700">
            Wavy is all about putting you in control. With a focus on personalization, you get to shape your experience and connect with like-minded individuals in a seamless, user-friendly environment.
        </p>
    </section>

    <!-- Commitment to Quality -->
    <section class="mb-8 bg-gray-100 p-6 rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Commitment to Quality</h2>
        <p class="text-lg text-gray-700">
            Our platform is optimized for performance, ensuring a smooth and reliable experience across all devices. Our team works tirelessly behind the scenes to maintain a secure and positive environment for all users.
        </p>
    </section>

    <!-- Call to Action -->
    <div class="text-center mt-10">
        <a href="{{ route('home') }}" class="inline-block px-6 py-3 text-white bg-blue-500 rounded-full hover:bg-blue-600 transition duration-300">Explore Wavy</a>
    </div>
</div>
@endsection
