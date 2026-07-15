<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountCodeResource\Pages;
use App\Models\DiscountCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DiscountCodeResource extends Resource
{
    protected static ?string $model = DiscountCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Códigos de Descuento';
    protected static ?string $modelLabel = 'Código de Descuento';
    protected static ?string $pluralModelLabel = 'Códigos de Descuento';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Código')
                    ->description('Datos básicos del código de descuento')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->unique('discount_codes', 'code', ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: SUMMER2024')
                            ->helperText('Se convertirá a mayúsculas automáticamente'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->nullable()
                            ->maxLength(255)
                            ->placeholder('Descripción del código (opcional)'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->inline(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configuración de Descuento')
                    ->description('Tipo y valor del descuento')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('Tipo de Descuento')
                            ->options([
                                'percentage' => 'Porcentaje (%)',
                                // 'fixed' => 'Cantidad Fija ($)',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('discount_value')
                            ->label(function (Forms\Get $get) {
                                return $get('discount_type') === 'percentage' ? 'Porcentaje' : 'Cantidad ($)';
                            })
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->helperText(function (Forms\Get $get) {
                                if ($get('discount_type') === 'percentage') {
                                    return 'Ej: 15 para 15% de descuento';
                                }
                                return 'Ej: 5.99 para $5.99 de descuento';
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Límites de Uso')
                    ->description('Controlar cuándo y quién puede usar este código')
                    ->schema([
                        Forms\Components\TextInput::make('max_uses')
                            ->label('Máximo de Usos Global')
                            ->nullable()
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Déjalo vacío para ilimitado. Ej: 100'),

                        Forms\Components\TextInput::make('per_user_limit')
                            ->label('Usos por Cliente')
                            ->default(1)
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Cuántas veces cada cliente puede usar este código'),

                        Forms\Components\DateTimePicker::make('valid_from')
                            ->label('Válido Desde')
                            ->nullable()
                            ->withoutSeconds(),

                        Forms\Components\DateTimePicker::make('valid_until')
                            ->label('Válido Hasta')
                            ->nullable()
                            ->withoutSeconds(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información de Uso')
                    ->description('Estadísticas del código')
                    ->schema([
                        Forms\Components\TextInput::make('used_count')
                            ->label('Total de Usos')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\BadgeColumn::make('discount_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state) => $state === 'percentage' ? 'Porcentaje' : 'Fijo')
                    ->colors([
                        'blue' => 'percentage',
                        'green' => 'fixed',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Valor')
                    ->formatStateUsing(function (DiscountCode $record) {
                        if ($record->discount_type === 'percentage') {
                            return "{$record->discount_value}%";
                        }
                        return "\${$record->discount_value}";
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('used_count')
                    ->label('Usos')
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_uses')
                    ->label('Máximo')
                    ->formatStateUsing(fn ($state) => $state ?? '∞ (Ilimitado)')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('valid_from')
                    ->label('Desde')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Hasta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado'),

                Tables\Filters\SelectFilter::make('discount_type')
                    ->label('Tipo de Descuento')
                    ->options([
                        'percentage' => 'Porcentaje (%)',
                        'fixed' => 'Cantidad Fija ($)',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscountCodes::route('/'),
            'create' => Pages\CreateDiscountCode::route('/create'),
            'edit' => Pages\EditDiscountCode::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestión de Ventas';
    }
}
