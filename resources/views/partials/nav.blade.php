@php
    $sections = [
        'how-it-works' => 'كيف يعمل',
        'features' => 'المميزات',
        'suppliers' => 'الموردون',
        'products' => 'المنتجات'
    ];
@endphp

<header class="fixed w-full bg-white shadow-sm z-50" 
    x-data="{ 
        activeSection: '',
        navOpen: false,
        init() {
            window.addEventListener('section-changed', (e) => {
                this.activeSection = e.detail.sectionId;
            });
        }
    }"
>
    <div class="container mx-auto px-6 py-3 flex items-center">
        <!-- Logo -->
        <div class="flex-shrink-0">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active-nav' : '' }}">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Tzwad Logo" class="h-16 md:h-20" />
            </a>
        </div>

        <!-- Desktop Navigation -->
        <nav class="flex-1 flex justify-center">
            <div class="hidden md:flex items-center gap-8">
                @foreach($sections as $id => $label)
                    <a href="{{ request()->routeIs('home') ? "#{$id}" : route('home') . "#{$id}" }}" 
                       :class="activeSection === '{{ $id }}' ? 'active-nav' : ''" 
                       class="text-gray-700 hover:text-primary transition-colors font-bold text-lg">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </nav>

        <!-- Mobile Menu Button -->
        <div class="md:hidden w-8 h-8 flex items-center justify-center cursor-pointer" 
             @click="navOpen = !navOpen">
            <i class="ri-menu-line ri-lg text-gray-700"></i>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="navOpen" 
             @click.away="navOpen = false" 
             class="absolute top-20 right-0 w-full bg-white shadow-lg md:hidden z-50">
            <div class="flex flex-col items-center gap-6 py-6">
                @foreach($sections as $id => $label)
                    <a href="{{ request()->routeIs('home') ? "#{$id}" : route('home') . "#{$id}" }}" 
                       @click="navOpen = false" 
                       :class="activeSection === '{{ $id }}' ? 'active-nav' : ''" 
                       class="text-gray-700 hover:text-primary transition-colors font-bold text-lg">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Download Button (Hidden) -->
        <div class="flex-shrink-0">
            <button class="px-8 py-3 bg-primary text-white font-bold text-lg hover:bg-secondary transition-colors rounded-full shadow-lg hidden">
                <i class="ri-download-2-line"></i>
                تحميل التطبيق
            </button>
        </div>
    </div>
    <style>
        .active-nav {
            color: #008080 !important;
            border-bottom: 2px solid #008080;
        }
    </style>
    <!-- Alpine.js and Navigation -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/navigation.js') }}" defer></script>
</header> 