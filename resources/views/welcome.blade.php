<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>تزود</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#008080",
                        secondary: "#FFA500"
                    },
                    borderRadius: {
                        none: "0px",
                        sm: "4px",
                        DEFAULT: "8px",
                        md: "12px",
                        lg: "16px",
                        xl: "20px",
                        "2xl": "24px",
                        "3xl": "32px",
                        full: "9999px",
                        button: "8px",
                    },
                },
            },
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}" />
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <!-- Leaflet CSS for interactive map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>

<body class="bg-white">
    <!-- Header -->
    <header class="fixed w-full bg-white shadow-sm z-50">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Tzwad Logo" class="h-16 md:h-20" />
            </div>
            <nav class="hidden md:flex items-center gap-8">
                <a href="#how-it-works" class="text-gray-700 hover:text-primary transition-colors font-bold text-lg">كيف يعمل</a>
                <a href="#features" class="text-gray-700 hover:text-primary transition-colors font-bold text-lg">المميزات</a>
                <a href="#suppliers" class="text-gray-700 hover:text-primary transition-colors font-bold text-lg">الموردون</a>
                <a href="#products" class="text-gray-700 hover:text-primary transition-colors font-bold text-lg">المنتجات</a>
            </nav>
            <div class="flex items-center gap-4">
                <button
                    class="px-8 py-3 bg-primary text-white font-bold text-lg hover:bg-secondary transition-colors rounded-full shadow-lg hidden">
                    <i class="ri-download-2-line"></i>
                    تحميل التطبيق
                </button>
            </div>
            <div class="md:hidden w-8 h-8 flex items-center justify-center">
                <i class="ri-menu-line ri-lg text-gray-700"></i>
            </div>
        </div>
    </header>
    <!-- Improved Hero Section -->
    <section class="hero-section pt-24 pb-16 md:py-32 bg-gradient-to-r from-primary/10 to-secondary/10" data-aos="fade-up">
        <div class="container mx-auto px-6 w-full flex flex-col items-center justify-center text-center">
            <div class="max-w-2xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-extrabold text-primary mb-4 leading-tight drop-shadow">
                    تواصل مع موردي الأغذية المحليين بسهولة وموثوقية
                </h1>
                <p class="text-lg md:text-xl text-gray-700 mb-8 font-medium">
                    اكتشف أفضل المنتجات الغذائية مباشرة من الموردين المحليين إلى مائدتك، بسرعة وأمان.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-4">
                    <button
                        class="px-8 py-3 bg-primary text-white font-bold text-lg hover:bg-secondary transition-colors rounded-full shadow-lg hidden">
                        <i class="ri-download-2-line"></i>
                        تحميل التطبيق
                    </button>
                    <button
                        class="px-8 py-3 text-primary border-2 border-primary font-bold text-lg hover:bg-primary hover:text-white transition-colors rounded-full">
                        اعرف المزيد
                    </button>
                </div>
                <div class="flex items-center justify-center gap-2 mt-4">
                    <i class="ri-shield-star-line text-secondary text-2xl"></i>
                    <span class="text-gray-600 text-sm">موثوق به من قبل أكثر من 1000 مطعم ومورد</span>
                </div>
            </div>
        </div>
    </section>
    <!-- How It Works Section -->
    <section id="how-it-works" class="py-16 bg-gray-50" data-aos="fade-up">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 fade-up">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">كيف يعمل</h2>
                <p class="text-base text-gray-600 max-w-2xl mx-auto font-medium">
                    تربطك منصتنا مباشرة بموردي الأغذية بناءً على موقعك، مما يجعل عملية الشراء بسيطة وفعالة.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded shadow-md text-center fade-up" data-aos="zoom-in" data-aos-delay="100">
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
                <div class="bg-white p-8 rounded shadow-md text-center fade-up" data-aos="zoom-in" data-aos-delay="200">
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
                <div class="bg-white p-8 rounded shadow-md text-center fade-up" data-aos="zoom-in" data-aos-delay="300">
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
    <section id="features" class="py-16" data-aos="fade-up">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 fade-up">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">
                    المميزات الرئيسية
                </h2>
                <p class="text-base text-gray-600 max-w-2xl mx-auto font-medium">
                    4 مزايا تجعل "تزود" المنصة المثلى لربط مشتري الأغذية بالموردين
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded shadow-sm border border-gray-100 fade-up" data-aos="zoom-in" data-aos-delay="100">
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
                <div class="bg-white p-6 rounded shadow-sm border border-gray-100 fade-up" data-aos="zoom-in" data-aos-delay="200">
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
                <div class="bg-white p-6 rounded shadow-sm border border-gray-100 fade-up" data-aos="zoom-in" data-aos-delay="300">
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
                <div class="bg-white p-6 rounded shadow-sm border border-gray-100 fade-up" data-aos="zoom-in" data-aos-delay="400">
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
    <section id="suppliers" class="py-16 bg-gray-50" data-aos="fade-up">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 fade-up">
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
                            <li class="flex items-start  gap-x-6">
                                <div
                                    class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1">
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
                            <li class="flex items-start  gap-x-6">
                                <div
                                    class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1">
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
                            <li class="flex items-start  gap-x-6">
                                <div
                                    class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1">
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
                            <li class="flex items-start  gap-x-6">
                                <div
                                    class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1">
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
                <div id="supplier-map" class="h-96 w-full rounded shadow-md"></div>
            </div>
        </div>
    </section>
    <!-- Categories -->
    <section id="products" class="py-16" data-aos="fade-up">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 fade-up">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">
                    تصنيفاتنا
                </h2>
                <p class="text-base text-gray-600 max-w-2xl mx-auto font-medium">
                    تصفح من خلال مجموعة واسعة من فئات المنتجات الغذائية للعثور على ما تحتاجه بالضبط.
                </p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <div class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up">
                        <div class="h-48 overflow-hidden">
                            <img src="{{ $category->image_url ?? asset('images/logo.jpeg') }}"
                                 alt="{{ $category->name }}"
                                 class="w-full h-full object-cover object-top" />
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">
                                {{ $category->name }}
                            </h3>
                            <p class="text-sm text-gray-600 font-normal">
                                {{ $category->description ?? '' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- CTA Section -->
    <section class="py-16 bg-primary bg-opacity-5" data-aos="fade-up">
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
    <section class="py-16 bg-white" data-aos="fade-up">
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
    <!-- Footer -->
<footer class="bg-gray-800 text-white pt-16 pb-8">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8 mb-12">
            <div>
                <h3 class="text-xl font-semibold mb-4">تزود</h3>
                <p class="text-base text-gray-400 mb-4 font-normal">
                    ربط مشتري الأغذية مع الموردين المحليين لسلسلة توريد أكثر كفاءة.
                </p>
                <div class="flex space-x-4">
                    <a
                        href="#"
                        class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition-colors"
                    >
                        <i class="ri-facebook-fill ri-sm text-white"></i>
                    </a>
                    <a
                        href="#"
                        class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition-colors"
                    >
                        <i class="ri-twitter-fill ri-sm text-white"></i>
                    </a>
                    <a
                        href="#"
                        class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition-colors"
                    >
                        <i class="ri-instagram-fill ri-sm text-white"></i>
                    </a>
                    <a
                        href="#"
                        class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center hover:bg-primary transition-colors"
                    >
                        <i class="ri-linkedin-fill ri-sm text-white"></i>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">روابط سريعة</h3>
                <ul class="space-y-2">
                    <li>
                        <a
                            href="#"
                            class="text-gray-400 hover:text-white transition-colors"
                        >الرئيسية</a
                        >
                    </li>
                    <li>
                        <a
                            href="#how-it-works"
                            class="text-gray-400 hover:text-white transition-colors"
                        >كيف يعمل</a
                        >
                    </li>
                    <li>
                        <a
                            href="#features"
                            class="text-gray-400 hover:text-white transition-colors"
                        >المميزات</a
                        >
                    </li>
                    <li>
                        <a
                            href="#suppliers"
                            class="text-gray-400 hover:text-white transition-colors"
                        >الموردون</a
                        >
                    </li>
                    <li>
                        <a
                            href="#products"
                            class="text-gray-400 hover:text-white transition-colors"
                        >المنتجات</a
                        >
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">الدعم</h3>
                <ul class="space-y-2">
                    <li>
                        <a
                            href="#"
                            class="text-gray-400 hover:text-white transition-colors"
                        >مركز المساعدة</a
                        >
                    </li>
                    <li>
                        <a
                            href="#"
                            class="text-gray-400 hover:text-white transition-colors"
                        >الأسئلة الشائعة</a
                        >
                    </li>
                    <li>
                        <a
                            href="#"
                            class="text-gray-400 hover:text-white transition-colors"
                        >اتصل بنا</a
                        >
                    </li>
                    <li>
                        <a
                            href="#"
                            class="text-gray-400 hover:text-white transition-colors"
                        >شروط الخدمة</a
                        >
                    </li>
                    <li>
                        <a
                            href="#"
                            class="text-gray-400 hover:text-white transition-colors"
                        >سياسة الخصوصية</a
                        >
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">اتصل بنا</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center mr-3 mt-1">
                            <i class="ri-map-pin-line text-gray-400"></i>
                        </div>
                        <span class="text-gray-400"> عنوان</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center mr-3 mt-1">
                            <i class="ri-phone-line text-gray-400"></i>
                        </div>
                        <span class="text-gray-400">رقم هاتف</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center mr-3 mt-1">
                            <i class="ri-mail-line text-gray-400"></i>
                        </div>
                        <span class="text-gray-400">الايميل</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="pt-8 border-t border-gray-700 text-center text-gray-400">
            <p>&copy; 2025 تزود. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</footer>
    <script src="{{ asset('js/welcome.js') }}"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
      AOS.init({
        duration: 800,
        once: true
      });
    </script>
    <!-- Leaflet JS for interactive map -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var map = L.map('supplier-map').setView([24.7136, 46.6753], 5); // Center on Saudi Arabia

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Example supplier locations (replace with real coordinates)
        var suppliers = [
          { name: 'مورد المنطقة الشمالية', lat: 28.5, lng: 41.5 },
          { name: 'مورد المنطقة الشرقية', lat: 26.3, lng: 50.2 },
          { name: 'مورد المنطقة الغربية', lat: 21.5, lng: 39.2 },
          { name: 'مورد المنطقة الجنوبية', lat: 18.3, lng: 42.8 }
        ];

        suppliers.forEach(function(supplier) {
          L.marker([supplier.lat, supplier.lng])
            .addTo(map)
            .bindPopup('<b>' + supplier.name + '</b>');
        });
      });
    </script>
</body>

</html>
