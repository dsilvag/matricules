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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('street.PAISCOD')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('street.PROVCOD')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('street.MUNICOD')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('street.CARCOD')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('street.CARSIG')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARPAR')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARDESC')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARDESC2')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDUGR')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDUMOD')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDDGR')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDDMOD')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDHGR')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDHMOD')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.VALDATA')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.BAIXASW')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.INICIFI')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.OBSERVACIONS')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGCOD')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGDATA')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGOBS')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.PLACA')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.GENERIC')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ESPECIFIC')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.TEMATICA')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.SEXE')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.LOCAL')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
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
