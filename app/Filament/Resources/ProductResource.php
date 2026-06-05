<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Productos';

    protected static ?string $pluralModelLabel = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->label('Código')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01),

                Forms\Components\TextInput::make('stock')
                    ->label('Stock')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Forms\Components\Select::make('category')
                    ->label('Categoría')
                    ->options([
                        'Charm' => 'Charm',
                        'Collar' => 'Collar',
                        'Pulsera' => 'Pulsera',
                        'Arete' => 'Arete',
                        'Arracada' => 'Arracada',
                        'Dije' => 'Dije',
                        'Glifo' => 'Glifo',
                        'Mandala' => 'Mandala',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\Select::make('collection')
                    ->label('Colección')
                    ->options([
                        'Coleccion Cosmologia Maya' => 'Cosmología Maya',
                        'Coleccion Maya Contemporanea' => 'Maya Contemporánea'
                    ])


                    
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('material')
                    ->label('Material')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('peso')
                    ->label('Peso')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('dimensiones')
                    ->label('Dimensiones')
                    ->nullable()
                    ->maxLength(255),
                FileUpload::make('galeria_imagenes')
                    ->label('Galería de Imágenes')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                    ->maxSize(1024)
                    ->disk('public')
                    ->directory('products/galeria')
                    ->multiple()
                    ->nullable()
                    ->reorderable(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
                Forms\Components\Toggle::make('destacado')
                    ->label('Destacado')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('galeria_imagenes')
                    ->label('Imagen')
                    ->circular()
                    ->getStateUsing(function ($record) {
                        $imagenes = $record->galeria_imagenes;

                        if (is_array($imagenes) && count($imagenes) > 0) {
                            return $imagenes[0]; // primera imagen
                        }

                        return null;
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'Charm' => 'Charm',
                        'Collar' => 'Collar',
                        'Pulsera' => 'Pulsera',
                        'Arete' => 'Arete',
                        'Arracada' => 'Arracada',
                        'Dije' => 'Dije',
                        'Glifo' => 'Glifo',
                        'Mandala' => 'Mandala',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Charm' => 'gray',
                        'Collar' => 'success',
                        'Pulsera' => 'warning',
                        'Arete' => 'info',
                        'Arracada' => 'primary',
                        'Dije' => 'primary',
                        'Glifo' => 'danger',
                        'Mandala' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('collection')
                    ->label('Colección')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'Coleccion Cosmologia Maya' => 'Cosmología Maya',
                        'Coleccion Maya Contemporanea' => 'Maya Contemporánea',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Coleccion Cosmologia Maya' => 'primary',
                        'Coleccion Maya Contemporanea' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('MXN', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'Charm' => '🎩 Charm',
                        'Collar' => '⚡ Collar',
                        'Pulsera' => '🎨 Pulsera'
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
