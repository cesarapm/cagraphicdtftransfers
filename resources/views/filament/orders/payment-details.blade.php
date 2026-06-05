@php
    $payments = $order->pays->sortByDesc('created_at')->values();
    $latestPayment = $payments->first();

    $statusColor = match ($latestPayment?->estado) {
        'approved' => ['bg' => '#e7f5ec', 'fg' => '#1f7a45', 'border' => '#b6dfc2'],
        'pending', 'in_process' => ['bg' => '#fdf2df', 'fg' => '#9a6700', 'border' => '#ecd3a3'],
        'rejected', 'cancelled', 'refunded', 'charged_back' => ['bg' => '#fde9e7', 'fg' => '#b64334', 'border' => '#edbeb8'],
        default => ['bg' => '#f3efe8', 'fg' => '#77675a', 'border' => '#dfd4c7'],
    };
@endphp

<div style="display:grid; gap: 1rem; color:#4f4337;">
    @if ($latestPayment)
        <div style="position:relative; overflow:hidden; border-radius:28px; border:1px solid rgba(184,151,120,.22); background:linear-gradient(135deg, rgba(255,252,248,.98), rgba(243,235,226,.92)); box-shadow:0 24px 48px rgba(111,90,70,.08); padding:1.5rem;">
            <div style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:1rem; align-items:flex-start; margin-bottom:1.25rem;">
                <div>
                    <div style="font-size:.74rem; letter-spacing:.18em; text-transform:uppercase; color:#8c745f; margin-bottom:.45rem;">Pago más reciente</div>
                    <div style="font-size:1.55rem; font-weight:700; color:#6b5b47;">{{ $latestPayment->descripcion ?: 'Movimiento registrado en pasarela' }}</div>
                    <div style="margin-top:.35rem; color:#7d6b58;">Orden {{ $order->order_number }} · {{ $order->customer_full_name }}</div>
                </div>
                <div style="padding:.55rem 1rem; border-radius:999px; border:1px solid {{ $statusColor['border'] }}; background:{{ $statusColor['bg'] }}; color:{{ $statusColor['fg'] }}; font-weight:700; text-transform:uppercase; letter-spacing:.08em; font-size:.78rem;">
                    {{ $latestPayment->estado ?: 'Sin estado' }}
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:.9rem;">
                <div style="padding:1rem; border-radius:18px; background:rgba(255,255,255,.72); border:1px solid rgba(184,151,120,.18);">
                    <div style="font-size:.72rem; letter-spacing:.16em; text-transform:uppercase; color:#8c745f; margin-bottom:.35rem;">Monto transacción</div>
                    <div style="font-size:1.35rem; font-weight:700; color:#6b5b47;">${{ number_format((float) $latestPayment->monto_transaccion, 2) }}</div>
                </div>
                <div style="padding:1rem; border-radius:18px; background:rgba(255,255,255,.72); border:1px solid rgba(184,151,120,.18);">
                    <div style="font-size:.72rem; letter-spacing:.16em; text-transform:uppercase; color:#8c745f; margin-bottom:.35rem;">Método</div>
                    <div style="font-size:1.05rem; font-weight:600; color:#6b5b47;">{{ $latestPayment->metodo_pago ?: 'No especificado' }}</div>
                </div>
                <div style="padding:1rem; border-radius:18px; background:rgba(255,255,255,.72); border:1px solid rgba(184,151,120,.18);">
                    <div style="font-size:.72rem; letter-spacing:.16em; text-transform:uppercase; color:#8c745f; margin-bottom:.35rem;">Autorización</div>
                    <div style="font-size:1.05rem; font-weight:600; color:#6b5b47;">{{ $latestPayment->codigo_autorizacion ?: 'Sin código' }}</div>
                </div>
                <div style="padding:1rem; border-radius:18px; background:rgba(255,255,255,.72); border:1px solid rgba(184,151,120,.18);">
                    <div style="font-size:.72rem; letter-spacing:.16em; text-transform:uppercase; color:#8c745f; margin-bottom:.35rem;">Tarjeta</div>
                    <div style="font-size:1.05rem; font-weight:600; color:#6b5b47;">{{ $latestPayment->numero_tarjeta ?: 'Sin datos' }}</div>
                </div>
            </div>
        </div>

        <div style="display:grid; gap:.85rem;">
            @foreach ($payments as $payment)
                <div style="display:grid; grid-template-columns:minmax(0, 1.6fr) repeat(3, minmax(120px, 1fr)); gap:.85rem; align-items:center; padding:1rem 1.1rem; border-radius:22px; background:rgba(250,246,240,.9); border:1px solid rgba(184,151,120,.16);">
                    <div>
                        <div style="font-weight:700; color:#6b5b47;">{{ $payment->id_pago }}</div>
                        <div style="font-size:.92rem; color:#7b6857; margin-top:.2rem;">{{ $payment->descripcion ?: 'Pago registrado sin descripción.' }}</div>
                    </div>
                    <div>
                        <div style="font-size:.72rem; letter-spacing:.14em; text-transform:uppercase; color:#8c745f; margin-bottom:.2rem;">Estado</div>
                        <div style="font-weight:600; color:#6b5b47;">{{ $payment->estado ?: 'Sin estado' }}</div>
                    </div>
                    <div>
                        <div style="font-size:.72rem; letter-spacing:.14em; text-transform:uppercase; color:#8c745f; margin-bottom:.2rem;">Neto</div>
                        <div style="font-weight:600; color:#6b5b47;">${{ number_format((float) $payment->monto_recibido_neto, 2) }}</div>
                    </div>
                    <div>
                        <div style="font-size:.72rem; letter-spacing:.14em; text-transform:uppercase; color:#8c745f; margin-bottom:.2rem;">Actualizado</div>
                        <div style="font-weight:600; color:#6b5b47;">{{ $payment->fecha_ultima_actualizacion ?: $payment->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="padding:2rem; border-radius:28px; border:1px dashed rgba(184,151,120,.28); background:linear-gradient(180deg, rgba(255,252,248,.95), rgba(244,238,232,.88)); text-align:center; color:#746455;">
            <div style="font-size:.78rem; letter-spacing:.18em; text-transform:uppercase; color:#8c745f; margin-bottom:.75rem;">Sin pago vinculado</div>
            <div style="font-size:1.35rem; font-weight:700; color:#6b5b47; margin-bottom:.55rem;">Esta orden todavía no tiene registros en la tabla de pagos.</div>
            <div style="max-width:40rem; margin:0 auto; line-height:1.8;">Cuando Mercado Pago o el proceso manual registren un movimiento en pays, aquí podrás ver estado, método, montos y referencias sin salir del panel.</div>
        </div>
    @endif
</div>