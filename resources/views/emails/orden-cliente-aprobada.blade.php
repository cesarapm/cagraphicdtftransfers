<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu pedido fue confirmado</title>
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
            background: linear-gradient(135deg, #8c745f, #a88d74);
            color: #fffdf9 !important;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: .04em;
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
            <h1>Tu pago fue confirmado</h1>
            <p>La orden {{ $orden->order_number }} ya fue aprobada. Desde aquí puedes revisar tu estatus cuando quieras.</p>
        </div>

        <div class="content">
            <span class="badge">Pago aprobado</span>

            <div class="tracking-box">
                <h2>Seguimiento de tu pedido</h2>
                <p>Tu identificador para consultar el estatus es este número de orden:</p>
                <div class="tracking-code">{{ $orden->order_number }}</div>
                <p>También dejamos un acceso directo para que abras el seguimiento sin volver a capturarlo.</p>
                <a class="button" href="{{ $trackingUrl }}" target="_blank" rel="noopener noreferrer">Ver estatus de mi pedido</a>
                <div class="button-secondary">Si el botón no abre, usa este enlace: {{ $trackingUrl }}</div>
            </div>

            <div class="card">
                <h3>Resumen</h3>
                <div class="row"><span class="label">Orden</span><span class="value">&nbsp;{{ $orden->order_number }}</span></div>
                <div class="row"><span class="label">Cliente</span><span class="value">&nbsp;{{ $orden->customer_full_name }}</span></div>
                <div class="row"><span class="label">Correo</span><span class="value">&nbsp;{{ $orden->customer_email }}</span></div>
                <div class="row"><span class="label">Estado de la orden</span><span class="value">&nbsp;{{ $orden->status_label }}</span></div>
                <div class="row"><span class="label">Método de pago</span><span class="value">&nbsp;{{ 
                    match(strtolower((string) ($pago->metodo_pago ?: 'mercado_pago'))) {
                        'account_money', 'mercado_pago' => 'Mercado Pago',
                        'paypal' => 'PayPal',
                        'transferencia' => 'Transferencia Bancaria',
                        default => ucwords(str_replace('_', ' ', (string) $pago->metodo_pago))
                    }
                }}</span></div>
                <div class="row"><span class="label">Estado del pago</span><span class="value">&nbsp;{{ strtoupper((string) $pago->estado) }}</span></div>
            </div>

            <div class="card">
                <h3>Productos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Pieza</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
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
                <strong>Total pagado</strong>
                <span>${{ number_format((float) $pago->monto_transaccion, 2) }} MXN</span>
            </div>
        </div>

        <div class="footer">
            Este correo se envió automáticamente cuando el pago fue aprobado. Si necesitas ayuda, responde a este mensaje o contáctanos con tu número de orden {{ $orden->order_number }}.
        </div>
    </div>
</body>
</html>