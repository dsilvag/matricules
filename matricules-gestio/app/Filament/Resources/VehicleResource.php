<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use App\Models\StreetBarriVell;


class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('MATRICULA')
                    ->label('MATRICULA:')
                    ->required()
                    ->maxLength(255),
                Forms\Components\MultiSelect::make('carrersBarriVell')
                    ->label('Carrers validats')
                    ->relationship('carrersBarriVell', 'CARCOD') 
                    ->preload()
                    ->searchable()
                    ->multiple()
                    //Mostrem el nom del carrer
                    ->options(function () {
                        return \App\Models\StreetBarriVell::with('street')
                            ->get()
                            ->mapWithKeys(function ($streetBarrivell) {
                                return [
                                    $streetBarrivell->CARCOD => $streetBarrivell->nom_carrer
                                ];
                            });
                    }),
                Forms\Components\Select::make('DOMCOD')
                    ->label('Habitatge')
                    ->relationship('habitatge', 'DOMCOD')
                    ->preload()
                    ->searchable()
                    //Mostrem el nom del carrer i la direccio de la vivenda
                    ->options(function () {
                        return \App\Models\Dwelling::with('street')
                        ->get()
                        ->mapWithKeys(function ($dwelling) {
                            $streetName = $dwelling->street ? $dwelling->street->nom_carrer : 'No disponible';
                            return [
                                $dwelling->DOMCOD => "{$streetName}, {$dwelling->DOMNUM} {$dwelling->DOMBIS} {$dwelling->DOMNUM2} {$dwelling->DOMBIS2} {$dwelling->DOMESC} {$dwelling->DOMPIS} {$dwelling->DOMPTA} {$dwelling->DOMBLOC} {$dwelling->DOMPTAL} {$dwelling->DOMKM} {$dwelling->DOMHM}"
                            ];
                        });
                    }),
                Forms\Components\DatePicker::make('DATAEXP'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('MATRICULA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DATAEXP')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('carrersBarriVell.street.CARDESC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('habitatge.DOMCOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
