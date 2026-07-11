<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_name')
                    ->label('🛒 Product Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->label('🔢 Quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('total')
                    ->label('💰 Total')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('🖼️ Image')
                    ->square()
                    ->width(100)
                    ->height(100),
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Unit Price')
                    ->money('USD', true),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD', true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadImage')
                    ->label('📥 Download Item Image')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('info')
                    ->visible(fn ($record) => $record->image && Storage::disk('public')->exists($record->image))
                    ->action(function ($record) {
                        return Storage::disk('public')->download($record->image);
                    }),
                Tables\Actions\Action::make('downloadGangSheet')
                    ->label('📋 Download Gang Sheet')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('success')
                    ->visible(fn ($record) => $record->gang_sheet_id)
                    ->url(fn ($record) => '/api/gang-sheets/' . $record->gang_sheet_id . '/download')
                    ->openUrlInNewTab()
                    ->tooltip('Descargar el PNG compilado del gang sheet'),
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
