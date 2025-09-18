<nav class="bg-white w-100 pr-20 py-5 justify-between">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Zone navbar centrée sur toute la largeur --}}
        <div class="flex justify-between h-16 items-center font-normal pr-4">
            <div class="flex space-x-4 items-center">
                {{-- Logo du site --}}
                <div class="mr-6">
                    <a href="/">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="h-32 w-auto mr-5">
                    </a>
                </div>

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

            <div class="flex space-x-4 items-center mr-8">
                

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