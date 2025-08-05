<?php

namespace App\Enums;

use App\Traits\HasLabel;

enum UnitType: int
{
    use HasLabel;

    case PIECE = 0;       // قطعة واحدة (منتج فردي)
    case DOZEN = 1;       // درزن (12 قطعة)
    case HALF_DOZEN = 2;  // نصف درزن (6 قطع)
    case SET = 3;         // طقم أو مجموعة (مثلاً طقم فناجين)
    case PACK = 4;        // عبوة (عدد غير محدد داخليًا)
    case BOX = 5;         // صندوق يحتوي على وحدات
    case CARTON = 6;      // كرتونة كبيرة (أكبر من الصندوق)
    case ROLL = 7;        // لفة (مثلاً لفة قماش أو ورق)
    case STRIP = 8;       // شريط دواء (يحتوي على عدة حبات)
    case TABLET = 9;      // حبة دواء واحدة
    case PAIR = 10;       // زوج (مثلاً: حذاء أو قفاز)

    case KG = 11;         // كيلوجرام (1000 جرام)
    case G = 12;          // جرام
    case MG = 13;         // مليجرام (0.001 جرام)
    case TON = 14;        // طن (1000 كيلوجرام)
    case OUNCE = 15;      // أوقية (حوالي 28.3 جرام)
    case POUND = 16;      // باوند (حوالي 453.6 جرام)

    case LITER = 17;      // لتر (1000 مليلتر)
    case ML = 18;         // مليلتر (0.001 لتر)
    case GALLON = 19;     // جالون (حوالي 3.78 لتر)
    case BOTTLE = 20;     // زجاجة (وحدة مغلقة من السوائل)
    case CAN = 21;        // علبة معدنية (مشروبات أو معلبات)

    case METER = 22;      // متر (100 سم)
    case CM = 23;         // سنتيمتر
    case MM = 24;         // مليمتر
    case INCH = 25;       // بوصة (2.54 سم تقريبًا)
    case FOOT = 26;       // قدم (30.48 سم)
    case YARD = 27;       // ياردة (91.44 سم)

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // public function getArabicLabel(): string
    // {
    //     return match ($this) {
    //         self::PIECE => 'قطعة',
    //         self::DOZEN => 'درزن (12 قطعة)',
    //         self::HALF_DOZEN => 'نصف درزن (6 قطع)',
    //         self::SET => 'طقم',
    //         self::PACK => 'عبوة',
    //         self::BOX => 'صندوق',
    //         self::CARTON => 'كرتونة',
    //         self::ROLL => 'لفة',
    //         self::STRIP => 'شريط',
    //         self::TABLET => 'حبة',
    //         self::PAIR => 'زوج',
    //         self::KG => 'كيلوجرام',
    //         self::G => 'جرام',
    //         self::MG => 'مليجرام',
    //         self::TON => 'طن',
    //         self::OUNCE => 'أوقية',
    //         self::POUND => 'باوند',
    //         self::LITER => 'لتر',
    //         self::ML => 'مليلتر',
    //         self::GALLON => 'جالون',
    //         self::BOTTLE => 'زجاجة',
    //         self::CAN => 'علبة',
    //         self::METER => 'متر',
    //         self::CM => 'سنتيمتر',
    //         self::MM => 'مليمتر',
    //         self::INCH => 'بوصة',
    //         self::FOOT => 'قدم',
    //         self::YARD => 'ياردة',
    //     };
    // }

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
