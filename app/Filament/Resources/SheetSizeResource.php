<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SheetSizeResource\Pages;
use App\Models\SheetSize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SheetSizeResource extends Resource
{
    protected static ?string $model = SheetSize::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Sheet Sizes';

    protected static ?string $pluralModelLabel = 'Sheet Sizes';

    protected static ?string $modelLabel = 'Sheet Size';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Size Name')
                            ->placeholder('e.g., 22" × 120" (Inches) or 22\' × 10\' (Feet)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('A descriptive name for this sheet size'),
                        
                        Forms\Components\Select::make('unit')
                            ->label('Unit of Measurement')
                            ->options([
                                'feet' => '👣 Feet',
                                'inches' => '📏 Inches',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText('Choose the unit: Feet or Inches'),
                    ]),
                
                Forms\Components\Section::make('Dimensions')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('width')
                                    ->label('Width')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->helperText('Width of the sheet'),
                                
                                Forms\Components\TextInput::make('height')
                                    ->label('Height')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->helperText('Height of the sheet'),
                            ]),
                    ]),

                Forms\Components\Section::make('Pricing & Status')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->helperText('Price in USD for this sheet size'),
                                
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Display Order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),
                            ]),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active sizes appear in the builder'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Size Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('dimensions')
                    ->label('Dimensions')
                    ->getStateUsing(fn (SheetSize $record) => 
                        $record->width . ' × ' . $record->height . ' ' . strtoupper(substr($record->unit, 0, 1))
                    )
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'feet' => 'blue',
                        'inches' => 'purple',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('usd')
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit')
                    ->label('Unit Type')
                    ->options([
                        'feet' => '👣 Feet',
                        'inches' => '📏 Inches',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSheetSizes::route('/'),
            'create' => Pages\CreateSheetSize::route('/create'),
            'edit' => Pages\EditSheetSize::route('/{record}/edit'),
        ];
    }
}
