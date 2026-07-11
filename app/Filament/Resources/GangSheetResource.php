<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GangSheetResource\Pages;
use App\Models\GangSheet;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class GangSheetResource extends Resource
{
    protected static ?string $model = GangSheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Gang Sheets';

    protected static ?string $pluralModelLabel = 'Gang Sheets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),

                        Forms\Components\TextInput::make('width')
                            ->label('Ancho')
                            ->numeric(),

                        Forms\Components\TextInput::make('height')
                            ->label('Alto')
                            ->numeric(),

                        Forms\Components\TextInput::make('dpi')
                            ->label('DPI')
                            ->numeric(),

                        Forms\Components\Select::make('order_id')
                            ->label('Orden Asociada')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Archivos')
                    ->schema([
                        Forms\Components\TextInput::make('final_path')
                            ->label('Ruta del PNG')
                            ->disabled(),

                        Forms\Components\TextInput::make('preview_path')
                            ->label('Ruta del Preview')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Metadatos')
                    ->schema([
                        Forms\Components\TextInput::make('status')
                            ->label('Estado')
                            ->disabled(),

                        Forms\Components\TextInput::make('image_count')
                            ->label('Cantidad de Imágenes')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_area')
                            ->label('Área Total')
                            ->numeric()
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Orden')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (?string $state): string => $state === null ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('width')
                    ->label('Ancho')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('height')
                    ->label('Alto')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dpi')
                    ->label('DPI')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('image_count')
                    ->label('Imágenes')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Borrador',
                        'processing' => '⏳ Procesando',
                        'completed' => '✅ Completado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => '📝 Borrador',
                        'processing' => '⏳ Procesando',
                        'completed' => '✅ Completado',
                    ]),

                Tables\Filters\Filter::make('sin_orden')
                    ->label('Sin orden asociada')
                    ->query(fn ($query) => $query->whereNull('order_id')),

                Tables\Filters\Filter::make('antiguo')
                    ->label('Más de 15 días sin actualizar')
                    ->query(fn ($query) => $query->where('updated_at', '<', Carbon::now()->subDays(15))),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('mantenimiento')
                    ->label('🧹 Limpiar datos viejos')
                    ->icon('heroicon-o-sparkles')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('⚠️ Mantenimiento: Limpiar Gang Sheets antiguos')
                    ->modalDescription('Se eliminarán todos los Gang Sheets que:\n\n• NO tengan una orden asociada\n• Tengan más de 15 días sin actualizar\n\nEsta acción no puede deshacerse.')
                    ->modalSubmitActionLabel('Sí, eliminar datos viejos')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function (): void {
                        $fifteenDaysAgo = Carbon::now()->subDays(15);
                        
                        $deletedCount = 0;
                        $deletedSize = 0;

                        // Obtener todos los gang sheets que cumplen los criterios
                        $gangSheets = GangSheet::where(function ($query) {
                            $query->whereNull('order_id')
                                  ->orWhere('order_id', '');
                        })
                        ->where('updated_at', '<', $fifteenDaysAgo)
                        ->get();

                        // Log::info('🧹 Iniciando limpieza de Gang Sheets antiguos', [
                        //     'count' => $gangSheets->count(),
                        //     'criteria' => 'Sin order_id y más de 15 días sin actualizar',
                        //     'threshold_date' => $fifteenDaysAgo->toDateTimeString(),
                        // ]);

                        // Eliminar cada uno para disparar los eventos
                        foreach ($gangSheets as $gangSheet) {
                            // Log::info('→ Eliminando Gang Sheet antiguo', [
                            //     'id' => $gangSheet->id,
                            //     'name' => $gangSheet->name,
                            //     'updated_at' => $gangSheet->updated_at,
                            //     'final_path' => $gangSheet->final_path,
                            //     'preview_path' => $gangSheet->preview_path,
                            // ]);

                            $gangSheet->delete();
                            $deletedCount++;
                        }

                        // Log::info('✅ Limpieza de Gang Sheets completada', [
                        //     'deleted_count' => $deletedCount,
                        //     'timestamp' => now()->toDateTimeString(),
                        // ]);

                        \Filament\Notifications\Notification::make()
                            ->title('✅ Limpieza completada')
                            ->body("Se eliminaron $deletedCount Gang Sheets antiguos sin orden asociada.")
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListGangSheets::route('/'),
            'create' => Pages\CreateGangSheet::route('/create'),
            'edit' => Pages\EditGangSheet::route('/{record}/edit'),
        ];
    }
}
