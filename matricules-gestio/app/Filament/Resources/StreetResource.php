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

use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
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

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $pluralModelLabel = 'Carrers';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Dades de la Direcció')
                ->icon('heroicon-o-map')
                ->description('Informació relacionada amb la direcció de l\'ubicació.')
                ->schema([
                    Forms\Components\TextInput::make('PAISPROVMUNICARCOD')
                        ->required()
                        ->numeric()
                        ->label('PAISPROVMUNICARCOD'),
                    Forms\Components\TextInput::make('CARCOD')
                        ->required()
                        ->numeric()
                        ->label('CARCOD'),
                    Forms\Components\TextInput::make('PAISCOD')
                        ->required()
                        ->numeric()
                        ->label('PAISCOD'),
                    Forms\Components\TextInput::make('PROVCOD')
                        ->required()
                        ->numeric()
                        ->label('PROVCOD'),
                    Forms\Components\TextInput::make('MUNICOD')
                        ->required()
                        ->numeric()
                        ->label('MUNICOD'),
                ])->columns(5),

            Section::make('Descripció del Carrer')
                ->icon('heroicon-o-map-pin')
                ->description('Dades descriptives sobre el carrer.')
                ->schema([
                    Forms\Components\TextInput::make('CARSIG')
                        ->maxLength(5)
                        ->label('CARSIG'),
                    Forms\Components\TextInput::make('CARPAR')
                        ->maxLength(6)
                        ->label('CARPAR'),
                    Forms\Components\TextInput::make('CARDESC')
                        ->required()
                        ->maxLength(50)
                        ->label('CARDESC'),
                    Forms\Components\TextInput::make('CARDESC2')
                        ->maxLength(25)
                        ->label('CARDESC2'),
                ])->columns(4),

            Section::make('Temps i Modificacions')
                ->icon('heroicon-o-clock')
                ->description('Informació sobre les modificacions i dates rellevants.')
                ->schema([
                    Forms\Components\TextInput::make('STDUGR')
                        ->maxLength(20)
                        ->label('STDUGR'),
                    Forms\Components\TextInput::make('STDUMOD')
                        ->maxLength(20)
                        ->label('STDUMOD'),
                    Forms\Components\TextInput::make('STDDGR')
                        ->maxLength(8)
                        ->label('STDDGR'),
                    Forms\Components\TextInput::make('STDDMOD')
                        ->maxLength(8)
                        ->label('STDDMOD'),
                ])->columns(4),

            Section::make('Altres Dades')
                ->icon('heroicon-o-information-circle')
                ->description('Altres dades rellevants associades al carrer.')
                ->schema([
                    Forms\Components\TextInput::make('STDHGR')
                        ->maxLength(6)
                        ->label('STDHGR'),
                    Forms\Components\TextInput::make('STDHMOD')
                        ->maxLength(6)
                        ->label('STDHMOD'),
                    Forms\Components\TextInput::make('VALDATA')
                        ->maxLength(8)
                        ->label('VALDATA'),
                    Forms\Components\TextInput::make('BAIXASW')
                        ->maxLength(1)
                        ->label('BAIXASW'),
                ])->columns(4),

            Section::make('Informació Organització')
                ->icon('heroicon-o-building-library')
                ->description('Dades relacionades amb l\'organització i la seva ubicació.')
                ->schema([
                    Forms\Components\TextInput::make('ORGOBS')
                        ->maxLength(4000)
                        ->label('ORGOBS'),
                    Forms\Components\TextInput::make('PLACA')
                        ->maxLength(255)
                        ->label('PLACA'),
                    Forms\Components\TextInput::make('GENERIC')
                        ->maxLength(50)
                        ->label('GENERIC'),
                    Forms\Components\TextInput::make('ESPECIFIC')
                        ->maxLength(50)
                        ->label('ESPECIFIC'),
                ])->columns(4),

            Section::make('Informació Addicional')
                ->icon('heroicon-o-document-text')
                ->description('Dades addicionals per a la gestió del carrer.')
                ->schema([
                    Forms\Components\TextInput::make('INICIFI')
                        ->maxLength(4000)
                        ->label('INICIFI'),
                    Forms\Components\TextInput::make('OBSERVACIONS')
                        ->maxLength(4000)
                        ->label('OBSERVACIONS'),
                    Forms\Components\TextInput::make('ORGCOD')
                        ->maxLength(4)
                        ->label('ORGCOD'),
                    Forms\Components\TextInput::make('ORGDATA')
                        ->maxLength(8)
                        ->label('ORGDATA'),
                ])->columns(4),

            Section::make('Informació Complementària')
                ->icon('heroicon-o-users')
                ->description('Dades complementàries sobre la temàtica i la ubicació.')
                ->schema([
                    Forms\Components\TextInput::make('TEMATICA')
                        ->maxLength(50)
                        ->label('TEMATICA'),
                    Forms\Components\TextInput::make('SEXE')
                        ->maxLength(1)
                        ->label('SEXE'),
                    Forms\Components\TextInput::make('LOCAL')
                        ->maxLength(1)
                        ->label('LOCAL'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PAISPROVMUNICARCOD')
                    ->label('PAISPROVMUNICARCOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CARSIG')
                    ->label('CARSIG')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CARPAR')
                    ->label('CARPAR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CARDESC')
                    ->label('CARDESC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CARDESC2')
                    ->label('CARDESC2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDUGR')
                    ->label('STDUGR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDUMOD')
                    ->label('STDUMOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDDGR')
                    ->label('STDDGR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDDMOD')
                    ->label('STDDMOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDHGR')
                    ->label('STDHGR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDHMOD')
                    ->label('STDHMOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('VALDATA')
                    ->label('VALDATA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('BAIXASW')
                    ->label('BAIXASW')
                    ->searchable(),
                Tables\Columns\TextColumn::make('INICIFI')
                    ->label('INICIFI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('OBSERVACIONS')
                    ->label('OBSERVACIONS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ORGCOD')
                    ->label('ORGCOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ORGDATA')
                    ->label('ORGDATA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ORGOBS')
                    ->label('ORGOBS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PLACA')
                    ->label('PLACA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('GENERIC')
                    ->label('GENERIC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ESPECIFIC')
                    ->label('ESPECIFIC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('TEMATICA')
                    ->label('TEMATICA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('SEXE')
                    ->label('SEXE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('LOCAL')
                    ->label('LOCAL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
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
                Tables\Actions\Action::make('syncOracleToMysql')
                    ->label('Sincronizar Oracle a MySQL')
                    ->color('success') // Color del botón
                    ->action(function () {
                        self::syncOracleToMysql();
                    })//->hidden(fn ($record) => !auth()->user()->hasRole('Admin')),
                /*
                ExportAction::make()
                    ->hidden(fn ($record) => !auth()->user()->hasRole('Admin'))
                    ->exporter(StreetExporter::class)
                    ->label('Exportar carrers')
                    ->formats([
                        ExportFormat::Csv,
                    ]),

                ImportAction::make()
                    ->hidden(fn ($record) => !auth()->user()->hasRole('Admin'))
                    ->importer(StreetImporter::class)
                    ->csvDelimiter(';')
                    ->label('Importar carrers'),*/
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->hidden(fn () => !auth()->user()->hasRole('Admin'))
                    ->exporter(StreetExporter::class)
                    ->formats([
                        ExportFormat::Csv,
                    ]),
            ]);
    }

    private static function syncOracleToMysql()
    {
        // Asegúrate de que el archivo existe antes de incluirlo
        $filePath = base_path(env('SCRIPT_STREET'));
        
        if (file_exists($filePath)) {
            include_once $filePath;
        } else {
            // Si el archivo no existe, lanzar un error o manejarlo
            session()->flash('error', 'El script de sincronización no fue encontrado.');
        }
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
