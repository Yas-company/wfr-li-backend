<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="preview" content="فاتورة #{{ $bill['order_number'] ?? '000000' }}">
    <title>فاتورة الطلب</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans text-gray-900">
    <div class="flex justify-center py-6">
        <div class="max-w-xl w-full mx-4 bg-white rounded-xl shadow-sm">
            <div class="text-center py-4">
                <img src="{{ url('/images/logo-raster-to-vector.svg') }}" alt="wfrli" class="mx-auto h-24">
                <h2 class="mt-3 text-xl font-semibold">فاتورة طلبك</h2>
                <p class="text-sm text-gray-500 mt-1 mb-4">طلب رقم #{{ $bill['order_number'] ?? '000000' }}</p>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm mb-4">مرحباً، {{ $bill['recipient_name'] ?? 'المشتري' }}.</p>
                <p class="text-sm mb-4">شكراً لشرائك. فيما يلي ملخص طلبك.</p>

                @php
                    $items = $bill['items'] ?? [];
                    $shipping = $bill['totals']['shipping'] ?? 0;
                    $tax = $bill['totals']['tax'] ?? 0;
                    $subtotalBefore = 0;
                    $subtotalAfter = 0;
                @endphp

                @if(count($items))
                    <table class="w-full text-sm border-collapse bg-white rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr class="border-b-2 border-gray-200">
                                <th class="px-4 py-3 text-right font-semibold text-gray-600">المنتج</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">الكمية</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600">سعر الوحدة<br/><span class="text-xs">(قبل)</span></th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600">سعر الوحدة<br/><span class="text-xs">(بعد)</span></th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600">المجموع<br/><span class="text-xs">(قبل)</span></th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600">المجموع<br/><span class="text-xs">(بعد)</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php
                                    $qty = (int)($item['quantity'] ?? 1);
                                    $unitBefore = (float)($item['unit_price_before'] ?? 0);
                                    $unitAfter = (float)($item['unit_price_after'] ?? $unitBefore);
                                    $lineBefore = $qty * $unitBefore;
                                    $lineAfter = $qty * $unitAfter;
                                    $subtotalBefore += $lineBefore;
                                    $subtotalAfter += $lineAfter;
                                @endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-right">{{ $item['name'] ?? 'Item' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $qty }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($unitBefore, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($unitAfter, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($lineBefore, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($lineAfter, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <div class="mt-3">
                    @php
                        $discountTotal = max(0, $subtotalBefore - $subtotalAfter);
                        $grandTotal = $subtotalAfter + $shipping + $tax;
                    @endphp

                    <div class="mt-6 space-y-3 bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <strong class="text-sm text-gray-600">المجموع الفرعي (قبل الخصم):</strong>
                            <span class="text-sm">{{ number_format($subtotalBefore, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-red-600">
                            <strong class="text-sm">الخصم:</strong>
                            <span class="text-sm">-{{ number_format($discountTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                            <strong class="text-sm text-gray-600">المجموع الفرعي (بعد الخصم):</strong>
                            <span class="text-sm">{{ number_format($subtotalAfter, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <strong class="text-sm text-gray-600">الشحن:</strong>
                            <span class="text-sm">{{ number_format($shipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <strong class="text-sm text-gray-600">الضريبة:</strong>
                            <span class="text-sm">{{ number_format($tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-200 pt-3 font-bold">
                            <strong class="text-sm">المجموع الكلي:</strong>
                            <span class="text-sm">{{ number_format($grandTotal, 2) }}</span>
                        </div>
                    </div>

                    @if(!empty($bill['cta_url']))
                        <p class="mt-3">
                            <a href="{{ $bill['cta_url'] }}" class="inline-block bg-gray-900 text-white text-sm py-2 px-4 rounded-md hover:bg-gray-800">عرض الطلب</a>
                        </p>
                    @endif
                </div>

                <div class="mt-8 pt-5 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-500">شكراً لاختيارك لنا!<br><strong>{{ config('app.name') }}</strong></p>
                    <p class="text-xs text-gray-400 mt-2">إذا كان لديك أي أسئلة حول طلبك، يرجى الاتصال بفريق الدعم لدينا.</p>
                    <p class="text-xs text-gray-400 mt-3">© {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>