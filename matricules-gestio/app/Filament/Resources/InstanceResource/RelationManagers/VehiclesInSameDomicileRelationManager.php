<?php

namespace App\Filament\Resources\InstanceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Vehicle;

class VehiclesInSameDwellingRelationManager extends RelationManager
{
    protected static string $relationship = 'vehiclesInSameDwelling';
/*
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('DOMCOD')
                    ->required()
                    ->maxLength(255),
            ]);
    } */

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        $domcod = $ownerRecord->DOMCOD;
        $vehicleCount = Vehicle::whereHas('instance', function ($query) use ($domcod) {
            $query->where('DOMCOD', $domcod);
        })->count();
        return "Vehicles del mateix domicili ($vehicleCount)"; // Título personalizado
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('MATRICULA')
            ->columns([
                Tables\Columns\TextColumn::make('MATRICULA')
                    ->label('MATRICULA'),
                Tables\Columns\TextColumn::make('instance.RESNUME')
                    ->label('RESNUME'),    
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
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
    public function getTableQuery(): Builder
    {
        $domcod = $this->ownerRecord->DOMCOD; // Obtener el DOMCOD de la instancia actual

        // Consulta a la tabla de vehículos para obtener los que pertenecen a instancias con el mismo DOMCOD
        return Vehicle::whereHas('instance', function ($query) use ($domcod) {
            $query->where('DOMCOD', $domcod); // Filtra por el DOMCOD de la instancia
        });
    }
}
