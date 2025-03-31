<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstanceResource\Pages;
use App\Filament\Resources\InstanceResource\RelationManagers;
use App\Filament\Resources\InstanceResource\RelationManagers\VehiclesRelationManager;
use App\Models\Instance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class InstanceResource extends Resource
{
    protected static ?string $model = Instance::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $pluralModelLabel = 'Instàncies';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->icon('heroicon-o-plus')
                    ->schema([
                        Forms\Components\Placeholder::make('Missatge d\'Informació')
                            ->content('Per afegir vehicles a la instància, primer cal crear la instància'),
                    ])->visibleOn('create'),
                Section::make()
                    ->icon('heroicon-o-key')
                    ->schema([
                        Forms\Components\TextInput::make('RESNUME')
                            ->label('RESNUME')
                            ->required()
                            ->minLength(11)
                            ->maxLength(11),
                    ]),
                Section::make()
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('NUMEXP')
                            ->visibleOn('edit')
                            ->label('NUMEXP')
                            ->required()
                            ->maxLength(11),
                        Forms\Components\TextInput::make('DECRETAT')
                            ->visibleOn('edit')
                            ->label('DECRETAT')
                            ->maxLength(255),
                        Forms\Components\Radio::make('VALIDAT')
                            ->visibleOn('edit')
                            ->label('VALIDAT / REBUTJAT')
                            ->options([
                                'validat' => 'VALIDAT',
                                'rebutjat' => 'REBUTJAT',
                                
                            ]),
                        ])->columns(3)->visibleOn('edit'),
                Section::make()
                    ->icon('heroicon-o-identification')
                    ->schema([    
                        Forms\Components\Select::make('PERSCOD')
                        ->visibleOn('edit')
                        ->required()
                        ->label('SOL.LICITANT')
                        ->relationship('person', 'PERSCOD') 
                        ->preload()
                        ->searchable()
                        ->options(function () {
                            return \App\Models\Person::all()
                                ->mapWithKeys(function ($person) {
                                    return [
                                        $person->PERSCOD => $person->nom_person
                                    ];
                                });
                        }),

                        Forms\Components\Select::make('REPRCOD')
                            ->visibleOn('edit')
                            ->label('REPRESENTANT')
                            //->description('quan calgui')
                            ->relationship('personRepresentative', 'REPRCOD') 
                            ->preload()
                            ->searchable()
                            ->options(function () {
                                return \App\Models\Person::all()
                                    ->mapWithKeys(function ($person) {
                                        return [
                                            $person->PERSCOD => $person->nom_person
                                        ];
                                    });
                            }),
                    ])->columns(2)->visibleOn('edit'),
                Section::make()
                    ->icon('heroicon-o-globe-europe-africa')
                    ->schema([  
                    Forms\Components\Select::make('DOMCOD')
                        ->visibleOn('edit')
                        ->required()
                        ->label('CODI DOMICILI')
                        ->relationship('domicili', 'DOMCOD')
                        ->preload()
                        ->searchable()
                        //Mostrem el nom del carrer i la direccio de la vivenda
                        ->options(function () {
                            return \App\Models\Dwelling::with('street')
                            ->get()
                            ->mapWithKeys(function ($dwelling) {
                                $streetName = $dwelling->street ? $dwelling->street->nom_carrer : 'No disponible';
                                return [
                                    $dwelling->DOMCOD => "{$dwelling->DOMCOD} {$streetName}, {$dwelling->DOMNUM} {$dwelling->DOMBIS} {$dwelling->DOMNUM2} {$dwelling->DOMBIS2} {$dwelling->DOMESC} {$dwelling->DOMPIS} {$dwelling->DOMPTA} {$dwelling->DOMBLOC} {$dwelling->DOMPTAL} {$dwelling->DOMKM} {$dwelling->DOMHM}"
                                ];
                            });
                        }),
                    Forms\Components\MultiSelect::make('carrersBarriVell')
                        ->visibleOn('edit')
                        ->label('CARRERS VALIDATS')
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
                ])->columns(2)->visibleOn('edit')
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('RESNUME')
                    ->searchable(),
                Tables\Columns\TextColumn::make('NUMEXP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DECRETAT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('VALIDAT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSCOD')
                    ->numeric()
                    ->sortable(),
                /*Tables\Columns\TextColumn::make('vehicles.MATRICULA')
                    ->searchable(),*/
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
            VehiclesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstances::route('/'),
            'create' => Pages\CreateInstance::route('/create'),
            'edit' => Pages\EditInstance::route('/{record}/edit'),
        ];
    }
}
