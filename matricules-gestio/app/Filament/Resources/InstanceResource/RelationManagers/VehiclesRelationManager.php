<?php

namespace App\Filament\Resources\InstanceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        $vehicleCount = $ownerRecord->vehicles()->count();
        return "Vehicles instància ($vehicleCount)";
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('MATRICULA')
                    ->label('MATRICULA:')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('MATRICULA')
            ->columns([
                Tables\Columns\TextColumn::make('MATRICULA')
                    ->label('MATRICULA'),
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
                Tables\Actions\CreateAction::make(),
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
    public function canCreate(): bool
    {
        if($this->ownerRecord->is_notificat){
            return false;
        }
        else{
            return true;
        }
    }
}
