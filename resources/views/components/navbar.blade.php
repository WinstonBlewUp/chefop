<nav class="bg-white w-100 px-20 py-5 justify-between">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between h-16 items-center font-normal">
            <div class="flex space-x-4 items-center">
                <!-- <div class="text-lg font-bold text-gray-800">
                    <a href="/"></a>
                </div> -->

                @foreach ($menuLinks as $link)
                    @if($link->page_id)
                        <a href="{{ route('pages.show', $link->slug) }}"
                            class="text-sm text-gray-700 hover:text-indigo-600 transition">
                            {{ strtoupper($link->title) }}
                        </a>
                    @else
                        <a href="{{ route('categories.show', $link->slug) }}"
                            class="text-sm text-gray-700 hover:text-indigo-600 transition">
                            {{ strtoupper($link->title) }}
                        </a>
                    @endif
                @endforeach            
            </div>   

            <div class="flex space-x-4 items-center">
                

                <a href="/stills" class="text-sm text-gray-700 hover:text-indigo-600 transition">
                    STILLS
                </a>
                <a href="/contact" class="text-sm text-gray-700 hover:text-indigo-600 transition">
                    CONTACT
                </a>
            </div>                  
        </div>
    </div>
</nav>