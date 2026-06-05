<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MercadoPagoSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Métodos de Pago';

    protected static ?string $title = 'Configuración de Métodos de Pago';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.pages.mercado-pago-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // Mercado Pago
            'mercadopago_enabled' => Setting::get('mercadopago_enabled', true),
            'access_token' => Setting::get('mercadopago_access_token', ''),
            'notification_url' => Setting::get('mercadopago_notification_url', ''),
            'webhook_secret' => Setting::get('mercadopago_webhook_secret', ''),
            
            // PayPal
            'paypal_enabled' => Setting::get('paypal_enabled', false),
            'paypal_client_id' => Setting::get('paypal_client_id', ''),
            'paypal_client_secret' => Setting::get('paypal_client_secret', ''),
            'paypal_mode' => Setting::get('paypal_mode', 'sandbox'),
            
            // Transferencia
            'transferencia_enabled' => Setting::get('transferencia_enabled', false),
            'banco_nombre' => Setting::get('banco_nombre', ''),
            'banco_beneficiario' => Setting::get('banco_beneficiario', ''),
            'banco_cuenta' => Setting::get('banco_cuenta', ''),
            'banco_clabe' => Setting::get('banco_clabe', ''),
            'banco_swift' => Setting::get('banco_swift', ''),
            'whatsapp_transferencia' => Setting::get('whatsapp_transferencia', ''),
            'transferencia_instrucciones' => Setting::get('transferencia_instrucciones', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Mercado Pago')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\Toggle::make('mercadopago_enabled')
                                    ->label('Habilitar Mercado Pago')
                                    ->helperText('Activar pagos con Mercado Pago en el checkout')
                                    ->default(true)
                                    ->live()
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Credenciales de Mercado Pago')
                                    ->description('Configura las credenciales de Mercado Pago para procesar pagos en línea.')
                                    ->visible(fn ($get) => $get('mercadopago_enabled'))
                                    ->schema([
                                        Forms\Components\TextInput::make('access_token')
                                            ->label('Access Token')
                                            ->helperText('Token de acceso de Mercado Pago (APP_USR-...)')
                                            ->placeholder('APP_USR-XXXX-XXXXXX-XXXX')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('notification_url')
                                            ->label('URL de Notificación (Webhook)')
                                            ->helperText('URL donde Mercado Pago enviará las notificaciones de pago')
                                            ->placeholder('https://tudominio.com/api/mercado-pago/webhook')
                                            ->url()
                                            ->maxLength(500)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('webhook_secret')
                                            ->label('Webhook Secret')
                                            ->helperText('Secreto para validar las firmas de los webhooks')
                                            ->placeholder('7c9b512de072ed10...')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Información')
                                    ->visible(fn ($get) => $get('mercadopago_enabled'))
                                    ->schema([
                                        Forms\Components\Placeholder::make('info_mp')
                                            ->label('')
                                            ->content('Para obtener tus credenciales, ingresa a tu cuenta de Mercado Pago en la sección de Desarrolladores.'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('PayPal')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Toggle::make('paypal_enabled')
                                    ->label('Habilitar PayPal')
                                    ->helperText('Activar pagos con PayPal en el checkout')
                                    ->default(false)
                                    ->live()
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Credenciales de PayPal')
                                    ->description('Configura las credenciales de PayPal para procesar pagos en línea.')
                                    ->visible(fn ($get) => $get('paypal_enabled'))
                                    ->schema([
                                        Forms\Components\TextInput::make('paypal_client_id')
                                            ->label('Client ID')
                                            ->helperText('Client ID de tu aplicación PayPal')
                                            ->placeholder('AeB1234...')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('paypal_client_secret')
                                            ->label('Client Secret')
                                            ->helperText('Client Secret de tu aplicación PayPal')
                                            ->placeholder('EFgh5678...')
                                            ->password()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\Select::make('paypal_mode')
                                            ->label('Modo de Operación')
                                            ->helperText('Sandbox para pruebas, Live para producción')
                                            ->options([
                                                'sandbox' => 'Sandbox (Pruebas)',
                                                'live' => 'Live (Producción)',
                                            ])
                                            ->default('sandbox')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Información')
                                    ->visible(fn ($get) => $get('paypal_enabled'))
                                    ->schema([
                                        Forms\Components\Placeholder::make('info_paypal')
                                            ->label('')
                                            ->content('Para obtener tus credenciales, crea una aplicación en PayPal Developer (https://developer.paypal.com/dashboard/applications).'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Transferencia Bancaria')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Toggle::make('transferencia_enabled')
                                    ->label('Habilitar Transferencia Bancaria')
                                    ->helperText('Activar pagos por transferencia bancaria en el checkout')
                                    ->default(false)
                                    ->live()
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Datos Bancarios')
                                    ->description('Información que se mostrará al cliente para realizar la transferencia.')
                                    ->visible(fn ($get) => $get('transferencia_enabled'))
                                    ->schema([
                                        Forms\Components\TextInput::make('banco_nombre')
                                            ->label('Nombre del Banco')
                                            ->placeholder('Banco Nacional de México')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('banco_beneficiario')
                                            ->label('Beneficiario')
                                            ->placeholder('Nombre de tu negocio o persona')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('banco_cuenta')
                                            ->label('Número de Cuenta')
                                            ->placeholder('1234567890')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('banco_clabe')
                                            ->label('CLABE Interbancaria')
                                            ->placeholder('012345678901234567')
                                            ->maxLength(18)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('banco_swift')
                                            ->label('Código SWIFT/BIC (Opcional)')
                                            ->placeholder('BNMXMXMM')
                                            ->maxLength(50)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('whatsapp_transferencia')
                                            ->label('WhatsApp para Confirmación')
                                            ->helperText('Número de WhatsApp donde los clientes enviarán el comprobante (sin + ni espacios)')
                                            ->placeholder('5551234567')
                                            ->tel()
                                            ->maxLength(20)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('transferencia_instrucciones')
                                            ->label('Instrucciones Adicionales')
                                            ->helperText('Instrucciones que se mostrarán al cliente después de crear la orden')
                                            ->placeholder('Por favor envía tu comprobante de pago por WhatsApp con tu número de orden...')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Guardar configuración de Mercado Pago
        Setting::set('mercadopago_enabled', $data['mercadopago_enabled'] ?? false, 'Habilitar Mercado Pago');
        Setting::set('mercadopago_access_token', $data['access_token'] ?? '', 'Access Token de Mercado Pago');
        Setting::set('mercadopago_notification_url', $data['notification_url'] ?? '', 'URL de notificación de Mercado Pago');
        Setting::set('mercadopago_webhook_secret', $data['webhook_secret'] ?? '', 'Secreto del webhook de Mercado Pago');

        // Guardar configuración de PayPal
        Setting::set('paypal_enabled', $data['paypal_enabled'] ?? false, 'Habilitar PayPal');
        Setting::set('paypal_client_id', $data['paypal_client_id'] ?? '', 'Client ID de PayPal');
        Setting::set('paypal_client_secret', $data['paypal_client_secret'] ?? '', 'Client Secret de PayPal');
        Setting::set('paypal_mode', $data['paypal_mode'] ?? 'sandbox', 'Modo de PayPal (sandbox/live)');

        // Guardar configuración de Transferencia
        Setting::set('transferencia_enabled', $data['transferencia_enabled'] ?? false, 'Habilitar Transferencia Bancaria');
        Setting::set('banco_nombre', $data['banco_nombre'] ?? '', 'Nombre del banco');
        Setting::set('banco_beneficiario', $data['banco_beneficiario'] ?? '', 'Beneficiario');
        Setting::set('banco_cuenta', $data['banco_cuenta'] ?? '', 'Número de cuenta');
        Setting::set('banco_clabe', $data['banco_clabe'] ?? '', 'CLABE interbancaria');
        Setting::set('banco_swift', $data['banco_swift'] ?? '', 'Código SWIFT/BIC');
        Setting::set('whatsapp_transferencia', $data['whatsapp_transferencia'] ?? '', 'WhatsApp para transferencias');
        Setting::set('transferencia_instrucciones', $data['transferencia_instrucciones'] ?? '', 'Instrucciones de transferencia');

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->body('La configuración de métodos de pago se ha actualizado correctamente.')
            ->send();
    }
}
