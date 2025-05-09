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
        $domcod = $ownerRecord->domicili_acces;
        $resnume = $ownerRecord->RESNUME;
        $avui = now()->format('Y-m-d');
        $vehicleCount = Vehicle::whereHas('instance', function ($query) use ($domcod,$resnume,$avui) {
            $query->where('domicili_acces', $domcod)
            ->where('RESNUME', '!=', $resnume)
            ->where('DATAEXP', '>=', $avui);
        })->count();
        return "Altres vehicles al mateix domicili ($vehicleCount)";
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
                    ->label('DATAINICI')
                    ->date('d/m/Y'),    
                Tables\Columns\TextColumn::make('DATAEXP')
                    ->label('DATAEXP')
                    ->date('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public function getTableQuery(): Builder
    {
        return Vehicle::whereHas('instance', function ($query){
            $query->where('domicili_acces', $this->ownerRecord->domicili_acces)
                ->where('RESNUME', '!=', $this->ownerRecord->RESNUME)
                ->where('DATAEXP', '>=', now()->format('Y-m-d'));
        });
    }
}
