<?php

namespace Tests\Feature;

use App\Mail\CustomerOrdersAccessCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomerOrderAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_request_and_verify_access_code_to_view_orders(): void
    {
        Mail::fake();

        $product = Product::create([
            'name' => 'Anillo Aurora',
            'codigo' => 'TEST-001',
            'description' => 'Pieza de prueba',
            'price' => 1200,
            'stock' => 4,
            'collection' => 'Coleccion Cosmologia Maya',
            'category' => 'Charm',
            'image' => 'anillo.jpg',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-123',
            'customer_first_name' => 'Cesar',
            'customer_last_name' => 'Lopez',
            'customer_email' => 'cliente@example.com',
            'customer_phone' => '5512345678',
            'shipping_address' => 'Calle 1',
            'shipping_city' => 'CDMX',
            'shipping_state' => 'CDMX',
            'shipping_zip_code' => '01000',
            'subtotal' => 1200,
            'shipping_cost' => 0,
            'tax' => 0,
            'total' => 1200,
            'metodo_pago' => 'mercado_pago',
            'status' => 'pendiente',
            'envio' => [
                'address' => 'Calle 1',
                'city' => 'CDMX',
                'state' => 'CDMX',
                'zip_code' => '01000',
            ],
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Anillo Aurora',
            'quantity' => 1,
            'unit_price' => 1200,
            'total' => 1200,
        ]);

        $requestResponse = $this->postJson('/api/clientes/pedidos/acceso', [
            'email' => 'cliente@example.com',
        ]);

        $requestResponse->assertOk();
        Mail::assertSent(CustomerOrdersAccessCode::class, 1);

        $mailable = null;
        Mail::assertSent(CustomerOrdersAccessCode::class, function (CustomerOrdersAccessCode $mail) use (&$mailable) {
            $mailable = $mail;

            return $mail->hasTo('cliente@example.com');
        });

        $this->assertNotNull($mailable);
        $this->assertNotNull(Cache::get('customer-order-access-code:' . sha1('cliente@example.com')));

        $verifyResponse = $this->postJson('/api/clientes/pedidos/verificar', [
            'email' => 'cliente@example.com',
            'code' => $mailable->code,
        ]);

        $verifyResponse
            ->assertOk()
            ->assertJsonPath('customer.email', 'cliente@example.com')
            ->assertJsonPath('orders.0.order_number', 'ORD-TEST-123')
            ->assertJsonPath('orders.0.items.0.product_name', 'Anillo Aurora');
    }
}