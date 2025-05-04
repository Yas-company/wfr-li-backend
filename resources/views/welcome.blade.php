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
                    colors: { primary: "#008080", secondary: "#FFA500" },
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
    <link
        href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
        rel="stylesheet"
    />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
    />
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}" />
</head>
<body class="bg-white">
<!-- Header -->
<header class="fixed w-full bg-white shadow-sm z-50">
    <div
        class="container mx-auto px-6 py-3 flex justify-between items-center"
    >
        <div class="flex items-center">
            <img
                src="{{ asset('images/logo.jpeg') }}"
                alt="Tzwad Logo"
                class="h-10"
            />
        </div>
        <nav class="hidden md:flex items-center gap-8">
            <a
                href="#how-it-works"
                class="text-gray-700 hover:text-primary transition-colors"
            >كيف يعمل</a
            >
            <a
                href="#features"
                class="text-gray-700 hover:text-primary transition-colors"
            >المميزات</a
            >
            <a
                href="#suppliers"
                class="text-gray-700 hover:text-primary transition-colors"
            >الموردون</a
            >
            <a
                href="#products"
                class="text-gray-700 hover:text-primary transition-colors"
            >المنتجات</a
            >
        </nav>
        <div class="flex items-center gap-4">
            <button
                class="px-6 py-2 bg-primary text-white hover:bg-opacity-90 transition-colors !rounded-button whitespace-nowrap flex items-center gap-2"
            >
                <i class="ri-download-2-line"></i>
                تحميل التطبيق
            </button>
        </div>
        <div class="md:hidden w-8 h-8 flex items-center justify-center">
            <i class="ri-menu-line ri-lg text-gray-700"></i>
        </div>
    </div>
</header>
<!-- Hero Section -->
<section class="hero-section pt-24 pb-16 md:py-32">
    <div class="container mx-auto px-6 w-full">
        <div class="max-w-xl slide-in-right">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                تواصل مع موردي الأغذية المحليين
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                رابطك المباشر لمنتجات غذائية عالية الجودة من المصنع إلى مائدتك
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <button
                    class="px-6 py-3 bg-primary text-white font-medium hover:bg-opacity-90 transition-colors !rounded-button whitespace-nowrap flex items-center justify-center gap-2"
                >
                    <i class="ri-download-2-line"></i>
                    تحميل التطبيق
                </button>
                <button
                    class="px-6 py-3 text-primary border border-primary font-medium hover:bg-primary hover:text-white transition-colors !rounded-button whitespace-nowrap"
                >
                    اعرف المزيد
                </button>
            </div>
        </div>
    </div>
</section>
<!-- How It Works Section -->
<section id="how-it-works" class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12 fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-3">كيف يعمل</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                تربطك منصتنا مباشرة بموردي الأغذية بناءً على موقعك، مما يجعل عملية
                الشراء بسيطة وفعالة.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded shadow-md text-center fade-up">
                <div
                    class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6"
                >
                    <i class="ri-user-add-line ri-xl text-primary"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    سجل وشارك موقعك
                </h3>
                <p class="text-gray-600">
                    أنشئ حسابًا وحدد موقعك للبدء في تلقي توصيات مخصصة للموردين.
                </p>
            </div>
            <div class="bg-white p-8 rounded shadow-md text-center fade-up">
                <div
                    class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6"
                >
                    <i class="ri-map-pin-line ri-xl text-primary"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    تواصل مع الموردين
                </h3>
                <p class="text-gray-600">
                    يقوم نظامنا تلقائيًا بربطك مع أقرب الموردين بناءً على موقعك.
                </p>
            </div>
            <div class="bg-white p-8 rounded shadow-md text-center fade-up">
                <div
                    class="w-16 h-16 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mx-auto mb-6"
                >
                    <i class="ri-shopping-cart-line ri-xl text-primary"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-3">
                    تصفح واطلب المنتجات
                </h3>
                <p class="text-gray-600">
                    استكشف مجموعة واسعة من المنتجات الغذائية واطلب مباشرة من الموردين.
                </p>
            </div>
        </div>
    </div>
</section>
<!-- Features Section -->
<section id="features" class="py-16">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12 fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-3">
                المميزات الرئيسية
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                اكتشف ما يجعل تزود المنصة المفضلة للربط بين مشتري الأغذية والموردين.
            </p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div
                class="bg-white p-6 rounded shadow-sm border border-gray-100 fade-up"
            >
                <div
                    class="w-12 h-12 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-4"
                >
                    <i class="ri-map-2-line ri-lg text-secondary"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    المطابقة حسب الموقع
                </h3>
                <p class="text-gray-600">
                    تواصل مع الموردين في منطقتك للحصول على توصيل أسرع وتكاليف أقل.
                </p>
            </div>
            <div
                class="bg-white p-6 rounded shadow-sm border border-gray-100 fade-up"
            >
                <div
                    class="w-12 h-12 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-4"
                >
                    <i class="ri-store-2-line ri-lg text-secondary"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    فئات منتجات متنوعة
                </h3>
                <p class="text-gray-600">
                    الوصول إلى مجموعة متنوعة من المنتجات الغذائية المصنفة لسهولة
                    التصفح والطلب.
                </p>
            </div>
            <div
                class="bg-white p-6 rounded shadow-sm border border-gray-100 fade-up"
            >
                <div
                    class="w-12 h-12 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-4"
                >
                    <i class="ri-building-line ri-lg text-secondary"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    اتصال مباشر بالمصنع
                </h3>
                <p class="text-gray-600">
                    اطلب مباشرة من المصنع، مما يلغي الوسطاء ويقلل التكاليف.
                </p>
            </div>
            <div
                class="bg-white p-6 rounded shadow-sm border border-gray-100 fade-up"
            >
                <div
                    class="w-12 h-12 bg-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-4"
                >
                    <i class="ri-award-line ri-lg text-secondary"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    منتجات غذائية عالية الجودة
                </h3>
                <p class="text-gray-600">
                    يتم التحقق من جميع الموردين لضمان تلبيتهم لمعايير الجودة العالية.
                </p>
            </div>
        </div>
    </div>
</section>
<!-- Supplier Coverage Section -->
<section id="suppliers" class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12 fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-3">
                شبكة موردينا
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                لقد تعاونا مع 4 موردين رئيسيين للأغذية يقعون في مواقع استراتيجية لخدمتك بشكل أفضل.
            </p>
        </div>
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <div class="bg-white p-6 rounded shadow-md fade-in">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">
                        مواقع الموردين
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div
                                class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1"
                            >
                                <span class="text-white font-medium">1</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">
                                    مورد المنطقة الشمالية
                                </h4>
                                <p class="text-gray-600">
                                    متخصص في منتجات الألبان والمنتجات الطازجة
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div
                                class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1"
                            >
                                <span class="text-white font-medium">2</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">
                                    مورد المنطقة الشرقية
                                </h4>
                                <p class="text-gray-600">
                                    متخصص في الحبوب والأرز والبقوليات
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div
                                class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1"
                            >
                                <span class="text-white font-medium">3</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">
                                    مورد المنطقة الغربية
                                </h4>
                                <p class="text-gray-600">
                                    متخصص في الأغذية المصنعة والمشروبات
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div
                                class="w-8 h-8 bg-primary rounded-full flex items-center justify-center mr-3 mt-1"
                            >
                                <span class="text-white font-medium">4</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">
                                    مورد المنطقة الجنوبية
                                </h4>
                                <p class="text-gray-600">
                                    متخصص في اللحوم والدواجن والمأكولات البحرية
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="h-96 bg-white rounded shadow-md overflow-hidden">
                <div class="h-full w-full relative">
                    <img
                        src="https://public.readdy.ai/gen_page/map_placeholder_1280x720.png"
                        alt="Supplier Map"
                        class="w-full h-full object-cover object-top"
                    />
                    <div
                        class="absolute top-1/4 left-1/4 w-4 h-4 bg-primary rounded-full"
                    ></div>
                    <div
                        class="absolute top-1/3 right-1/3 w-4 h-4 bg-primary rounded-full"
                    ></div>
                    <div
                        class="absolute bottom-1/3 left-1/3 w-4 h-4 bg-primary rounded-full"
                    ></div>
                    <div
                        class="absolute bottom-1/4 right-1/4 w-4 h-4 bg-primary rounded-full"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Product Categories -->
<section id="products" class="py-16">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12 fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-3">
                فئات المنتجات
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                تصفح من خلال مجموعة واسعة من فئات المنتجات الغذائية للعثور على ما تحتاجه بالضبط.
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div
                class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up"
            >
                <div class="h-48 overflow-hidden">
                    <img
                        src="https://readdy.ai/api/search-image?query=Fresh%20fruits%20and%20vegetables%20arranged%20neatly%20on%20a%20clean%20surface%2C%20vibrant%20colors%2C%20healthy%20food%20concept%2C%20high%20quality%20produce&width=400&height=300&seq=12346&orientation=landscape"
                        alt="المنتجات الطازجة"
                        class="w-full h-full object-cover object-top"
                    />
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        المنتجات الطازجة
                    </h3>
                    <p class="text-gray-600 text-sm">فواكه وخضروات وأعشاب</p>
                </div>
            </div>
            <div
                class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up"
            >
                <div class="h-48 overflow-hidden">
                    <img
                        src="https://readdy.ai/api/search-image?query=Various%20dairy%20products%20including%20milk%20bottles%2C%20cheese%20wheels%2C%20yogurt%20cups%20on%20a%20clean%20white%20surface%2C%20dairy%20food%20concept&width=400&height=300&seq=12347&orientation=landscape"
                        alt="منتجات الألبان"
                        class="w-full h-full object-cover object-top"
                    />
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        منتجات الألبان
                    </h3>
                    <p class="text-gray-600 text-sm">
                        حليب وجبن وزبادي والمزيد
                    </p>
                </div>
            </div>
            <div
                class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up"
            >
                <div class="h-48 overflow-hidden">
                    <img
                        src="https://readdy.ai/api/search-image?query=Various%20cuts%20of%20meat%2C%20poultry%2C%20and%20seafood%20arranged%20on%20a%20clean%20surface%2C%20high%20quality%20protein%20food%20concept&width=400&height=300&seq=12348&orientation=landscape"
                        alt="اللحوم والمأكولات البحرية"
                        class="w-full h-full object-cover object-top"
                    />
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        اللحوم والمأكولات البحرية
                    </h3>
                    <p class="text-gray-600 text-sm">
                        لحوم بقر ودواجن وأسماك ومحار
                    </p>
                </div>
            </div>
            <div
                class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up"
            >
                <div class="h-48 overflow-hidden">
                    <img
                        src="https://readdy.ai/api/search-image?query=Various%20grains%2C%20rice%2C%20pasta%2C%20and%20legumes%20in%20containers%20on%20a%20clean%20surface%2C%20staple%20food%20ingredients%20concept&width=400&height=300&seq=12349&orientation=landscape"
                        alt="الحبوب والمعكرونة"
                        class="w-full h-full object-cover object-top"
                    />
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        الحبوب والمعكرونة
                    </h3>
                    <p class="text-gray-600 text-sm">
                        أرز وقمح ومعكرونة وبقوليات
                    </p>
                </div>
            </div>
            <div
                class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up"
            >
                <div class="h-48 overflow-hidden">
                    <img
                        src="https://readdy.ai/api/search-image?query=Assorted%20baked%20goods%20including%20bread%2C%20pastries%2C%20and%20cakes%20on%20a%20clean%20surface%2C%20bakery%20food%20concept&width=400&height=300&seq=12350&orientation=landscape"
                        alt="منتجات المخابز"
                        class="w-full h-full object-cover object-top"
                    />
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        منتجات المخابز
                    </h3>
                    <p class="text-gray-600 text-sm">خبز وحلويات وكيك</p>
                </div>
            </div>
            <div
                class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up"
            >
                <div class="h-48 overflow-hidden">
                    <img
                        src="https://readdy.ai/api/search-image?query=Various%20beverages%20including%20bottled%20water%2C%20juices%2C%20and%20soft%20drinks%20on%20a%20clean%20surface%2C%20drinks%20concept&width=400&height=300&seq=12351&orientation=landscape"
                        alt="المشروبات"
                        class="w-full h-full object-cover object-top"
                    />
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        المشروبات
                    </h3>
                    <p class="text-gray-600 text-sm">
                        ماء وعصائر ومشروبات غازية
                    </p>
                </div>
            </div>
            <div
                class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up"
            >
                <div class="h-48 overflow-hidden">
                    <img
                        src="https://readdy.ai/api/search-image?query=Various%20canned%20and%20jarred%20foods%20neatly%20arranged%20on%20a%20clean%20surface%2C%20preserved%20food%20concept&width=400&height=300&seq=12352&orientation=landscape"
                        alt="المعلبات"
                        class="w-full h-full object-cover object-top"
                    />
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        المعلبات
                    </h3>
                    <p class="text-gray-600 text-sm">
                        فواكه وخضروات محفوظة والمزيد
                    </p>
                </div>
            </div>
            <div
                class="bg-white rounded shadow-sm overflow-hidden border border-gray-100 scale-up"
            >
                <div class="h-48 overflow-hidden">
                    <img
                        src="https://readdy.ai/api/search-image?query=Various%20snacks%2C%20chips%2C%20nuts%2C%20and%20dried%20fruits%20on%20a%20clean%20surface%2C%20snack%20food%20concept&width=400&height=300&seq=12353&orientation=landscape"
                        alt="الوجبات الخفيفة"
                        class="w-full h-full object-cover object-top"
                    />
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">الوجبات الخفيفة</h3>
                    <p class="text-gray-600 text-sm">رقائق ومكسرات وفواكه مجففة</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- CTA Section -->
<section class="py-16 bg-primary bg-opacity-5">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">
                هل أنت مستعد لتبسيط سلسلة التوريد الغذائية الخاصة بك؟
            </h2>
            <p class="text-gray-600 mb-8 text-lg">
                انضم إلى تزود اليوم وتواصل مع موردي الأغذية المحليين للحصول على أفضل المنتجات بأسعار تنافسية.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button
                    class="px-8 py-3 bg-primary text-white font-medium hover:bg-opacity-90 transition-colors !rounded-button whitespace-nowrap flex items-center justify-center gap-2"
                >
                    <i class="ri-download-2-line"></i>
                    تثبيت التطبيق
                </button>
                <button
                    class="px-8 py-3 text-primary border border-primary font-medium hover:bg-primary hover:text-white transition-colors !rounded-button whitespace-nowrap"
                >
                    اتصل بالمبيعات
                </button>
            </div>
        </div>
    </div>
</section>
<!-- Newsletter Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-3">ابق على اطلاع</h2>
            <p class="text-gray-600 mb-6">
                اشترك في نشرتنا البريدية لتلقي تحديثات حول الموردين والمنتجات الجديدة.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <input
                    type="email"
                    placeholder="أدخل بريدك الإلكتروني"
                    class="flex-1 px-4 py-3 border border-gray-300 rounded focus:border-primary custom-input"
                />
                <button
                    class="px-6 py-3 bg-primary text-white font-medium hover:bg-opacity-90 transition-colors !rounded-button whitespace-nowrap"
                >
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
                <p class="text-gray-400 mb-4">
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
                        <span class="text-gray-400"
                        >شارع الغذاء 123، مدينة التوريد، 12345</span
                        >
                    </li>
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center mr-3 mt-1">
                            <i class="ri-phone-line text-gray-400"></i>
                        </div>
                        <span class="text-gray-400">+1 (234) 567-8900</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-5 h-5 flex items-center justify-center mr-3 mt-1">
                            <i class="ri-mail-line text-gray-400"></i>
                        </div>
                        <span class="text-gray-400">info@tzwad.com</span>
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
</body>
</html>
