<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StreetResource\Pages;
use App\Filament\Resources\StreetResource\RelationManagers;
use App\Models\Street;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\StreetExporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Imports\StreetImporter;
use Filament\Tables\Actions\ImportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class StreetResource extends Resource
{
    protected static ?string $model = Street::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('PAISCOD')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('PROVCOD')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('MUNICOD')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('CARSIG')
                    ->maxLength(5),
                Forms\Components\TextInput::make('CARPAR')
                    ->maxLength(6),
                Forms\Components\TextInput::make('CARDESC')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('CARDESC2')
                    ->maxLength(25),
                Forms\Components\TextInput::make('STDUGR')
                    ->maxLength(20),
                Forms\Components\TextInput::make('STDUMOD')
                    ->maxLength(20),
                Forms\Components\TextInput::make('STDDGR')
                    ->maxLength(8),
                Forms\Components\TextInput::make('STDDMOD')
                    ->maxLength(8),
                Forms\Components\TextInput::make('STDHGR')
                    ->maxLength(6),
                Forms\Components\TextInput::make('STDHMOD')
                    ->maxLength(6),
                Forms\Components\TextInput::make('VALDATA')
                    ->maxLength(8),
                Forms\Components\TextInput::make('BAIXASW')
                    ->maxLength(1),
                Forms\Components\TextInput::make('INICIFI')
                    ->maxLength(4000),
                Forms\Components\TextInput::make('OBSERVACIONS')
                    ->maxLength(4000),
                Forms\Components\TextInput::make('ORGCOD')
                    ->maxLength(4),
                Forms\Components\TextInput::make('ORGDATA')
                    ->maxLength(8),
                Forms\Components\TextInput::make('ORGOBS')
                    ->maxLength(4000),
                Forms\Components\TextInput::make('PLACA')
                    ->maxLength(255),
                Forms\Components\TextInput::make('GENERIC')
                    ->maxLength(50),
                Forms\Components\TextInput::make('ESPECIFIC')
                    ->maxLength(50),
                Forms\Components\TextInput::make('TEMATICA')
                    ->maxLength(50),
                Forms\Components\TextInput::make('SEXE')
                    ->maxLength(1),
                Forms\Components\TextInput::make('LOCAL')
                    ->maxLength(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PAISCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PROVCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MUNICOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('CARCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('CARSIG')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CARPAR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CARDESC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CARDESC2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDUGR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDUMOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDDGR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDDMOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDHGR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDHMOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('VALDATA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('BAIXASW')
                    ->searchable(),
                Tables\Columns\TextColumn::make('INICIFI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('OBSERVACIONS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ORGCOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ORGDATA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ORGOBS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PLACA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('GENERIC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ESPECIFIC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('TEMATICA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('SEXE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('LOCAL')
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
            ->headerActions([
                ExportAction::make()
                    ->exporter(StreetExporter::class)
                    ->label('Export Streets')
                    ->formats([
                        ExportFormat::Csv,
                    ]),

                ImportAction::make()
                    ->importer(StreetImporter::class)
                    ->csvDelimiter(';')
                    ->label('Import Streets'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(StreetExporter::class)
                    ->formats([
                        ExportFormat::Csv,
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
            'index' => Pages\ListStreets::route('/'),
            'create' => Pages\CreateStreet::route('/create'),
            'edit' => Pages\EditStreet::route('/{record}/edit'),
        ];
    }
}
