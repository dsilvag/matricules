<?php

namespace App\Filament\Resources\InstanceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Dwelling; 
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Models\Instance;

class DomiciliRelationManager extends RelationManager
{
    protected static string $relationship = 'domicili';

    protected static ?string $title = 'Buscar domicili';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('DOMCOD')
            ->columns([
                Tables\Columns\TextColumn::make('DOMCOD')
                    ->label('DOMCOD')
                    ->numeric()
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('street.CARSIG')
                    ->searchable(isIndividual: true)
                    ->label('CARSIG'),
                Tables\Columns\TextColumn::make('street.CARDESC')
                    ->searchable(isIndividual: true)
                    ->label('CARDESC'),
                Tables\Columns\TextColumn::make('DOMNUM')
                    ->searchable(isIndividual: true)
                    ->label('DOMNUM'),
                Tables\Columns\TextColumn::make('DOMBIS')
                    ->searchable(isIndividual: true)
                    ->label('DOMBIS'),
                Tables\Columns\TextColumn::make('DOMNUM2')
                    ->searchable(isIndividual: true)
                    ->label('DOMNUM2'),
                Tables\Columns\TextColumn::make('DOMBIS2')
                    ->searchable(isIndividual: true)
                    ->label('DOMBIS2'),
                Tables\Columns\TextColumn::make('DOMESC')
                    ->searchable(isIndividual: true)
                    ->label('DOMESC'),
                Tables\Columns\TextColumn::make('DOMPIS')
                    ->searchable(isIndividual: true)
                    ->label('DOMPIS'),
                Tables\Columns\TextColumn::make('DOMPTA')
                    ->searchable(isIndividual: true)
                    ->label('DOMPTA'),
                Tables\Columns\TextColumn::make('DOMBLOC')
                    ->searchable(isIndividual: true)
                    ->label('DOMBLOC'),
                Tables\Columns\TextColumn::make('DOMPTAL')
                    ->searchable(isIndividual: true)
                    ->label('DOMPTAL'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('assignDomicili')
                    ->label('Asignar Domicili Accés')
                    ->icon('heroicon-m-pencil')
                    ->action(function (Dwelling $dom) {
                        $instance = $this->ownerRecord;
                        $instance->skipValidation();
                        $instance->domicili_acces = $dom->DOMCOD;
                        $instance->save();
                    })
            ])
            ->bulkActions([
                
            ]);
    }
    public function getTableQuery(): Builder
    {
        return Dwelling::query();
    }
}
