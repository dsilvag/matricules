<?php

namespace App\Filament\Resources\InstanceResource\RelationManagers;

use App\Filament\Resources\InstanceResource;
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
        $dom1 = $ownerRecord->domicili_acces;
        $dom2 = $ownerRecord->domicili_acces2;
        $dom3 = $ownerRecord->domicili_acces3;
        $id = $ownerRecord->id;
        $avui = now()->format('Y-m-d');

        $vehicleCount = Vehicle::whereHas('instance', function ($query) use ($dom1, $dom2, $dom3, $id, $avui) {
            $query->where(function ($q) use ($dom1, $dom2, $dom3) {
                $q->whereIn('domicili_acces', [$dom1, $dom2, $dom3])
                ->orWhereIn('domicili_acces2', [$dom1, $dom2, $dom3])
                ->orWhereIn('domicili_acces3', [$dom1, $dom2, $dom3]);
            })
            //->where('instance_id', '!=', $id)
            ->where('DATAEXP', '>=', $avui);
        })->count();

        return "Tots els vehicles dels domicilis ($vehicleCount)";
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('MATRICULA')
            ->recordClasses(fn (Vehicle $record) => $record->instance_id == $this->ownerRecord->id ? 'my-green-bg'  : null)
            ->recordUrl(function ($record) {
                return $record->instance
                    ? InstanceResource::getUrl('edit', ['record' => $record->instance->getKey()])
                    : null;
            })
            ->columns([
                Tables\Columns\TextColumn::make('MATRICULA')
                    ->label('MATRICULA'),
                Tables\Columns\TextColumn::make('instance.NUMEXP')
                    ->label('NUMEXP'),
                Tables\Columns\TextColumn::make('instance.domiciliAccess.nom_habitatge')
                    ->label('Domicili 1'),
                Tables\Columns\TextColumn::make('instance.domiciliAccess2.nom_habitatge')
                    ->label('Domicili 2'),
                Tables\Columns\TextColumn::make('instance.domiciliAccess3.nom_habitatge')
                    ->label('Domicili 3'),
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
        $dom1 = $this->ownerRecord->domicili_acces;
        $dom2 = $this->ownerRecord->domicili_acces2;
        $dom3 = $this->ownerRecord->domicili_acces3;
        $id = $this->ownerRecord->id;

        return Vehicle::whereHas('instance', function ($query) use ($dom1, $dom2, $dom3, $id) {
            $query->where(function ($q) use ($dom1, $dom2, $dom3) {
                $q->whereIn('domicili_acces', [$dom1, $dom2, $dom3])
                ->orWhereIn('domicili_acces2', [$dom1, $dom2, $dom3])
                ->orWhereIn('domicili_acces3', [$dom1, $dom2, $dom3]);
            })
            //->where('instance_id', '!=', $id)
            ->where('DATAEXP', '>=', now()->format('Y-m-d'));
        });
    }
}
