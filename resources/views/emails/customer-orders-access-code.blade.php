<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codigo de acceso a tus pedidos</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #f5efe8;
            color: #5f5244;
            font-family: Georgia, 'Times New Roman', serif;
        }
        .container {
            max-width: 680px;
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
        .content {
            padding: 28px;
            line-height: 1.8;
        }
        .code-box {
            margin: 24px 0;
            padding: 18px;
            border-radius: 18px;
            text-align: center;
            background: #f6ead9;
            border: 1px solid #ead3b6;
        }
        .code {
            font-size: 34px;
            letter-spacing: .22em;
            font-weight: 700;
            color: #6b4e2f;
        }
        .note {
            color: #7a6754;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Tu acceso a pedidos</h1>
            <p>Usa este codigo temporal para ver tus compras recientes en IzaguirreQu.</p>
        </div>

        <div class="content">
            <p>Recibimos una solicitud para consultar el historial asociado a este correo.</p>

            <div class="code-box">
                <div class="code">{{ $code }}</div>
                <p class="note">Vence en 15 minutos.</p>
            </div>

            <p>Tu compra mas reciente registrada es la orden {{ $order->order_number }}.</p>
            <p class="note">Si no fuiste tu, puedes ignorar este mensaje.</p>
        </div>
    </div>
</body>
</html>