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
use Filament\Tables\Filters\Filter;


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
                Forms\Components\DatePicker::make('DATAEXP'),
                Forms\Components\DatePicker::make('DATAINICI'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(function ($record) {
                    return $record->instance
                        ? InstanceResource::getUrl('edit', ['record' => $record->instance->getKey()])
                        : null;
                })
            ->columns([
                Tables\Columns\TextColumn::make('MATRICULA')
                    ->label('MATRICULA')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('instance.RESNUME')
                    ->label('RESNUME')
                    ->sortable()
                    ->searchable(),    
                Tables\Columns\TextColumn::make('instance.NUMEXP')
                    ->label('NUMEXP')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('instance.domiciliAccess.nom_habitatge')
                    ->label('Domicili 1'),
                Tables\Columns\TextColumn::make('instance.domiciliAccess2.nom_habitatge')
                    ->label('Domicili 2'),
                Tables\Columns\TextColumn::make('instance.domiciliAccess3.nom_habitatge')
                    ->label('Domicili 3'),
                Tables\Columns\TextColumn::make('DATAINICI')
                    ->label('DATAINICI')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('DATAEXP')
                    ->label('DATAEXP')
                    ->date()
                    ->sortable(),    
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
                 Filter::make('DATAEXP')->label('Actius')->query(fn ($query) => $query->whereDate('DATAEXP', '>=', now()->toDateString())->whereDate('DATAINICI','<=',now()->toDateString()))->default(true),
                 Filter::make('no_padro')
                    ->label('No padro')
                    ->default(true)
                    ->query(fn ($query) => $query->whereHas('instance', function ($query) {
                        $query->where('RESNUME', '!=', 'PADRO');
                    })),
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

    public static function canCreate(): bool
    {
        return false;
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
