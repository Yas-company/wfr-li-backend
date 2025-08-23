<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="preview" content="Supplier Bill #{{ $bill['order_number'] ?? '000000' }}">
    <title>Supplier Bill</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans text-gray-900">
    <div class="flex justify-center py-6">
        <div class="max-w-xl w-full mx-4 bg-white rounded-xl shadow-sm">
            <div class="text-center py-4">
                <img src="{{ url('/images/logo-raster-to-vector.svg') }}" alt="wfrli" class="mx-auto h-24">
                <h2 class="mt-3 text-xl font-semibold">Supplier Bill</h2>
                <p class="text-sm text-gray-500 mt-1 mb-4">Order #{{ $bill['order_number'] ?? '000000' }}</p>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm mb-4">Hello, {{ $bill['recipient_name'] ?? 'Supplier' }}.</p>
                <p class="text-sm mb-4">Items and mediator interest are shown below.</p>

                <div class="mt-5 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-base font-semibold text-gray-700 mb-3">Customer Information</h3>
                    @if(!empty($bill['buyer_details']))
                        <div class="mb-4">
                            <p class="font-bold text-sm text-gray-700 mb-1">Buyer Details:</p>
                            <p class="text-sm mb-0.5">Name: {{ $bill['buyer_details']['name'] ?? 'N/A' }}</p>
                            <p class="text-sm mb-0.5">Email: {{ $bill['buyer_details']['email'] ?? 'N/A' }}</p>
                            @if(!empty($bill['buyer_details']['phone']))
                                <p class="text-sm mb-0.5">Phone: {{ $bill['buyer_details']['phone'] }}</p>
                            @endif
                        </div>
                    @endif

                    @if(!empty($bill['shipping_address']))
                        <div>
                            <p class="font-bold text-sm text-gray-700 mb-1">Delivery Address:</p>
                            <p class="text-sm mb-0.5">{{ $bill['shipping_address']['name'] ?? 'N/A' }}</p>
                            <p class="text-sm mb-0.5">{{ $bill['shipping_address']['street'] ?? 'N/A' }}</p>
                            <p class="text-sm mb-0.5">{{ $bill['shipping_address']['city'] ?? 'N/A' }}</p>
                            @if(!empty($bill['shipping_address']['phone']))
                                <p class="text-sm mb-0.5">Phone: {{ $bill['shipping_address']['phone'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                @php
                    $items = $bill['items'] ?? [];
                    $shipping = $bill['totals']['shipping'] ?? 0;
                    $tax = $bill['totals']['tax'] ?? 0;
                    $mediatorInterest = (float)($bill['mediator_interest'] ?? 0);
                    $subtotalBefore = 0;
                    $subtotalAfter = 0;
                @endphp

                @if(count($items))
                    <table class="w-full text-sm border-collapse mt-4">
                        <thead>
                            <tr class="border-b border-gray-200 text-left">
                                <th class="py-2">Product</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Unit Price (Before)</th>
                                <th class="py-2 text-right">Unit Price (After)</th>
                                <th class="py-2 text-right">Line Total (Before)</th>
                                <th class="py-2 text-right">Line Total (After)</th>
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
                                <tr class="border-b border-gray-100">
                                    <td class="py-2">{{ $item['name'] ?? 'Item' }}</td>
                                    <td class="py-2 text-center">{{ $qty }}</td>
                                    <td class="py-2 text-right">{{ number_format($unitBefore, 2) }}</td>
                                    <td class="py-2 text-right">{{ number_format($unitAfter, 2) }}</td>
                                    <td class="py-2 text-right">{{ number_format($lineBefore, 2) }}</td>
                                    <td class="py-2 text-right">{{ number_format($lineAfter, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <div class="mt-3">
                    @php
                        $discountTotal = max(0, $subtotalBefore - $subtotalAfter);
                        $mediatorInterestAmount = $subtotalAfter * ($mediatorInterest / 100);
                        $grandTotal = $subtotalAfter + $shipping + $tax + $mediatorInterestAmount;
                    @endphp

                    <p class="text-sm mb-4"><strong>Subtotal (Before Discount):</strong> {{ number_format($subtotalBefore, 2) }}</p>
                    <p class="text-sm mb-4"><strong>Discount:</strong> -{{ number_format($discountTotal, 2) }}</p>
                    <p class="text-sm mb-4"><strong>Subtotal (After Discount):</strong> {{ number_format($subtotalAfter, 2) }}</p>
                    <p class="text-sm mb-4"><strong>Mediator Interest ({{ $mediatorInterest }}%):</strong> {{ number_format($mediatorInterestAmount, 2) }}</p>
                    <p class="text-sm mb-4"><strong>Shipping:</strong> {{ number_format($shipping, 2) }}</p>
                    <p class="text-sm mb-4"><strong>Tax:</strong> {{ number_format($tax, 2) }}</p>
                    <p class="text-sm mb-4"><strong>Grand Total (Including Mediator Interest):</strong> {{ number_format($grandTotal, 2) }}</p>

                    @if(!empty($bill['cta_url']))
                        <p class="mt-3">
                            <a href="{{ $bill['cta_url'] }}" class="inline-block bg-gray-900 text-white text-sm py-2 px-4 rounded-md hover:bg-gray-800">View Order</a>
                        </p>
                    @endif
                </div>

                <div class="mt-8 pt-5 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-500">Thank you for your partnership!<br><strong>{{ config('app.name') }}</strong></p>
                    <p class="text-xs text-gray-400 mt-2">If you have any questions about this order or need assistance, please contact our supplier support team.</p>
                    <p class="text-xs text-gray-400 mt-3">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>