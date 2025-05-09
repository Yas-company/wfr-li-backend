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
    'registration_failed' => 'فشل التسجيل. يرجى المحاولة مرة أخرى لاحقاً',
    'login_successful' => 'تم تسجيل الدخول بنجاح',
    'login_failed' => 'فشل تسجيل الدخول. يرجى المحاولة مرة أخرى لاحقاً',
    'logout_successful' => 'تم تسجيل الخروج بنجاح',
    'logout_failed' => 'فشل تسجيل الخروج. يرجى المحاولة مرة أخرى لاحقاً',
    'profile_failed' => 'فشل في جلب الملف الشخصي. يرجى المحاولة مرة أخرى لاحقاً',

    // Password Change Messages
    'password_changed_successful' => 'تم تغيير كلمة المرور بنجاح',
    'password_change_failed' => 'فشل في تغيير كلمة المرور',
    'invalid_current_password' => 'كلمة المرور الحالية غير صحيحة',

    // OTP Messages
    'otp_sent' => 'تم إرسال رمز التحقق بنجاح',
    'otp_send_failed' => 'فشل في إرسال رمز التحقق',
    'otp_verified' => 'تم التحقق من رمز التحقق بنجاح',
    'otp_verification_failed' => 'فشل في التحقق من رمز التحقق',
    'invalid_otp' => 'رمز التحقق غير صحيح أو منتهي الصلاحية',
    'otp_expired' => 'رمز التحقق منتهي الصلاحية',

    // Password Reset Messages
    'password_reset_successful' => 'تم إعادة تعيين كلمة المرور بنجاح',
    'password_reset_failed' => 'فشل في إعادة تعيين كلمة المرور',

    // Validation Messages
    'validation' => [
        'required' => [
            'name' => 'يرجى إدخال الاسم',
            'phone' => 'يرجى إدخال رقم الهاتف',
            'address' => 'يرجى إدخال العنوان',
            'location' => 'يرجى إدخال الموقع',
            'business_name' => 'يرجى إدخال اسم العمل',
            'lic_id' => 'يرجى إدخال رقم الترخيص',
            'email' => 'يرجى إدخال البريد الإلكتروني',
            'password' => 'يرجى إدخال كلمة المرور',
            'otp' => 'يرجى إدخال رمز التحقق',
            'current_password' => 'يرجى إدخال كلمة المرور الحالية',
            'password_confirmation' => 'يرجى تأكيد كلمة المرور'
        ],
        'unique' => [
            'phone' => 'رقم الهاتف مسجل مسبقاً',
            'email' => 'البريد الإلكتروني مسجل مسبقاً',
            'lic_id' => 'رقم الترخيص مسجل مسبقاً',
        ],
        'email' => 'يرجى إدخال بريد إلكتروني صحيح',
        'password' => [
            'min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل',
            'confirmed' => 'تأكيد كلمة المرور غير متطابق',
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