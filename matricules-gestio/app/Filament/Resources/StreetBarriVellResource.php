<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StreetBarriVellResource\Pages;
use App\Filament\Resources\StreetBarriVellResource\RelationManagers;
use App\Models\StreetBarriVell;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;


class StreetBarriVellResource extends Resource
{
    protected static ?string $model = StreetBarriVell::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $pluralModelLabel = 'Carrers barri vell';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('CARCOD')
                ->label('Carrer')
                ->relationship('street','CARDESC')
                ->preload()
                ->searchable() 
                ->options(function () {
                    // Obtener las calles que no están asignadas en el Barri Vell
                    return \App\Models\Street::whereNotIn('CARCOD', StreetBarriVell::pluck('CARCOD'))
                        ->pluck('CARDESC', 'CARCOD'); // Usamos CARCOD para la clave primaria
                }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('street.PAISCOD')
                ->label('PAISCOD')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('street.PROVCOD')
                ->label('PROVCOD')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('street.MUNICOD')
                ->label('MUNICOD')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('street.CARCOD')
                ->label('CARCOD')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('street.CARSIG')
                ->label('CARSIG')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARPAR')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('CARPAR')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARDESC')
                ->label('CARDESC')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARDESC2')
                ->label('CARDESC2')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('street.STDUGR')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('STDUGR')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDUMOD')
                ->toggleable(isToggledHiddenByDefault: true)    
                ->label('STDUMOD')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDDGR')
                ->label('STDDGR')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDDMOD')
                ->label('STDDMOD')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDHGR')
                ->label('STDHGR')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDHMOD')
                ->label('STDHMOD')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.VALDATA')
                ->label('VALDATA')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.BAIXASW')
                ->label('BAIXASW')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.INICIFI')
                ->label('INICIFI')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.OBSERVACIONS')
                ->label('OBSERVACIONS')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGCOD')
                ->label('ORGCOD')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGDATA')
                ->label('ORGDATA')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGOBS')
                ->label('ORGOBS')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.PLACA')
                ->label('PLACA')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.GENERIC')
                ->label('GENERIC')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ESPECIFIC')
                ->label('ESPECIFIC')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.TEMATICA')
                ->label('TEMATICA')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.SEXE')
                ->label('SEXE')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.LOCAL')
                ->label('LOCAL')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.created_at')
                ->label('Created At')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Updated At')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([
                //
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
            'index' => Pages\ListStreetBarriVells::route('/'),
            'create' => Pages\CreateStreetBarriVell::route('/create'),
            'edit' => Pages\EditStreetBarriVell::route('/{record}/edit'),
        ];
    }
}
