@if ($paginator->hasPages())
    <nav class="flex items-center justify-center space-x-2 mt-6"> 
        
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed">←</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition">←</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-4 py-2 text-gray-500">{{ $element }}</span>
            @elseif (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-4 py-2 bg-blue-600 text-white rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-blue-500 transition">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition">→</a>
        @else
            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed">→</span>
        @endif
    </nav>
@endif
