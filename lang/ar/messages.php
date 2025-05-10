<?php

return [
    // General Messages
    'success' => 'تمت العملية بنجاح',
    'error' => 'حدث خطأ',
    'created' => 'تم الإنشاء بنجاح',
    'updated' => 'تم التحديث بنجاح',
    'deleted' => 'تم الحذف بنجاح',
    'not_found' => 'لم يتم العثور على البيانات',
    'unauthorized' => 'غير مصرح',
    'forbidden' => 'غير مسموح',
    'validation_failed' => 'فشل التحقق',
    'server_error' => 'خطأ في الخادم',

    // Auth Messages
    'registration_successful' => 'تم التسجيل بنجاح',
    'registration_failed' => 'فشل التسجيل',
    'registration_verified' => 'تم التحقق من التسجيل بنجاح',
    'login_successful' => 'تم تسجيل الدخول بنجاح',
    'login_failed' => 'فشل تسجيل الدخول',
    'logout_successful' => 'تم تسجيل الخروج بنجاح',
    'logout_failed' => 'فشل تسجيل الخروج',
    'profile_failed' => 'فشل في الحصول على الملف الشخصي',
    'account_not_verified' => 'لم يتم التحقق من حسابك. يرجى التحقق من رقم هاتفك أولاً',
    'invalid_credentials' => 'بيانات الاعتماد غير صحيحة',

    // Password Change Messages
    'password_changed_successful' => 'تم تغيير كلمة المرور بنجاح',
    'password_change_failed' => 'فشل تغيير كلمة المرور',
    'invalid_current_password' => 'كلمة المرور الحالية غير صحيحة',

    // OTP Messages
    'otp_sent' => 'تم إرسال رمز التحقق',
    'otp_send_failed' => 'فشل في إرسال رمز التحقق',
    'otp_verified' => 'تم التحقق من رمز التحقق بنجاح',
    'otp_verification_failed' => 'فشل التحقق من رمز التحقق',
    'invalid_otp' => 'رمز التحقق غير صحيح',
    'otp_expired' => 'رمز التحقق منتهي الصلاحية',

    // Password Reset Messages
    'password_reset_successful' => 'تم إعادة تعيين كلمة المرور بنجاح',
    'password_reset_failed' => 'فشل إعادة تعيين كلمة المرور',

    // Validation Messages
    'validation' => [
        'required' => [
            'name' => 'الاسم مطلوب',
            'phone' => 'رقم الهاتف مطلوب',
            'email' => 'البريد الإلكتروني مطلوب',
            'password' => 'كلمة المرور مطلوبة',
            'address' => 'العنوان مطلوب',
            'location' => 'الموقع مطلوب',
            'business_name' => 'اسم العمل مطلوب',
            'lic_id' => 'رقم الترخيص مطلوب',
        ],
        'unique' => [
            'phone' => 'رقم الهاتف مستخدم بالفعل',
            'email' => 'البريد الإلكتروني مستخدم بالفعل',
            'lic_id' => 'رقم الترخيص مستخدم بالفعل',
        ],
        'email' => 'البريد الإلكتروني غير صحيح',
        'password' => [
            'confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل',
            'mixed_case' => 'يجب أن تحتوي كلمة المرور على أحرف كبيرة وصغيرة',
            'numbers' => 'يجب أن تحتوي كلمة المرور على أرقام',
            'symbols' => 'يجب أن تحتوي كلمة المرور على رموز',
        ],
        'size' => [
            'otp' => 'يجب أن يتكون رمز التحقق من 6 أرقام'
        ],
        'exists' => [
            'phone' => 'رقم الهاتف غير مسجل'
        ]
    ],
]; 