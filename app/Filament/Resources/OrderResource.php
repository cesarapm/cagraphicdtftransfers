<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\PaysRelationManager;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $pluralModelLabel = 'Pedidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cliente')
                    ->schema([
                        Forms\Components\TextInput::make('customer_first_name')
                            ->label('👤 Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_last_name')
                            ->label('Apellido')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('📧 Email')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('📱 Teléfono')
                            ->tel()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Dirección de Envío')
                    ->schema([
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('📍 Dirección')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('shipping_city')
                            ->label('Ciudad')
                            ->required(),

                        Forms\Components\TextInput::make('shipping_state')
                            ->label('Estado')
                            ->required(),

                        Forms\Components\TextInput::make('shipping_zip_code')
                            ->label('Código Postal')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Detalles del Pedido')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('# Orden')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('💰 Subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),

                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('📦 Envío')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),

                        Forms\Components\TextInput::make('tax')
                            ->label('Impuestos')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),

                        Forms\Components\TextInput::make('total')
                            ->label('💵 Total')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),

           

                        Forms\Components\Select::make('payment_status')
                            ->label('💳 Estado del Pago')
                            ->options([
                                'pendiente_pago' => '⏳ Pendiente de Pago',
                                'pagado' => '✅ Pagado',
                                'rechazado' => '❌ Rechazado',
                                'reembolsado' => '↩️ Reembolsado',
                            ])
                            ->default('pendiente_pago')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('order_status')
                            ->label('📦 Estado del Pedido')
                            ->options([
                                'pendiente' => '⏳ Pendiente',
                                'procesando' => '🔄 Procesando',
                                'enviado' => '📦 Enviado',
                                'entregado' => '✅ Entregado',
                                'cancelado' => '❌ Cancelado',
                            ])
                            ->default('pendiente')
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('📝 Notas del Cliente')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('🗒️ Observaciones del Admin')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('# Orden')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer_full_name')
                    ->label('Cliente')
                    ->getStateUsing(fn (Order $record) => $record->customer_full_name)
                    ->searchable(['customer_first_name', 'customer_last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'mercado_pago' => '💳 MP',
                        'paypal' => '💰 PP',
                        'transferencia' => '🏦 Transf',
                     
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'mercado_pago' => 'info',
                        'paypal' => 'warning',
                        'transferencia' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('💳 Pago')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente_pago' => '⏳ Pendiente',
                        'pagado' => '✅ Pagado',
                        'rechazado' => '❌ Rechazado',
                        'reembolsado' => '↩️ Reembolsado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente_pago' => 'warning',
                        'pagado' => 'success',
                        'rechazado' => 'danger',
                        'reembolsado' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_status')
                    ->label('📦 Pedido')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => '⏳ Pendiente',
                        'procesando' => '🔄 Procesando',
                        'enviado' => '📦 Enviado',
                        'entregado' => '✅ Entregado',
                        'cancelado' => '❌ Cancelado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'procesando' => 'info',
                        'enviado' => 'primary',
                        'entregado' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Estado del Pago')
                    ->options([
                        'pendiente_pago' => '⏳ Pendiente de Pago',
                        'pagado' => '✅ Pagado',
                        'rechazado' => '❌ Rechazado',
                        'reembolsado' => '↩️ Reembolsado',
                    ]),

                Tables\Filters\SelectFilter::make('order_status')
                    ->label('Estado del Pedido')
                    ->options([
                        'pendiente' => '⏳ Pendiente',
                        'procesando' => '🔄 Procesando',
                        'enviado' => '📦 Enviado',
                        'entregado' => '✅ Entregado',
                        'cancelado' => '❌ Cancelado',
                    ]),

                Tables\Filters\SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'mercado_pago' => '💳 Mercado Pago',
                        'paypal' => '💰 PayPal',
                        'transferencia' => '🏦 Transferencia',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('marcarPagado')
                    ->label('Marcar Pagado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record): bool => 
                        $record->metodo_pago === 'transferencia' && 
                        $record->payment_status === 'pendiente_pago'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('¿Marcar como pagado?')
                    ->modalDescription('Confirma que recibiste la transferencia bancaria y quieres marcar este pedido como pagado.')
                    ->modalSubmitActionLabel('Sí, marcar como pagado')
                    ->form([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones (opcional)')
                            ->placeholder('Ej: Transferencia recibida el 30/05/2026, referencia 123456')
                            ->rows(3),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update([
                            'payment_status' => 'pagado',
                            'order_status' => 'procesando',
                            'observaciones' => $data['observaciones'] ?? $record->observaciones,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Pedido marcado como pagado')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('viewPayment')
                    ->label('Ver pago')
                    ->icon('heroicon-o-credit-card')
                    ->color(fn (Order $record): string => $record->pays()->exists() ? 'success' : 'gray')
                    ->modalHeading(fn (Order $record): string => 'Pago de la orden ' . $record->order_number)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalWidth('4xl')
                    ->modalContent(fn (Order $record) => view('filament.orders.payment-details', [
                        'order' => $record->load('pays'),
                    ])),

                Tables\Actions\Action::make('downloadGangSheet')
                    ->label('Descargar Gang Sheet')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (Order $record): bool => 
                        $record->items()
                            ->whereNotNull('gang_sheet_id')
                            ->exists()
                    )
                    ->url(fn (Order $record): string => 
                        '/api/gang-sheets/' . 
                        $record->items()
                            ->whereNotNull('gang_sheet_id')
                            ->first()
                            ?->gang_sheet_id . 
                        '/download'
                    )
                    ->openUrlInNewTab()
                    ->tooltip('Descargar la imagen PNG compilada del gang sheet'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            PaysRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
