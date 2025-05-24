<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Terms and Conditions
        Page::updateOrCreate(
            ['slug' => 'terms-and-conditions'],
            [
                'title' => [
                    'en' => 'Terms and Conditions',
                    'ar' => 'الشروط والأحكام'
                ],
                'content' => [
                    'en' => '<h2>Terms and Conditions</h2>
<p>Welcome to our platform. By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>

<h3>1. Use License</h3>
<p>Permission is granted to temporarily download one copy of the materials (information or software) on our website for personal, non-commercial transitory viewing only.</p>

<h3>2. Disclaimer</h3>
<p>The materials on our website are provided on an \'as is\' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>

<h3>3. Limitations</h3>
<p>In no event shall we or our suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on our website.</p>',
                    'ar' => '<h2>الشروط والأحكام</h2>
<p>مرحباً بك في منصتنا. من خلال الوصول إلى هذا الموقع واستخدامه، فإنك تقبل وتوافق على الالتزام بشروط وأحكام هذا الاتفاق.</p>

<h3>1. ترخيص الاستخدام</h3>
<p>يتم منح الإذن لتنزيل نسخة واحدة مؤقتة من المواد (المعلومات أو البرامج) على موقعنا الإلكتروني للعرض المؤقت الشخصي غير التجاري فقط.</p>

<h3>2. إخلاء المسؤولية</h3>
<p>يتم تقديم المواد على موقعنا على أساس "كما هي". نحن لا نقدم أي ضمانات، صريحة أو ضمنية، وبموجب هذا نتنصل وننفي جميع الضمانات الأخرى بما في ذلك، دون قيود، الضمانات الضمنية أو شروط القابلية للتسويق، أو اللياقة لغرض معين، أو عدم انتهاك الملكية الفكرية أو أي انتهاك آخر للحقوق.</p>

<h3>3. القيود</h3>
<p>في أي حال من الأحوال لن نكون أو موردينا مسؤولين عن أي أضرار (بما في ذلك، دون قيود، الأضرار الناجمة عن فقدان البيانات أو الربح، أو بسبب انقطاع الأعمال) الناشئة عن استخدام أو عدم القدرة على استخدام المواد على موقعنا.</p>'
                ],
                'is_active' => true,
            ]
        );

        // Privacy Policy
        Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية'
                ],
                'content' => [
                    'en' => '<h2>Privacy Policy</h2>
<p>This Privacy Policy describes how we collect, use, and handle your personal information when you use our website.</p>

<h3>1. Information We Collect</h3>
<p>We collect information that you provide directly to us, including but not limited to your name, email address, and any other information you choose to provide.</p>

<h3>2. How We Use Your Information</h3>
<p>We use the information we collect to provide, maintain, and improve our services, to develop new ones, and to protect our company and our users.</p>

<h3>3. Information Sharing</h3>
<p>We do not share your personal information with companies, organizations, or individuals outside of our company except in the following cases:</p>
<ul>
    <li>With your consent</li>
    <li>For legal reasons</li>
    <li>With our service providers</li>
</ul>',
                    'ar' => '<h2>سياسة الخصوصية</h2>
<p>تصف سياسة الخصوصية هذه كيفية جمع واستخدام ومعالجة معلوماتك الشخصية عند استخدام موقعنا.</p>

<h3>1. المعلومات التي نجمعها</h3>
<p>نقوم بجمع المعلومات التي تقدمها لنا مباشرة، بما في ذلك على سبيل المثال لا الحصر اسمك وعنوان بريدك الإلكتروني وأي معلومات أخرى تختار تقديمها.</p>

<h3>2. كيفية استخدام معلوماتك</h3>
<p>نستخدم المعلومات التي نجمعها لتقديم وصيانة وتحسين خدماتنا، وتطوير خدمات جديدة، وحماية شركتنا ومستخدمينا.</p>

<h3>3. مشاركة المعلومات</h3>
<p>نحن لا نشارك معلوماتك الشخصية مع الشركات أو المنظمات أو الأفراد خارج شركتنا إلا في الحالات التالية:</p>
<ul>
    <li>بموافقتك</li>
    <li>لأسباب قانونية</li>
    <li>مع مزودي الخدمة لدينا</li>
</ul>'
                ],
                'is_active' => true,
            ]
        );
    }
}
