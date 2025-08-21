<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html dir="ltr" lang="en"><head><meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/><meta name="x-apple-disable-message-reformatting"/></head><body style="background-color:#f6f6f6"><!--$--><div><meta name="preview" content="Supplier Bill #{{ $bill['order_number'] ?? '000000' }}" /></div><table border="0" width="100%" cellPadding="0" cellSpacing="0" role="presentation" align="center"><tbody><tr><td style="background-color:#f6f6f6;color:#111827;font-family:ui-sans-serif, system-ui"><table align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="max-width:600px;margin:0 auto;padding:24px;background-color:#ffffff;border-radius:12px"><tbody><tr style="width:100%"><td><table align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="text-align:center;padding-bottom:8px"><tbody><tr><td><div><img src="{{ url('/images/logo-raster-to-vector.svg') }}" width="120" height="auto" alt="wfrli" style="margin: 0 auto; display: block;" /></div><h2 style="margin:12px 0 0">Supplier Bill</h2><p style="font-size:12px;line-height:24px;color:#6b7280;margin-top:4px;margin-bottom:16px"><span>Order #{{ $bill["order_number"] ?? "000000" }}</span></p></td></tr></tbody></table><table align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="padding-top:8px"><tbody><tr><td><p style="font-size:14px;line-height:24px;margin-top:16px;margin-bottom:16px"><span>Hello, {{ $bill["recipient_name"] ?? "Supplier" }}.</span></p><p style="font-size:14px;line-height:24px;margin-top:8px;margin-bottom:16px">Items and mediator interest are shown below.</p></td></tr></tbody></table><table align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="margin-top:20px;padding:16px;background-color:#f9fafb;border-radius:8px"><tbody><tr><td><h3 style="margin:0 0 12px 0;font-size:16px;color:#374151">Customer Information</h3><div>
								<!-- Buyer Information -->
								@if(!empty($bill['buyer_details']))
								<div style="margin-bottom: 16px;">
									<p style="font-weight: bold; margin-bottom: 4px; color: #374151; font-size: 14px;">Buyer Details:</p>
									<p style="font-size: 14px; margin-bottom: 2px;">Name: {{ $bill['buyer_details']['name'] ?? 'N/A' }}</p>
									<p style="font-size: 14px; margin-bottom: 2px;">Email: {{ $bill['buyer_details']['email'] ?? 'N/A' }}</p>
									@if(!empty($bill['buyer_details']['phone']))
									<p style="font-size: 14px; margin-bottom: 2px;">Phone: {{ $bill['buyer_details']['phone'] }}</p>
									@endif
								</div>
								@endif

								<!-- Shipping Address -->
								@if(!empty($bill['shipping_address']))
								<div>
									<p style="font-weight: bold; margin-bottom: 4px; color: #374151; font-size: 14px;">Delivery Address:</p>
									<p style="font-size: 14px; margin-bottom: 2px;">{{ $bill['shipping_address']['name'] ?? 'N/A' }}</p>
									<p style="font-size: 14px; margin-bottom: 2px;">{{ $bill['shipping_address']['street'] ?? 'N/A' }}</p>
									<p style="font-size: 14px; margin-bottom: 2px;">{{ $bill['shipping_address']['city'] ?? 'N/A' }}</p>
									@if(!empty($bill['shipping_address']['phone']))
									<p style="font-size: 14px; margin-bottom: 2px;">Phone: {{ $bill['shipping_address']['phone'] }}</p>
									@endif
								</div>
								@endif
								</div></td></tr></tbody></table><table align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="margin-top:16px"><tbody><tr><td><div>@php
    $items = $bill['items'] ?? [];
    $shipping = $bill['totals']['shipping'] ?? 0;
    $tax = $bill['totals']['tax'] ?? 0;
    $mediatorInterest = (float)($bill['mediator_interest'] ?? 0);
    $subtotalBefore = 0;
    $subtotalAfter = 0;
@endphp

@if(count($items))
<table role="presentation" width="100%" cellpadding="8" cellspacing="0" border="0" style="border-collapse:collapse;font-size:14px">
  <thead>
    <tr style="text-align:left;border-bottom:1px solid #e5e7eb">
      <th>Product</th>
      <th style="text-align:center">Qty</th>
      <th style="text-align:right">Unit Price (Before)</th>
      <th style="text-align:right">Unit Price (After)</th>
      <th style="text-align:right">Line Total (Before)</th>
      <th style="text-align:right">Line Total (After)</th>
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
    <tr style="border-bottom:1px solid #f3f4f6">
      <td>{{ $item['name'] ?? 'Item' }}</td>
      <td style="text-align:center">{{ $qty }}</td>
      <td style="text-align:right">{{ number_format($unitBefore, 2) }}</td>
      <td style="text-align:right">{{ number_format($unitAfter, 2) }}</td>
      <td style="text-align:right">{{ number_format($lineBefore, 2) }}</td>
      <td style="text-align:right">{{ number_format($lineAfter, 2) }}</td>
    </tr>
  @endforeach
  </tbody>
</table>
@endif</div></td></tr></tbody></table><table align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="margin-top:12px"><tbody><tr><td><div>@php
    $discountTotal = max(0, $subtotalBefore - $subtotalAfter);
    // Calculate mediator interest as percentage of subtotal after discount
    $mediatorInterestAmount = $subtotalAfter * ($mediatorInterest / 100);
    $grandTotal = $subtotalAfter + $shipping + $tax + $mediatorInterestAmount;
@endphp

<p><strong>Subtotal (Before Discount):</strong> {{ number_format($subtotalBefore, 2) }}</p>

<p><strong>Discount:</strong> -{{ number_format($discountTotal, 2) }}</p>

<p><strong>Subtotal (After Discount):</strong> {{ number_format($subtotalAfter, 2) }}</p>

<p><strong>Mediator Interest ({{ $mediatorInterest }}%):</strong> {{ number_format($mediatorInterestAmount, 2) }}</p>

<p><strong>Shipping:</strong> {{ number_format($shipping, 2) }}</p>

<p><strong>Tax:</strong> {{ number_format($tax, 2) }}</p>

<p><strong>Grand Total (Including Mediator Interest):</strong> {{ number_format($grandTotal, 2) }}</p>

@if(!empty($bill['cta_url']))
<p style="margin-top:12px">
  <a href="{{ $bill['cta_url'] }}" style="display:inline-block;background:#111827;color:#fff;text-decoration:none;padding:10px 14px;border-radius:6px">View Order</a>
</p>
@endif</div></td></tr></tbody></table><table align="center" width="100%" border="0" cellPadding="0" cellSpacing="0" role="presentation" style="margin-top:32px;padding-top:20px;border-top:1px solid #e5e7eb"><tbody><tr><td><p style="font-size:14px;line-height:24px;color:#6b7280;text-align:center;margin-top:16px;margin-bottom:16px"><span>Thank you for your partnership!<br /><strong>{{ config("app.name") }}</strong></span></p><p style="font-size:12px;line-height:24px;color:#9ca3af;text-align:center;margin-top:8px;margin-bottom:16px">If you have any questions about this order or need assistance, please contact our supplier support team.</p><p style="font-size:11px;line-height:24px;color:#9ca3af;text-align:center;margin-top:12px;margin-bottom:16px"><span>© {{ date("Y") }} {{ config("app.name") }}. All rights reserved.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><!--7--><!--/$--></body></html>