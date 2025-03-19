<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DwellingResource\Pages;
use App\Filament\Resources\DwellingResource\RelationManagers;
use App\Models\Dwelling;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\DwellingExporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Imports\DwellingImporter;
use Filament\Tables\Actions\ImportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;


class DwellingResource extends Resource
{
    protected static ?string $model = Dwelling::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('PAISCOD')
                    ->numeric(),
                Forms\Components\TextInput::make('PROVCOD')
                    ->numeric(),
                Forms\Components\TextInput::make('MUNICOD')
                    ->numeric(),
                Forms\Components\TextInput::make('CARCOD')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('PSEUDOCOD')
                    ->numeric(),
                Forms\Components\TextInput::make('GISCOD')
                    ->maxLength(255),
                Forms\Components\TextInput::make('DOMNUM')
                    ->maxLength(4),
                Forms\Components\TextInput::make('DOMBIS')
                    ->maxLength(1),
                Forms\Components\TextInput::make('DOMNUM2')
                    ->maxLength(4),
                Forms\Components\TextInput::make('DOMBIS2')
                    ->maxLength(1),
                Forms\Components\TextInput::make('DOMESC')
                    ->maxLength(2),
                Forms\Components\TextInput::make('DOMPIS')
                    ->maxLength(3),
                Forms\Components\TextInput::make('DOMPTA')
                    ->maxLength(4),
                Forms\Components\TextInput::make('DOMBLOC')
                    ->maxLength(2),
                Forms\Components\TextInput::make('DOMPTAL')
                    ->maxLength(2),
                Forms\Components\TextInput::make('DOMKM')
                    ->numeric(),
                Forms\Components\TextInput::make('DOMHM')
                    ->numeric(),
                Forms\Components\TextInput::make('DOMTLOC')
                    ->maxLength(1),
                Forms\Components\TextInput::make('APCORREUS')
                    ->numeric(),
                Forms\Components\TextInput::make('DOMTIP')
                    ->maxLength(4),
                Forms\Components\TextInput::make('DOMOBS')
                    ->maxLength(256),
                Forms\Components\TextInput::make('VALDATA')
                    ->maxLength(8),
                Forms\Components\TextInput::make('BAIXASW')
                    ->maxLength(1),
                Forms\Components\TextInput::make('STDAPLADD')
                    ->maxLength(5),
                Forms\Components\TextInput::make('STDAPLMOD')
                    ->maxLength(5),
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
                Forms\Components\TextInput::make('DOMCP')
                    ->maxLength(20),
                Forms\Components\TextInput::make('X')
                    ->numeric(),
                Forms\Components\TextInput::make('Y')
                    ->numeric(),
                Forms\Components\TextInput::make('POBLDESC')
                    ->maxLength(50),
                Forms\Components\TextInput::make('GID')
                    ->maxLength(32),
                Forms\Components\TextInput::make('SWREVISAT')
                    ->numeric(),
                Forms\Components\TextInput::make('REFCADASTRAL')
                    ->maxLength(255),
                Forms\Components\TextInput::make('SWPARE')
                    ->numeric(),
                Forms\Components\TextInput::make('CIV')
                    ->maxLength(24),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('DOMCOD')
                    ->numeric()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('PSEUDOCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('GISCOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMNUM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMBIS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMNUM2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMBIS2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMESC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMPIS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMPTA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMBLOC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMPTAL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMKM')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('DOMHM')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('DOMTLOC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('APCORREUS')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('DOMTIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMOBS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('VALDATA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('BAIXASW')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDAPLADD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDAPLMOD')
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
                Tables\Columns\TextColumn::make('DOMCP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('X')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Y')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('POBLDESC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('GID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('SWREVISAT')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('REFCADASTRAL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('SWPARE')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('CIV')
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
                    ->exporter(DwellingExporter::class)
                    ->label('Export Dwelling')
                    ->formats([
                        ExportFormat::Csv,
                    ]),

                ImportAction::make()
                    ->importer(DwellingImporter::class)
                    ->csvDelimiter(';')
                    ->label('Import Dwelling'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(DwellingExporter::class)
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
            'index' => Pages\ListDwellings::route('/'),
            'create' => Pages\CreateDwelling::route('/create'),
            'edit' => Pages\EditDwelling::route('/{record}/edit'),
        ];
    }
}
