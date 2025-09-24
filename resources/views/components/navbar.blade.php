<style>
@media (max-width: 767px) {
    .desktop-only { display: none !important; }
    .mobile-only { display: block !important; }
    .mobile-flex { display: flex !important; }
}
@media (min-width: 768px) {
    .desktop-only { display: flex !important; }
    .mobile-only { display: none !important; }
    .mobile-flex { display: none !important; }
}
</style>

<nav class="bg-white w-full lg:pr-20 py-5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center font-normal lg:pr-4">

            <!-- Logo + Menu Desktop -->
            <div id="desktop-nav" class="desktop-only space-x-4 items-center">
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

            <!-- Logo Mobile -->
            <div id="mobile-logo" class="mobile-only">
                <a href="/">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="h-24 w-auto">
                </a>
            </div>

            <!-- Liens fixes Desktop -->
            <div id="desktop-links" class="desktop-only space-x-4 items-center mr-0 lg:mr-8">
                <a href="/stills" class="text-sm text-gray-700 hover:text-indigo-600 transition">
                    STILLS
                </a>
                <a href="/contact" class="text-sm text-gray-700 hover:text-indigo-600 transition">
                    CONTACT
                </a>
            </div>

            <!-- Burger Mobile -->
            <div id="mobile-burger" class="mobile-only">
                <button id="mobile-menu-button" class="text-gray-700 hover:text-indigo-600 focus:outline-none focus:text-indigo-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu Mobile Overlay -->
        <div id="mobile-menu" class="fixed inset-0 bg-white z-50" style="display: none;">
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center px-4 py-5 border-b">
                    <a href="/">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="h-24 w-auto">
                    </a>
                    <button id="mobile-menu-close" class="text-gray-700 hover:text-indigo-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 px-4 py-6 space-y-6">
                    @foreach ($menuLinks as $link)
                        @if($link->page_id)
                            <a href="{{ route('pages.show', $link->slug) }}"
                                class="block text-lg text-gray-700 hover:text-indigo-600 transition py-2 border-b border-gray-100">
                                {{ strtoupper($link->title) }}
                            </a>
                        @else
                            <a href="{{ route('categories.show', $link->slug) }}"
                                class="block text-lg text-gray-700 hover:text-indigo-600 transition py-2 border-b border-gray-100">
                                {{ strtoupper($link->title) }}
                            </a>
                        @endif
                    @endforeach

                    <a href="/stills" class="block text-lg text-gray-700 hover:text-indigo-600 transition py-2 border-b border-gray-100">
                        STILLS
                    </a>
                    <a href="/contact" class="block text-lg text-gray-700 hover:text-indigo-600 transition py-2 border-b border-gray-100">
                        CONTACT
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenuClose = document.getElementById('mobile-menu-close');

    function openMenu() {
        if (mobileMenu) {
            mobileMenu.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMenu() {
        if (mobileMenu) {
            mobileMenu.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Menu events
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', openMenu);
    }

    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', closeMenu);
    }

    // Close menu on link click
    if (mobileMenu) {
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    }

    // Close with Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMenu();
        }
    });
});
</script>