<footer class="w-full bg-sky-900 text-white p-4 border-t border-gray-300">
    <div class="container mx-auto flex flex-col items-center lg:flex-row lg:justify-between items-center gap-4">
        <!-- Logo and Copyright -->
        <div class="flex flex-col items-center lg:items-start text-center lg:text-left">
            <h1 class="text-xl font-bold">Wavy</h1>
            <p class="text-sm">&copy; 2024 Wavy. All rights reserved.</p>
        </div>
        <!-- Quick Links -->
        <nav class="w-full lg:w-auto">
            <ul class="flex justify-center gap-4 text-sm">
                <li>
                    <a href="{{ route('features') }}" class="hover:underline">Main Features</a>
                </li>
                <li>
                    <a href="{{ route('about') }}" class="hover:underline">About Us</a>
                </li>
                <li>
                    <a href="{{ route('contacts') }}" class="hover:underline">Contact</a>
                </li>
            </ul>
        </nav>
    </div>
</footer>
