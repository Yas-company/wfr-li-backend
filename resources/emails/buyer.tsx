import * as React from 'react';
import { Html, Head, Preview, Body, Container, Text, Heading, Section, Img } from '@react-email/components';

type BillItem = {
	name: string;
	quantity: number;
	unit_price_before: number;
	unit_price_after: number;
};

type BillTotals = {
	shipping: number;
	tax: number;
};

type BillData = {
	order_number: string | number;
	recipient_name: string;
	email_subject: string;
	items: BillItem[];
	totals: BillTotals;
	cta_url?: string | null;
	note?: string | null;
};

type BuyerEmailProps = {
	bill?: BillData;
	buyerName?: string;
	orderNumber?: string;
};

export default function BuyerEmail(props: BuyerEmailProps) {
	// Use bill data if provided, otherwise fall back to props or defaults
	const bill = props.bill;
	const buyerName = bill?.recipient_name || props.buyerName || 'Buyer';
	const orderNumber = bill?.order_number || props.orderNumber || '000000';
	const items = bill?.items || [];
	const totals = bill?.totals || { shipping: 0, tax: 0 };
	
	// Calculate totals when bill data is available
	let subtotalBefore = 0;
	let subtotalAfter = 0;
	if (items.length > 0) {
		items.forEach(item => {
			const lineBefore = item.quantity * item.unit_price_before;
			const lineAfter = item.quantity * item.unit_price_after;
			subtotalBefore += lineBefore;
			subtotalAfter += lineAfter;
		});
	}
	const discountTotal = Math.max(0, subtotalBefore - subtotalAfter);
	const grandTotal = subtotalAfter + totals.shipping + totals.tax;
	return (
		<Html>
			<Head />
			<div dangerouslySetInnerHTML={{ __html: '<meta name="preview" content="Receipt #{{ $bill[\'order_number\'] ?? \'000000\' }}" />' }} />
			<Body style={{ backgroundColor: '#f6f6f6', color: '#111827', fontFamily: 'ui-sans-serif, system-ui' }}>
				<Container style={{ maxWidth: '600px', margin: '0 auto', padding: '24px', backgroundColor: '#ffffff', borderRadius: '12px' }}>
					<Section style={{ textAlign: 'center', paddingBottom: '8px' }}>
						<div dangerouslySetInnerHTML={{ 
							__html: '<img src="{{ url(\'/images/logo-raster-to-vector.svg\') }}" width="120" height="auto" alt="wfrli" style="margin: 0 auto; display: block;" />' 
						}} />
						<Heading as="h2" style={{ margin: '12px 0 0' }}>Your Order Bill</Heading>
						<Text style={{ fontSize: '12px', color: '#6b7280', marginTop: '4px' }}>
							<span dangerouslySetInnerHTML={{ __html: 'Order #{{ $bill["order_number"] ?? "000000" }}' }} />
						</Text>
					</Section>

					<Section style={{ paddingTop: '8px' }}>
						<Text>
							<span dangerouslySetInnerHTML={{ __html: 'Hello, {{ $bill["recipient_name"] ?? "Buyer" }}.' }} />
						</Text>
						<Text style={{ marginTop: '8px' }}>Thanks for your purchase. Below is a summary of your order.</Text>
					</Section>

					<Section style={{ marginTop: '16px' }}>
						{/* Render table with React when bill data is available */}
						{bill && items.length > 0 ? (
							<table role="presentation" width="100%" cellPadding="8" cellSpacing="0" border="0" style={{ borderCollapse: 'collapse', fontSize: '14px' }}>
								<thead>
									<tr style={{ textAlign: 'left', borderBottom: '1px solid #e5e7eb' }}>
										<th>Product</th>
										<th style={{ textAlign: 'center' }}>Qty</th>
										<th style={{ textAlign: 'right' }}>Unit Price (Before Discount)</th>
										<th style={{ textAlign: 'right' }}>Unit Price (After Discount)</th>
										<th style={{ textAlign: 'right' }}>Line Total (Before Discount)</th>
										<th style={{ textAlign: 'right' }}>Line Total (After Discount)</th>
									</tr>
								</thead>
								<tbody>
									{items.map((item, index) => {
										const lineBefore = item.quantity * item.unit_price_before;
										const lineAfter = item.quantity * item.unit_price_after;
										return (
											<tr key={index} style={{ borderBottom: '1px solid #f3f4f6' }}>
												<td>{item.name}</td>
												<td style={{ textAlign: 'center' }}>{item.quantity}</td>
												<td style={{ textAlign: 'right' }}>{item.unit_price_before.toFixed(2)}</td>
												<td style={{ textAlign: 'right' }}>{item.unit_price_after.toFixed(2)}</td>
												<td style={{ textAlign: 'right' }}>{lineBefore.toFixed(2)}</td>
												<td style={{ textAlign: 'right' }}>{lineAfter.toFixed(2)}</td>
											</tr>
										);
									})}
								</tbody>
							</table>
						) : (
							/* Fall back to Blade rendering when no bill data */
							<div
								dangerouslySetInnerHTML={{
									__html: `@php
    $items = $bill['items'] ?? [];
    $shipping = $bill['totals']['shipping'] ?? 0;
    $tax = $bill['totals']['tax'] ?? 0;
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
@endif`
								}}
							/>
						)}
					</Section>

					<Section style={{ marginTop: '12px' }}>
						{/* Render totals with React when bill data is available */}
						{bill ? (
							<div>
								<Text><strong>Subtotal (Before Discount):</strong> {subtotalBefore.toFixed(2)}</Text>
								<Text><strong>Discount:</strong> -{discountTotal.toFixed(2)}</Text>
								<Text><strong>Subtotal (After Discount):</strong> {subtotalAfter.toFixed(2)}</Text>
								<Text><strong>Shipping:</strong> {totals.shipping.toFixed(2)}</Text>
								<Text><strong>Tax:</strong> {totals.tax.toFixed(2)}</Text>
								<Text><strong>Grand Total:</strong> {grandTotal.toFixed(2)}</Text>
								{bill.cta_url && (
									<Text style={{ marginTop: '12px' }}>
										<a href={bill.cta_url} style={{ display: 'inline-block', background: '#111827', color: '#fff', textDecoration: 'none', padding: '10px 14px', borderRadius: '6px' }}>View Order</a>
									</Text>
								)}
							</div>
						) : (
							/* Fall back to Blade rendering when no bill data */
							<div
								dangerouslySetInnerHTML={{
									__html: `@php
    $discountTotal = max(0, $subtotalBefore - $subtotalAfter);
    $grandTotal = $subtotalAfter + ($bill['totals']['shipping'] ?? 0) + ($bill['totals']['tax'] ?? 0);
@endphp

<p><strong>Subtotal (Before Discount):</strong> {{ number_format($subtotalBefore, 2) }}</p>

<p><strong>Discount:</strong> -{{ number_format($discountTotal, 2) }}</p>

<p><strong>Subtotal (After Discount):</strong> {{ number_format($subtotalAfter, 2) }}</p>

<p><strong>Shipping:</strong> {{ number_format($bill['totals']['shipping'] ?? 0, 2) }}</p>

<p><strong>Tax:</strong> {{ number_format($bill['totals']['tax'] ?? 0, 2) }}</p>

<p><strong>Grand Total:</strong> {{ number_format($grandTotal, 2) }}</p>

@if(!empty($bill['cta_url']))
<p style="margin-top:12px">
  <a href="{{ $bill['cta_url'] }}" style="display:inline-block;background:#111827;color:#fff;text-decoration:none;padding:10px 14px;border-radius:6px">View Order</a>
</p>
@endif`
								}}
							/>
						)}
					</Section>

					<Section style={{ marginTop: '32px', paddingTop: '20px', borderTop: '1px solid #e5e7eb' }}>
						<Text style={{ color: '#6b7280', fontSize: '14px', textAlign: 'center' }}>
							<span dangerouslySetInnerHTML={{ __html: 'Thanks for choosing us!<br /><strong>{{ config("app.name") }}</strong>' }} />
						</Text>
						<Text style={{ color: '#9ca3af', fontSize: '12px', textAlign: 'center', marginTop: '8px' }}>
							If you have any questions about your order, please contact our support team.
						</Text>
						<Text style={{ color: '#9ca3af', fontSize: '11px', textAlign: 'center', marginTop: '12px' }}>
							<span dangerouslySetInnerHTML={{ __html: '© {{ date("Y") }} {{ config("app.name") }}. All rights reserved.' }} />
						</Text>
					</Section>
				</Container>
			</Body>
		</Html>
	);
}


