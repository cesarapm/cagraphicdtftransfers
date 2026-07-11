<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Approved</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #f5efe8;
            color: #5f5244;
            font-family: Georgia, 'Times New Roman', serif;
        }
        .container {
            max-width: 720px;
            margin: 0 auto;
            background: #fffaf5;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 48px rgba(111, 90, 70, 0.12);
            border: 1px solid #e7d8c6;
        }
        .hero {
            padding: 32px;
            background: linear-gradient(135deg, #8c745f, #c5ab8f);
            color: #fffaf4;
        }
        .hero h1 {
            margin: 0 0 10px;
            font-size: 30px;
        }
        .hero p {
            margin: 0;
            opacity: .92;
        }
        .content {
            padding: 28px;
        }
        .badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #e5f3ea;
            color: #1f7a45;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-size: 12px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin: 22px 0;
        }
        .card {
            background: #f8f2eb;
            border: 1px solid #eadccc;
            border-radius: 18px;
            padding: 16px;
        }
        .card h3 {
            margin: 0 0 12px;
            font-size: 16px;
            color: #6b5b47;
        }
        .row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #eadccc;
            font-size: 14px;
        }
        .row:last-child {
            border-bottom: 0;
        }
        .label {
            color: #8a7865;
            font-weight: 700;
        }
        .value {
            color: #4f4337;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            overflow: hidden;
            border-radius: 18px;
        }
        thead {
            background: #7d6550;
            color: #fffaf4;
        }
        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #eee3d7;
            font-size: 14px;
        }
        tbody tr:last-child td {
            border-bottom: 0;
        }
        .total-box {
            margin-top: 20px;
            padding: 18px;
            background: #f6ead9;
            border-radius: 18px;
            text-align: right;
        }
        .total-box strong {
            display: block;
            color: #7b5f3f;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 6px;
        }
        .total-box span {
            font-size: 28px;
            color: #6b4e2f;
            font-weight: 700;
        }
        .footer {
            padding: 0 28px 28px;
            color: #84715d;
            font-size: 13px;
        }
        @media (max-width: 640px) {
            body {
                padding: 12px;
            }
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Payment Approved for Processing</h1>
            <p>Order {{ $orden->order_number }} has been authorized and can proceed to preparation.</p>
        </div>

        <div class="content">
            <span class="badge">Payment Approved</span>

            <div class="grid">
                <div class="card">
                    <h3>Order</h3>
                    <div class="row"><span class="label">Number</span><span class="value">&nbsp;{{ $orden->order_number }}</span></div>
                    <div class="row"><span class="label">Date</span><span class="value">&nbsp;{{ optional($orden->created_at)->format('m/d/Y H:i') }}</span></div>
                    <div class="row"><span class="label">Customer</span><span class="value">&nbsp;{{ $orden->customer_full_name }}</span></div>
                    <div class="row"><span class="label">Email</span><span class="value">&nbsp;{{ $orden->customer_email }}</span></div>
                    <div class="row"><span class="label">Phone</span><span class="value">&nbsp;{{ $orden->customer_phone }}</span></div>
                </div>

                <div class="card">
                    <h3>Payment</h3>
                    <div class="row"><span class="label">Payment ID</span><span class="value">&nbsp;{{ $pago->id_pago }}</span></div>
                    <div class="row"><span class="label">Status</span><span class="value">&nbsp;{{ strtoupper((string) $pago->estado) }}</span></div>
                    <div class="row"><span class="label">Method</span><span class="value">&nbsp;{{ 
                        match(strtolower((string) ($pago->metodo_pago ?: 'mercado_pago'))) {
                            'account_money', 'mercado_pago' => 'Mercado Pago',
                            'paypal' => 'PayPal',
                            'transferencia' => 'Bank Transfer',
                            default => ucwords(str_replace('_', ' ', (string) $pago->metodo_pago))
                        }
                    }}</span></div>
                    <div class="row"><span class="label">Authorization</span><span class="value">&nbsp;{{ $pago->codigo_autorizacion ?: 'No code' }}</span></div>
                    <div class="row"><span class="label">Approved</span><span class="value">&nbsp;{{ $pago->fecha_aprobacion ? \Carbon\Carbon::parse($pago->fecha_aprobacion)->format('m/d/Y H:i') : 'No date' }}</span></div>
                </div>
            </div>

            <div class="card">
                <h3>Shipping</h3>
                <div class="row"><span class="label">Address</span><span class="value">&nbsp;{{ $orden->shipping_address }}</span></div>
                <div class="row"><span class="label">City / State</span><span class="value">&nbsp;{{ $orden->shipping_city }}, {{ $orden->shipping_state }}</span></div>
                <div class="row"><span class="label">Postal Code</span><span class="value">&nbsp;{{ $orden->shipping_zip_code }}</span></div>
                @if($orden->notes)
                    <div class="row"><span class="label">Notes</span><span class="value">&nbsp;{{ $orden->notes }}</span></div>
                @endif
            </div>

            <div class="card" style="margin-top: 18px;">
                <h3>Products</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                            <tr>
                                <td>{{ $producto->product_name }}</td>
                                <td>{{ $producto->quantity }}</td>
                                <td>${{ number_format((float) $producto->unit_price, 2) }}</td>
                                <td>${{ number_format((float) $producto->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="total-box">
                <div class="row" style="border-bottom: 1px solid rgba(0,0,0,.1); padding: 8px 0;">
                    <strong style="color: #7b5f3f; font-size: 13px;">Subtotal</strong>
                    <span style="color: #6b4e2f; font-weight: 700;">${{ number_format((float) ($pago->monto_transaccion - ($orden->shipping_cost ?? 0) + ($orden->discount_amount ?? 0)), 2) }} USD</span>
                </div>
                @if($orden->discount_amount && $orden->discount_amount > 0)
                    <div class="row" style="border-bottom: 1px solid rgba(0,0,0,.1); padding: 8px 0;">
                        <strong style="color: #7b5f3f; font-size: 13px;">
                            Discount @if($orden->discount_code)({{ $orden->discount_code }})@endif
                        </strong>
                        <span style="color: #22863a; font-weight: 700;">-${{ number_format((float) $orden->discount_amount, 2) }} USD</span>
                    </div>
                @endif
                <div class="row" style="border-bottom: 1px solid rgba(0,0,0,.1); padding: 8px 0;">
                    <strong style="color: #7b5f3f; font-size: 13px;">Shipping</strong>
                    <span style="color: #6b4e2f; font-weight: 700;">${{ number_format((float) ($orden->shipping_cost ?? 0), 2) }} USD</span>
                </div>
                <div style="text-align: right; padding-top: 12px;">
                    <strong style="display: block; color: #7b5f3f; font-size: 13px; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px;">Total Authorized</strong>
                    <span style="font-size: 28px; color: #6b4e2f; font-weight: 700;">${{ number_format((float) $pago->monto_transaccion, 2) }} USD</span>
                </div>
            </div>
        </div>

        <div class="footer">
            This email was sent automatically when Mercado Pago reported approval of the payment.
        </div>
    </div>
</body>
</html>
