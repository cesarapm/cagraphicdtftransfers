<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DtfSizeResource\Pages;
use App\Filament\Resources\DtfSizeResource\RelationManagers;
use App\Filament\Resources\DtfSizeResource\RelationManagers\PromotionsRelationManager;
use App\Models\DtfSize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DtfSizeResource extends Resource
{
    protected static ?string $model = DtfSize::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Size Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('width')
                                    ->label('Width')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01),
                                Forms\Components\TextInput::make('height')
                                    ->label('Height')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01),
                            Forms\Components\Select::make('unit')
                            ->label('Unit of Measurement')
                            ->options([
                                // 'feet' => '👣 Feet',
                                'inches' => '📏 Inches',
                            ])
                            ->required()
                            ->default('inches')
                            ->native(false)
                            ->helperText('Choose the unit: Feet or Inches'),
                            ]),

                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                    ]),
                Forms\Components\Section::make('Price & Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
                Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Size Image')
                            ->image()
                            ->directory('dtf-sizes')
                            ->visibility('public')
                            ->deletable()
                            ->previewable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('width')
                    ->label('Width')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('height')
                    ->label('Height')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            PromotionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDtfSizes::route('/'),
            'create' => Pages\CreateDtfSize::route('/create'),
            'edit' => Pages\EditDtfSize::route('/{record}/edit'),
        ];
    }
}
