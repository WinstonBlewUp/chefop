<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- <div class="text-lg font-bold text-gray-800">
                <a href="/"></a>
            </div> -->

            <div class="flex space-x-4 items-center">
                @foreach ($menuLinks as $link)
                    <a href="{{ route('pages.show', $link->page->slug) }}"
                       class="text-sm text-gray-700 hover:text-indigo-600 transition">
                        {{ $link->page->title }}
                    </a>
                @endforeach

                <a href="/stills" class="text-sm text-gray-700 hover:text-indigo-600 transition">
                    Stills
                </a>
                <a href="/contact" class="text-sm text-gray-700 hover:text-indigo-600 transition">
                    Contact
                </a>
            </div>
        </div>
    </div>
</nav>
