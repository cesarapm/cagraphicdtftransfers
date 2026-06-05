<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaysRelationManager extends RelationManager
{
    protected static string $relationship = 'pays';

    protected static ?string $title = 'Pagos';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id_pago')
            ->columns([
                Tables\Columns\TextColumn::make('id_pago')
                    ->label('ID pago')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('payment_id')
                    ->label('Referencia')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending', 'in_process' => 'warning',
                        'rejected', 'cancelled', 'refunded', 'charged_back' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método'),
                Tables\Columns\TextColumn::make('monto_transaccion')
                    ->label('Monto')
                    ->money('MXN', true),
                Tables\Columns\TextColumn::make('fecha_aprobacion')
                    ->label('Aprobación')
                    ->placeholder('Sin aprobar'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}