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
        $resnume = $ownerRecord->RESNUME;
        $vehicleCount = Vehicle::whereHas('instance', function ($query) use ($domcod,$resnume) {
            $query->where('DOMCOD', $domcod)
            ->where('RESNUME', '!=', $resnume);
        })->count();
        return "Altres vehicles al mateix domicili ($vehicleCount)"; // Título personalizado
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
                Tables\Columns\TextColumn::make('DATAINICI')
                    ->label('DATAINICI'),    
                Tables\Columns\TextColumn::make('DATAEXP')
                    ->label('DATAEXP'),
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
        $domcod = $this->ownerRecord->DOMCOD;
        $resnume = $this->ownerRecord->RESNUME;

        return Vehicle::whereHas('instance', function ($query) use ($domcod, $resnume) {
            $query->where('DOMCOD', $domcod)
                ->where('RESNUME', '!=', $resnume);
        });
    }
}
