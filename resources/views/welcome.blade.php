@extends('layouts.app')

@section('title', 'تزود')
@section('body-class', 'bg-white')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section pt-24 pb-16 md:py-32 bg-gradient-to-r from-primary/10 to-secondary/10" data-aos="zoom-in-rotate">
        <div class="container mx-auto px-6 w-full flex flex-col items-center justify-center text-center">
            <div class="max-w-2xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-extrabold text-primary mb-4 leading-tight drop-shadow float-animation">
                    تواصل مع موردي الأغذية المحليين بسهولة وموثوقية
                </h1>
                <p class="text-lg md:text-xl text-gray-700 mb-8 font-medium slide-up-fade">
                    اكتشف أفضل المنتجات الغذائية مباشرة من الموردين المحليين إلى مائدتك، بسرعة وأمان.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-4">
                    <button
                        class="px-8 py-3 bg-primary text-white font-bold text-lg hover:bg-secondary transition-colors rounded-full shadow-lg hidden">
                        <i class="ri-download-2-line"></i>
                        تحميل التطبيق
                    </button>
                    <button
                        class="px-8 py-3 text-primary border-2 border-primary font-bold text-lg hover:bg-primary hover:text-white transition-colors rounded-full bounce-in">
                        اعرف المزيد
                    </button>
                </div>
                <div class="flex items-center justify-center gap-2 mt-4 rotate-in">
                    <i class="ri-shield-star-line text-secondary text-2xl"></i>
                    <span class="text-gray-600 text-sm">موثوق به من قبل أكثر من 1000 مطعم ومورد</span>
                </div>
            </div>
        </div>
    </section>
    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 bg-gray-50" data-aos="flip-up">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12" data-aos="zoom-in">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">كيف يعمل</h2>
                <p class="text-base text-gray-600 max-w-2xl mx-auto font-medium">
                    تربطك منصتنا مباشرة بموردي الأغذية بناءً على موقعك، مما يجعل عملية الشراء بسيطة وفعالة.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded shadow-md text-center scale-up-hover" data-aos="zoom-in-rotate" data-aos-delay="100">
                    <div
                        class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-user-add-line ri-xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">
                        سجل وشارك موقعك
                    </h3>
                    <p class="text-base text-gray-600 font-normal">
                        أنشئ حسابًا وحدد موقعك للبدء في تلقي توصيات مخصصة للموردين.
                    </p>
                </div>
                <div class="bg-white p-8 rounded shadow-md text-center scale-up-hover" data-aos="zoom-in-rotate" data-aos-delay="200">
                    <div
                        class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-contacts-line ri-xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">
                        تواصل مع الموردين
                    </h3>
                    <p class="text-base text-gray-600 font-normal">
                        يقوم نظامنا تلقائيًا بربطك مع أقرب الموردين بناءً على موقعك.
                    </p>
                </div>
                <div class="bg-white p-8 rounded shadow-md text-center scale-up-hover" data-aos="zoom-in-rotate" data-aos-delay="300">
                    <div
                        class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-shopping-cart-line ri-xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">
                        تصفح واطلب المنتجات
                    </h3>
                    <p class="text-base text-gray-600 font-normal">
                        استكشف مجموعة واسعة من المنتجات الغذائية واطلب مباشرة من الموردين.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- Features Section -->
    <section id="features" class="py-16" data-aos="slide-in-right">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12" data-aos="zoom-in">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">
                    المميزات الرئيسية
                </h2>
                <p class="text-base text-gray-600 max-w-2xl mx-auto font-medium">
                    4 مزايا تجعل "تزود" المنصة المثلى لربط مشتري الأغذية بالموردين
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded shadow-sm border border-gray-100 scale-up-hover" data-aos="flip-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                        <i class="ri-map-2-line ri-lg text-secondary"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        الموقع
                    </h3>
                    <p class="text-base text-gray-600 font-normal">
                        تواصل مع موردين قريبين لتقليل الوقت والتكلفة.
                    </p>
                </div>
                <div class="bg-white p-6 rounded shadow-sm border border-gray-100 scale-up-hover" data-aos="flip-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                        <i class="ri-store-2-line ri-lg text-secondary"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        التنوع
                    </h3>
                    <p class="text-base text-gray-600 font-normal">
                        تصفح واطلب من فئات غذائية متعددة بسهولة.
                    </p>
                </div>
                <div class="bg-white p-6 rounded shadow-sm border border-gray-100 scale-up-hover" data-aos="flip-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                        <i class="ri-building-line ri-lg text-secondary"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        المصدر
                    </h3>
                    <p class="text-base text-gray-600 font-normal">
                        اطلب مباشرة من المصدر لتقليل التكاليف.
                    </p>
                </div>
                <div class="bg-white p-6 rounded shadow-sm border border-gray-100 scale-up-hover" data-aos="flip-up" data-aos-delay="400">
                    <div class="w-12 h-12 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-4">
                        <i class="ri-award-line ri-lg text-secondary"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        الجودة
                    </h3>
                    <p class="text-base text-gray-600 font-normal">
                        نضمن موردين يلبّون أعلى معايير الجودة
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- Supplier Coverage Section -->
    <section id="suppliers" class="py-16 bg-gray-50" data-aos="zoom-in-rotate">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12" data-aos="zoom-in">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">
                    شبكة موردينا
                </h2>
                <p class="text-base text-gray-600 max-w-2xl mx-auto font-medium">
                    لنخدمك بأعلي كفاءة تعاوﻧّا مع أربعة مورّدين رئيسيين لﻸغذية يتمركزون في مواقع استراتيجية
                </p>
            </div>
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <div class="bg-white p-6 rounded shadow-md fade-in" data-aos="zoom-in">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">
                            مواقع الموردين
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-x-6">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1">
                                    <span class="text-white font-medium">1</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">
                                        مورد المنطقة الشمالية
                                    </h4>
                                    <p class="text-base text-gray-600 font-normal">
                                        متخصص في منتجات الألبان والمنتجات الطازجة
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start gap-x-6">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1">
                                    <span class="text-white font-medium">2</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">
                                        مورد المنطقة الشرقية
                                    </h4>
                                    <p class="text-base text-gray-600 font-normal">
                                        متخصص في الحبوب والأرز والبقوليات
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start gap-x-6">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1">
                                    <span class="text-white font-medium">3</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">
                                        مورد المنطقة الغربية
                                    </h4>
                                    <p class="text-base text-gray-600 font-normal">
                                        متخصص في الأغذية المصنعة والمشروبات
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start gap-x-6">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1">
                                    <span class="text-white font-medium">4</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">
                                        مورد المنطقة الجنوبية
                                    </h4>
                                    <p class="text-base text-gray-600 font-normal">
                                        متخصص في اللحوم والدواجن والمأكولات البحرية
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="bg-white p-6 rounded shadow-md h-[500px]" data-aos="zoom-in">
                    <div id="suppliers-map" class="w-full h-full rounded-lg"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- Categories -->
    <section id="products" class="py-16" data-aos="slide-in-right">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12" data-aos="zoom-in">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">
                    تصنيفاتنا
                </h2>
                <p class="text-base text-gray-600 max-w-2xl mx-auto font-medium">
                    تصفح من خلال مجموعة واسعة من فئات المنتجات الغذائية للعثور على ما تحتاجه بالضبط.
                </p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('category.products', $category->id) }}" 
                       class="block hover:shadow-lg transition-shadow duration-300 scale-up-hover" 
                       data-aos="flip-up" 
                       data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="bg-white rounded shadow-sm overflow-hidden border border-gray-100">
                            <div class="h-48 overflow-hidden">
                                <img src="{{ $category->image_url ?? asset('images/logo.jpeg') }}"
                                     alt="{{ $category->getTranslation('name', 'ar') }}"
                                     class="w-full h-full object-cover object-top" />
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-1">
                                    {{ $category->getTranslation('name', 'ar') }}
                                </h3>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    <!-- CTA Section -->
    <section class="py-16 bg-primary bg-opacity-5" data-aos="zoom-in-rotate">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    هل أنت مستعد لتبسيط سلسلة التوريد الغذائية الخاصة بك؟
                </h2>
                <p class="text-base text-gray-600 mb-8 font-medium">
                    انضم إلى تزود اليوم وتواصل مع موردي الأغذية المحليين للحصول على أفضل المنتجات بأسعار تنافسية.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button
                        class="px-8 py-3 bg-primary text-white font-medium hover:bg-opacity-90 transition-colors !rounded-button whitespace-nowrap flex items-center justify-center gap-2 hidden">
                        <i class="ri-download-2-line"></i>
                        تثبيت التطبيق
                    </button>
                    <button
                        class="px-8 py-3 text-primary border border-primary font-medium hover:bg-primary hover:text-white transition-colors !rounded-button whitespace-nowrap">
                        اتصل بالمبيعات
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- Newsletter Section -->
    <section class="py-16 bg-white" data-aos="slide-in-right">
        <div class="container mx-auto px-6">
            <div class="max-w-xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-3">ابق على اطلاع</h2>
                <p class="text-base text-gray-600 mb-6 font-medium">
                    اشترك في نشرتنا البريدية لتلقي تحديثات حول الموردين والمنتجات الجديدة.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="email" placeholder="أدخل بريدك الإلكتروني"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded focus:border-primary custom-input" />
                    <button
                        class="px-6 py-3 bg-primary text-white font-medium hover:bg-opacity-90 transition-colors !rounded-button whitespace-nowrap">
                        اشتراك
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/welcome.js') }}"></script>
@endpush

@push('styles')
<style>
    /* Custom Animation Classes */
    .float-animation {
        animation: float 6s ease-in-out infinite;
    }

    .scale-up-hover {
        transition: transform 0.3s ease;
    }

    .scale-up-hover:hover {
        transform: scale(1.05);
    }

    .rotate-in {
        animation: rotateIn 1s ease-out;
    }

    .bounce-in {
        animation: bounceIn 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .slide-up-fade {
        animation: slideUpFade 1s ease-out;
    }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }

    @keyframes rotateIn {
        from { transform: rotate(-180deg) scale(0); opacity: 0; }
        to { transform: rotate(0) scale(1); opacity: 1; }
    }

    @keyframes bounceIn {
        0% { transform: scale(0.3); opacity: 0; }
        50% { transform: scale(1.05); opacity: 0.8; }
        70% { transform: scale(0.9); opacity: 0.9; }
        100% { transform: scale(1); opacity: 1; }
    }

    @keyframes slideUpFade {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Custom AOS Animations */
    [data-aos="flip-up"] {
        transform: perspective(2500px) rotateX(-90deg);
        opacity: 0;
        transition-property: transform, opacity;
    }

    [data-aos="flip-up"].aos-animate {
        transform: perspective(2500px) rotateX(0);
        opacity: 1;
    }

    [data-aos="zoom-in-rotate"] {
        transform: scale(0.5) rotate(-180deg);
        opacity: 0;
        transition-property: transform, opacity;
    }

    [data-aos="zoom-in-rotate"].aos-animate {
        transform: scale(1) rotate(0);
        opacity: 1;
    }

    [data-aos="slide-in-right"] {
        transform: translateX(100%);
        opacity: 0;
        transition-property: transform, opacity;
    }

    [data-aos="slide-in-right"].aos-animate {
        transform: translateX(0);
        opacity: 1;
    }
</style>
@endpush
