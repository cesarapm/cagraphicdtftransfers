<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario administrador por defecto
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('123123'),
            ]
        );

        // Configurar métodos de pago por defecto
        $this->configurePaymentMethods();

        // Opcional: Descomentar para crear datos de ejemplo
        // $this->createSampleProducts();
        // $this->createSampleCustomers();
    }

    /**
     * Configurar métodos de pago por defecto
     */
    private function configurePaymentMethods(): void
    {
        // Habilitar MercadoPago por defecto
        Setting::updateOrCreate(
            ['key' => 'mercadopago_enabled'],
            ['value' => '1', 'description' => 'Habilitar Mercado Pago']
        );

        // PayPal deshabilitado por defecto (requiere configuración)
        Setting::updateOrCreate(
            ['key' => 'paypal_enabled'],
            ['value' => '0', 'description' => 'Habilitar PayPal']
        );

        Setting::updateOrCreate(
            ['key' => 'paypal_client_id'],
            ['value' => '', 'description' => 'Client ID de PayPal']
        );

        Setting::updateOrCreate(
            ['key' => 'paypal_client_secret'],
            ['value' => '', 'description' => 'Client Secret de PayPal']
        );

        Setting::updateOrCreate(
            ['key' => 'paypal_mode'],
            ['value' => 'sandbox', 'description' => 'Modo de PayPal (sandbox/live)']
        );

        // Habilitar Transferencia por defecto
        Setting::updateOrCreate(
            ['key' => 'transferencia_enabled'],
            ['value' => '1', 'description' => 'Habilitar Transferencia Bancaria']
        );

        // Datos bancarios de ejemplo
        Setting::updateOrCreate(
            ['key' => 'banco_nombre'],
            ['value' => 'Banco Ejemplo', 'description' => 'Nombre del banco']
        );

        Setting::updateOrCreate(
            ['key' => 'banco_beneficiario'],
            ['value' => 'Mi Negocio S.A. de C.V.', 'description' => 'Beneficiario']
        );

        Setting::updateOrCreate(
            ['key' => 'banco_cuenta'],
            ['value' => '1234567890', 'description' => 'Número de cuenta']
        );

        Setting::updateOrCreate(
            ['key' => 'banco_clabe'],
            ['value' => '012345678901234567', 'description' => 'CLABE interbancaria']
        );

        Setting::updateOrCreate(
            ['key' => 'whatsapp_transferencia'],
            ['value' => '5551234567', 'description' => 'WhatsApp para transferencias']
        );

        Setting::updateOrCreate(
            ['key' => 'transferencia_instrucciones'],
            ['value' => 'Por favor envía tu comprobante de pago por WhatsApp con tu número de orden.', 'description' => 'Instrucciones de transferencia']
        );
    }

    /**
     * Crear productos de ejemplo (puedes comentar si no necesitas)
     */
    private function createSampleProducts(): void
    {
        $products = [
            [
                'name' => 'Producto Ejemplo 1',
                'codigo' => 'PROD-001',
                'description' => 'Descripción del producto de ejemplo',
                'price' => 99.99,
                'stock' => 50,
                'category' => 'Categoría 1',
                'image' => null,
                'galeria_imagenes' => null,
                'collection' => null,
                'material' => null,
                'peso' => null,
                'dimensiones' => null,
                'is_active' => true,
                'destacado' => true,
            ],
            [
                'name' => 'Producto Ejemplo 2',
                'codigo' => 'PROD-002',
                'description' => 'Otro producto de ejemplo',
                'price' => 149.99,
                'stock' => 30,
                'category' => 'Categoría 2',
                'image' => null,
                'galeria_imagenes' => null,
                'collection' => null,
                'material' => null,
                'peso' => null,
                'dimensiones' => null,
                'is_active' => true,
                'destacado' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['codigo' => $product['codigo']],
                $product
            );
        }
    }

    /**
     * Crear clientes de ejemplo (puedes comentar si no necesitas)
     */
    private function createSampleCustomers(): void
    {
        $customers = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan@example.com',
                'phone' => '5551234567',
                'address' => 'Calle Principal 123',
                'city' => 'Ciudad',
                'state' => 'Estado',
                'zip_code' => '12345',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['email' => $customer['email']],
                $customer
            );
        }
    }
}