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

    // Cart Messages
    'cart' => [
        'added' => 'تمت إضافة المنتج إلى السلة بنجاح',
        'removed' => 'تمت إزالة المنتج من السلة بنجاح',
        'updated' => 'تم تحديث كمية السلة بنجاح',
        'empty' => 'السلة فارغة',
        'not_found' => 'لم يتم العثور على السلة',
        'item_not_found' => 'لم يتم العثور على عنصر السلة',
        'invalid_quantity' => 'الكمية غير صالحة',
        'invalid_product' => 'المنتج غير صالح',
        'product_not_found' => 'لم يتم العثور على المنتج',
        'insufficient_stock' => 'الكمية غير متوفرة في المخزون',
        'cannot_mix_products_from_different_suppliers' => 'لا يمكن دمج المنتجات من مزودين مختلفين',
        'insufficient_min_order_amount' => 'الحد الأدنى لقيمة الطلب من التاجر :supplier_name هو :min_order_amount. يرجى إضافة منتجات إضافية لتحقيق هذا المبلغ.',
        'order_type_not_allowed' => 'نوع الطلب غير مسموح',
    ],

    'orders' => [
        'order_status_updated' => 'تم تحديث حالة الطلب بنجاح',
        'invalid_transition' => 'لا يمكن تغيير حالة الطلب إلى الحالة المحددة',
    ],

    'users' => [
        'cannot_delete_last_address' => 'لا يمكن حذف العنوان الأخير',
        'at_least_one_default_address_required' => 'يجب تحديد عنوان افتراضي واحد على الأقل',
        'cannot_delete_address_attached_to_order' => 'لا يمكن حذف العنوان المرتبط بالطلب',
    ],

    'ratings' => [
        'rated_successfully' => 'تم التقييم بنجاح',
    ],

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
    'invalid_phone' => 'رقم الهاتف غير صحيح',

    // Password Change Messages
    'password_changed_successful' => 'تم تغيير كلمة المرور بنجاح',
    'password_change_failed' => 'فشل تغيير كلمة المرور',
    'invalid_current_password' => 'كلمة المرور الحالية غير صحيحة',

    // OTP Messages
    'otp_sent' => 'تم إرسال رمز التحقق بنجاح',
    'otp_send_failed' => 'فشل في إرسال رمز التحقق',
    'otp_verified' => 'تم التحقق من رمز التحقق بنجاح',
    'otp_verification_failed' => 'فشل في التحقق من رمز التحقق',
    'invalid_otp' => 'رمز التحقق غير صحيح أو منتهي الصلاحية',
    'otp_expired' => 'رمز التحقق منتهي الصلاحية',
    'registration_verified' => 'تم التحقق من التسجيل بنجاح',

    // Password Reset Messages
    'password_reset_successful' => 'تم إعادة تعيين كلمة المرور بنجاح',
    'password_reset_failed' => 'فشل في إعادة تعيين كلمة المرور',

    // Validation Messages
    'validation' => [
        'required' => [
            'name' => 'الاسم مطلوب',
            'phone' => 'رقم الهاتف مطلوب',
            'country_code' => 'رمز الدولة مطلوب',
            'address' => 'يرجى إدخال العنوان',
            'business_name' => 'يرجى إدخال اسم العمل',
            'lic_id' => 'يرجى إدخال رقم الترخيص',
            'email' => 'البريد الإلكتروني مطلوب',
            'password' => 'كلمة المرور مطلوبة',
            'otp' => 'يرجى إدخال رمز التحقق',
            'current_password' => 'يرجى إدخال كلمة المرور الحالية',
            'password_confirmation' => 'يرجى تأكيد كلمة المرور',
            'product_id' => 'معرف المنتج مطلوب',
            'quantity' => 'الكمية مطلوبة',
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
            'otp' => 'يجب أن يتكون رمز التحقق من 6 أرقام',
        ],
        'exists' => [
            'phone' => 'رقم الهاتف غير مسجل',
            'product' => 'لم يتم العثور على المنتج',
        ],
        'integer' => [
            'quantity' => 'يجب أن تكون الكمية رقماً',
        ],
        'min' => [
            'quantity' => 'يجب أن تكون الكمية 1 على الأقل',
        ],
        'end_date' => [
            'after' => 'يجب أن تكون تاريخ الانتهاء بعد تاريخ البدء',
        ],
    ],

    // Favorites Messages
    'added_to_favorites' => 'تمت الإضافة إلى المفضلة',
    'removed_from_favorites' => 'تمت الإزالة من المفضلة',

    // Account Deletion Messages
    'account_deleted_successfully' => 'تم حذف الحساب بنجاح',
    'account_delete_failed' => 'فشل في حذف الحساب',

    // Supplier Pending Review Message
    'supplier_pending_review' => 'حساب المورد الخاص بك قيد المراجعة حالياً. يرجى الانتظار حتى تتم الموافقة من قبل المسؤول.',
    'supplier_registration_pending' => 'حساب المورد الخاص بك قيد انتظار موافقة المسؤول.',

    // Favorite Messages
    'favorite_status_updated_successfully' => 'تم تحديث حالة المفضلة بنجاح',
    'favorites_fetched_successfully' => 'تم جلب المفضلة بنجاح',

    // Supplier Setting Messages
    'supplier_setting_updated' => 'تم تحديث إعدادات المورد بنجاح',

    // supplier profile messages
    'supplier_profile_updated' => 'تم تحديث ملف المورد بنجاح',

    // request supplier profile messages
    'phone_already_exists' => 'رقم الهاتف مسجل مسبقاً',
    'email_already_exists' => 'البريد الإلكتروني مسجل مسبقاً',

    // Product Messages
    'product' => [
        'price' => [
            'min' => 'يجب أن يكون السعر أكبر من 0.01',
        ],
        'quantity' => [
            'min' => 'يجب أن يكون الكمية أكبر من 0',
        ],
        'category_id' => [
            'exists' => ' الفئة غير موجودة او لا تنتمي للمورد',
        ],
        'unit_type' => [
            'in' => 'نوع الوحدة غير صالح',
        ],
        'status' => [
            'in' => 'الحالة غير صالحة',
        ],
        'image' => [
            'required' => 'يجب أن يكون لديك صورة للمنتج',
            'image' => 'يجب أن يكون الملف صورة',
            'mimes' => 'يجب أن يكون الملف صورة',
            'max' => 'يجب إرفاق على الأكثر 5 صورة',
            'max_per_file' => 'يجب ألا يزيد حجم كل صورة عن 2 ميجابايت.',
        ],
        'min_order_quantity' => [
            'required' => 'يجب أن يكون لديك حد أدنى للطلب',
            'numeric' => 'يجب أن يكون الحد الأدنى للطلب رقماً',
        ],
    ],

    // Order Messages
    'order_reordered' => 'تم إعادة الطلب بنجاح',

    'products' => [
        'retrieved_successfully' => 'تم جلب المنتجات بنجاح',
        'not_found' => 'لم يتم العثور على المنتج',
        'similar_products_retrieved_successfully' => 'تم جلب المنتجات المماثلة بنجاح',
    ],

    'ads' => [
        'retrieved_successfully' => 'تم جلب الإعلانات بنجاح',
    ],

    'categories' => [
        'retrieved_successfully' => 'تم جلب الفئات بنجاح',
    ],

    'suppliers' => [
        'image_changed' => 'تم تحديث صورة المورد بنجاح',
    ],

    'category' => [
        'retrieved_successfully' => 'تم جلب الفئة بنجاح',
        'created_successfully' => 'تم إنشاء الفئة بنجاح',
        'updated_successfully' => 'تم تحديث الفئة بنجاح',
        'deleted_successfully' => 'تم حذف الفئة بنجاح',
    ],

    'errors' => [
        'unauthorized_category_access' => 'غير مصرح للوصول إلى الفئات',
        'unauthorized_category_creation' => 'غير مصرح لإنشاء فئة',
        'unauthorized_category_update' => 'غير مصرح لتحديث الفئة',
        'unauthorized_category_delete' => 'غير مصرح لحذف الفئة',
        'unauthorized_category_ownership' => 'غير مصرح للتعامل مع الفئة',
    ],

    'buyer' => [
        'profile_updated' => 'تم تحديث ملف المشتري بنجاح',
        'image_updated' => 'تم تحديث صورة المشتري بنجاح',
    ],

    'page' => [
        'fetched_successfully' => 'تم جلب الصفحة بنجاح',
        'slug_required' => 'يجب أن يكون لديك ',
        'slug_not_found' => 'لم يتم العثور على الصفحة',
    ],

];
