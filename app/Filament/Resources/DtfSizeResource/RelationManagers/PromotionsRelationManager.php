<?php

namespace App\Filament\Resources\DtfSizeResource\RelationManagers;

use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionsRelationManager extends RelationManager
{
    protected static string $relationship = 'promotion';

    protected static ?string $recordTitleAttribute = 'titulo';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promotion Details')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Discount Configuration')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('Discount Type')
                            ->options([
                                'percentage' => '📊 Percentage (%)',
                                'fixed' => '💰 Fixed Amount ($)',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('discount_value')
                            ->label('Discount Value')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->helperText('Enter the percentage or fixed amount'),
                    ]),
                Forms\Components\Section::make('Validity Period')
                    ->schema([
                        Forms\Components\DatePicker::make('inicio')
                            ->label('Start Date')
                            ->nullable(),
                        Forms\Components\DatePicker::make('fin')
                            ->label('End Date')
                            ->nullable(),
                    ]),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => '% Percentage',
                        'fixed' => '$ Fixed',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Value')
                    ->formatStateUsing(fn ($record) => 
                        $record->discount_type === 'percentage' 
                            ? $record->discount_value . '%' 
                            : '$' . number_format($record->discount_value, 2)
                    ),
                Tables\Columns\TextColumn::make('inicio')
                    ->label('Start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fin')
                    ->label('End')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
