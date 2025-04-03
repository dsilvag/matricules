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
use App\Models\Person;
use App\Models\Dwelling;
use App\Models\StreetBarriVell;

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
                        //->preload()
                        ->searchable()
                        ->getOptionLabelFromRecordUsing(fn(Person $record):string =>"{$record->nom_person}"),

                        Forms\Components\Select::make('REPRCOD')
                            ->visibleOn('edit')
                            ->label('REPRESENTANT')
                            //->description('quan calgui')
                            ->relationship('personRepresentative', 'PERSCOD')
                            //->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn(Person $record):string =>"{$record->nom_person}"),
                    ])->columns(2)->visibleOn('edit'),
                    
                Section::make()
                    ->icon('heroicon-o-globe-europe-africa')
                    ->schema([  
                    Forms\Components\Select::make('DOMCOD')
                        ->visibleOn('edit')
                        ->required()
                        ->label('CODI DOMICILI')
                        ->relationship('domicili', 'DOMCOD')
                        ->getOptionLabelFromRecordUsing(fn(Dwelling $record): string => 
                            "{$record->DOMCOD} {$record->street->nom_carrer}, {$record->nom_habitatge}")
                        ->searchable(),
                    Forms\Components\Select::make('carrersBarriVell')
                        ->visibleOn('edit')
                        ->label('CARRERS VALIDATS')
                        ->relationship('carrersBarriVell', 'CARCOD') 
                        ->preload()
                        ->searchable()
                        ->multiple()
                        ->getOptionLabelFromRecordUsing(fn(StreetBarriVell $record): string => "{$record->nom_carrer}"),
                ])->columns(2)->visibleOn('edit')
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('RESNUME')
                    ->label('RESNUME')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('NUMEXP')
                    ->label('NUMEXP')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('person.nom_person')
                    ->label('PERSONA')
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'word-wrap: break-word; word-break: normal; white-space: normal;',
                    ])
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('personRepresentative.nom_person')
                    ->label('REPRESENTANT')
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'word-wrap: break-word; word-break: normal; white-space: normal;',
                    ])
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('carrersBarriVell.street.CARSIG')
                    ->label('CARSIG')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('carrersBarriVell.street.CARDESC')
                    ->label('CARRERS VALIDATS')
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'word-wrap: break-word; word-break: normal; white-space: normal;',
                    ])
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('DECRETAT')
                        ->label('DECRETAT')
                        ->searchable()
                        ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('VALIDAT')
                        ->label('VALIDAT')
                        ->searchable()
                        ->searchable(isIndividual: true),
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
