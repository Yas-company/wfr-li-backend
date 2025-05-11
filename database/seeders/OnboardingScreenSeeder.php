<?php

namespace Database\Seeders;

use App\Models\OnboardingScreen;
use Illuminate\Database\Seeder;

class OnboardingScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $screens = [
            [
                'title' => [
                    'en' => 'Welcome to Our App',
                    'ar' => 'مرحباً بك في تطبيقنا',
                ],
                'description' => [
                    'en' => 'Discover amazing features and services tailored just for you.',
                    'ar' => 'اكتشف الميزات والخدمات المذهلة المصممة خصيصاً لك.',
                ],
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => [
                    'en' => 'Explore Features',
                    'ar' => 'استكشف الميزات',
                ],
                'description' => [
                    'en' => 'Learn about all the amazing features that make our app unique.',
                    'ar' => 'تعرف على جميع الميزات المذهلة التي تجعل تطبيقنا فريداً.',
                ],
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => [
                    'en' => 'Get Started',
                    'ar' => 'ابدأ الآن',
                ],
                'description' => [
                    'en' => 'Ready to begin your journey? Let\'s get started!',
                    'ar' => 'هل أنت مستعد لبدء رحلتك؟ دعنا نبدأ!',
                ],
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($screens as $screen) {
            OnboardingScreen::updateOrCreate(['order' => $screen['order']], $screen);
        }
    }
}
