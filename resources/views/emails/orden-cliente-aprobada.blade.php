<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your order was confirmed</title>
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
            line-height: 1.6;
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
        .card {
            background: #f8f2eb;
            border: 1px solid #eadccc;
            border-radius: 18px;
            padding: 18px;
            margin-top: 18px;
        }
        .card h2,
        .card h3 {
            margin: 0 0 12px;
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
        .tracking-box {
            margin-top: 22px;
            padding: 20px;
            border-radius: 20px;
            background: #f6ead9;
            border: 1px solid #ead3b6;
        }
        .tracking-box p {
            margin: 0 0 14px;
            line-height: 1.7;
        }
        .tracking-code {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 12px;
            background: #fffaf5;
            border: 1px solid #e0cfbb;
            color: #6b4e2f;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: .06em;
        }
        .button {
            display: inline-block;
            margin-top: 16px;
            padding: 14px 22px;
            border-radius: 999px;
            background: #8c745f;
            color: #fffdf9 !important;
            text-decoration: none !important;
            font-weight: 700;
            letter-spacing: .04em;
            border: none;
        }
        .button-secondary {
            word-break: break-all;
            color: #7b5f3f;
            font-size: 13px;
            margin-top: 14px;
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
            line-height: 1.6;
        }
        @media (max-width: 640px) {
            body {
                padding: 12px;
            }
            .row {
                flex-direction: column;
            }
            .value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Your payment was confirmed</h1>
            <p>Order {{ $orden->order_number }} has been approved. From here you can review your status whenever you want.</p>
        </div>

        <div class="content">
            <span class="badge">Payment Approved</span>

            <div class="tracking-box">
                <h2>Your Order Tracking</h2>
                <p>Your identifier to check the status is this order number:</p>
                <div class="tracking-code">{{ $orden->order_number }}</div>
                <p>We also left direct access for you to open the tracking without entering it again.</p>
                <a class="button" href="{{ $trackingUrl }}" target="_blank" rel="noopener noreferrer">View my order status</a>
                <div class="button-secondary">If the button doesn't open, use this link: {{ $trackingUrl }}</div>
            </div>

            <div class="card">
                <h3>Summary</h3>
                <div class="row"><span class="label">Order</span><span class="value">&nbsp;{{ $orden->order_number }}</span></div>
                <div class="row"><span class="label">Customer</span><span class="value">&nbsp;{{ $orden->customer_full_name }}</span></div>
                <div class="row"><span class="label">Email</span><span class="value">&nbsp;{{ $orden->customer_email }}</span></div>
                <div class="row"><span class="label">Order Status</span><span class="value">&nbsp;{{ $orden->status_label }}</span></div>
                <div class="row"><span class="label">Payment Method</span><span class="value">&nbsp;{{ 
                    match(strtolower((string) ($pago->metodo_pago ?: 'mercado_pago'))) {
                        'account_money', 'mercado_pago' => 'Mercado Pago',
                        'paypal' => 'PayPal',
                        'transferencia' => 'Bank Transfer',
                        default => ucwords(str_replace('_', ' ', (string) $pago->metodo_pago))
                    }
                }}</span></div>
                <div class="row"><span class="label">Payment Status</span><span class="value">&nbsp;{{ strtoupper((string) $pago->estado) }}</span></div>
            </div>

            <div class="card">
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
                    <strong style="display: block; color: #7b5f3f; font-size: 13px; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px;">Total Paid</strong>
                    <span style="font-size: 28px; color: #6b4e2f; font-weight: 700;">${{ number_format((float) $pago->monto_transaccion, 2) }} USD</span>
                </div>
            </div>
        </div>

        <div class="footer">
            This email was sent automatically when the payment was approved. If you need help, reply to this message or contact us with your order number {{ $orden->order_number }}.
        </div>
    </div>
</body>
</html>