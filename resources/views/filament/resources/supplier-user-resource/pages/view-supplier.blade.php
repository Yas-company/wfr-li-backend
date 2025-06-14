<div class="p-6 space-y-8">
    {{-- Status Banner --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $supplier->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $supplier->business_name }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span @class([
                        'px-4 py-2 rounded-full text-sm font-medium flex items-center gap-2',
                        'bg-yellow-100 text-yellow-800 border border-yellow-200' => $supplier->status === \App\Enums\UserStatus::PENDING,
                        'bg-green-100 text-green-800 border border-green-200' => $supplier->status === \App\Enums\UserStatus::APPROVED,
                        'bg-red-100 text-red-800 border border-red-200' => $supplier->status === \App\Enums\UserStatus::REJECTED,
                    ])>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ match($supplier->status) {
                            \App\Enums\UserStatus::PENDING => 'قيد الانتظار',
                            \App\Enums\UserStatus::APPROVED => 'تم القبول',
                            \App\Enums\UserStatus::REJECTED => 'مرفوض',
                        } }}
                    </span>
                    <span @class([
                        'px-4 py-2 rounded-full text-sm font-medium flex items-center gap-2',
                        'bg-green-100 text-green-800 border border-green-200' => $supplier->is_verified,
                        'bg-red-100 text-red-800 border border-red-200' => !$supplier->is_verified,
                    ])>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $supplier->is_verified ? 'مفعل' : 'غير مفعل' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Basic Info Card --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">المعلومات الأساسية</h3>
            </div>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">الاسم</label>
                            <p class="text-base text-gray-900">{{ $supplier->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</label>
                            <p class="text-base text-gray-900">{{ $supplier->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">رقم الهاتف</label>
                            <p class="text-base text-gray-900">{{ $supplier->phone }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">اسم العمل</label>
                            <p class="text-base text-gray-900">{{ $supplier->business_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">المجال</label>
                            <p class="text-base text-gray-900">{{ $supplier->field?->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">العنوان</label>
                            <p class="text-base text-gray-900">{{ $supplier->address }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Documents Card --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">المستندات</h3>
            </div>
            
            <div class="space-y-4">
                {{-- License Document --}}
                <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">رخصة العمل</h4>
                            <p class="mt-1 text-sm text-gray-500">المستند الرسمي للعمل</p>
                        </div>
                        @if($supplier->license_attachment)
                            <a href="{{ Storage::url($supplier->license_attachment) }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                عرض المستند
                            </a>
                        @else
                            <span class="text-sm text-gray-500">لا يوجد مستند</span>
                        @endif
                    </div>
                </div>

                {{-- Commercial Register Document --}}
                <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">السجل التجاري</h4>
                            <p class="mt-1 text-sm text-gray-500">وثيقة السجل التجاري</p>
                        </div>
                        @if($supplier->commercial_register_attachment)
                            <a href="{{ Storage::url($supplier->commercial_register_attachment) }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                عرض المستند
                            </a>
                        @else
                            <span class="text-sm text-gray-500">لا يوجد مستند</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 