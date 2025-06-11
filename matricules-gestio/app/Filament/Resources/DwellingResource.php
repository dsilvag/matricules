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
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\DwellingExporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Imports\DwellingImporter;
use Filament\Tables\Actions\ImportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Action;


class DwellingResource extends Resource
{
    protected static ?string $model = Dwelling::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $pluralModelLabel = 'Habitatges';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // Direcció
            Section::make('Direcció')
                ->icon('heroicon-o-map-pin')
                ->description('Introduïu la informació relacionada amb la direcció del domicili.')
                ->schema([
                    Forms\Components\TextInput::make('PAISPROVMUNIDOMCOD')->required()->numeric()->label('PAISPROVMUNIDOMCOD'),
                    Forms\Components\TextInput::make('PAISPROVMUNICARCOD')->required()->numeric()->label('PAISPROVMUNICARCOD'),
                    
                    Forms\Components\TextInput::make('DOMCOD')->required()->numeric()->label('DOMCOD'),
                    Forms\Components\TextInput::make('PAISCOD')->numeric()->label('PAISCOD'),
                    Forms\Components\TextInput::make('PROVCOD')->numeric()->label('PROVCOD'),
                    Forms\Components\TextInput::make('MUNICOD')->numeric()->label('MUNICOD'),
                    Forms\Components\TextInput::make('CARCOD')->required()->numeric()->label('CARCOD'),

                    Forms\Components\TextInput::make('PSEUDOCOD')->numeric()->label('PSEUDOCOD'),
                    Forms\Components\TextInput::make('GISCOD')->maxLength(255)->label('GISCOD'),
                    Forms\Components\TextInput::make('DOMNUM')->maxLength(4)->label('DOMNUM'),
                    Forms\Components\TextInput::make('DOMBIS')->maxLength(1)->label('DOMBIS'),
                    Forms\Components\TextInput::make('DOMNUM2')->maxLength(4)->label('DOMNUM2'),
                    Forms\Components\TextInput::make('DOMBIS2')->maxLength(1)->label('DOMBIS2'),
                    Forms\Components\TextInput::make('DOMESC')->maxLength(2)->label('DOMESC'),
                    Forms\Components\TextInput::make('DOMPIS')->maxLength(3)->label('DOMPIS'),
                    Forms\Components\TextInput::make('DOMPTA')->maxLength(4)->label('DOMPTA'),
                    Forms\Components\TextInput::make('DOMBLOC')->maxLength(2)->label('DOMBLOC'),
                    Forms\Components\TextInput::make('DOMPTAL')->maxLength(2)->label('DOMPTAL'),
                    Forms\Components\TextInput::make('DOMKM')->numeric()->label('DOMKM'),
                    Forms\Components\TextInput::make('DOMHM')->numeric()->label('DOMHM'),
                    Forms\Components\TextInput::make('DOMTLOC')->maxLength(1)->label('DOMTLOC'),
                    Forms\Components\TextInput::make('DOMTIP')->maxLength(4)->label('DOMTIP'),
                    Forms\Components\TextInput::make('DOMOBS')->maxLength(256)->label('DOMOBS'),
                ])->columnSpan(2)->columns(3),
                                
            // Estat i Validació
            Section::make('Estat i Validació')
                ->icon('heroicon-o-check-circle')
                ->description('Indiqueu l\'estat de validació del domicili i altres dades associades.')
                ->schema([
                    Forms\Components\TextInput::make('VALDATA')->maxLength(8)->label('VALDATA'),
                    Forms\Components\TextInput::make('BAIXASW')->maxLength(1)->label('BAIXASW'),
                    Forms\Components\TextInput::make('SWREVISAT')->numeric()->label('SWREVISAT'),
                    Forms\Components\TextInput::make('SWPARE')->numeric()->label('SWPARE'),
                ])->columnSpan(1)->columns(2),
        
            // Temps i Modificacions
            Section::make('Temps i Modificacions')
                ->icon('heroicon-o-clock')
                ->description('Introduïu la informació sobre els canvis i modificacions de l\'habitatge.')
                ->schema([
                    Forms\Components\TextInput::make('STDAPLADD')->maxLength(5)->label('STDAPLADD'),
                    Forms\Components\TextInput::make('STDAPLMOD')->maxLength(5)->label('STDAPLMOD'),
                    Forms\Components\TextInput::make('STDUGR')->maxLength(20)->label('STDUGR'),
                    Forms\Components\TextInput::make('STDUMOD')->maxLength(20)->label('STDUMOD'),
                    Forms\Components\TextInput::make('STDDGR')->maxLength(8)->label('STDDGR'),
                    Forms\Components\TextInput::make('STDDMOD')->maxLength(8)->label('STDDMOD'),
                    Forms\Components\TextInput::make('STDHGR')->maxLength(6)->label('STDHGR'),
                    Forms\Components\TextInput::make('STDHMOD')->maxLength(6)->label('STDHMOD'),
                ])->columnSpan(1)->columns(4),
        
            // Informació Geogràfica i Catastral
            Section::make('Informació Geogràfica i Catastral')
                ->icon('heroicon-o-flag')
                ->description('Dades relacionades amb la ubicació geogràfica i la informació catastral del domicili.')
                ->schema([
                    Forms\Components\TextInput::make('APCORREUS')->numeric()->label('APCORREUS'),
                    Forms\Components\TextInput::make('DOMCP')->maxLength(20)->label('DOMCP'),
                    Forms\Components\TextInput::make('X')->numeric()->label('X'),
                    Forms\Components\TextInput::make('Y')->numeric()->label('Y'),
                    Forms\Components\TextInput::make('POBLDESC')->maxLength(50)->label('POBLDESC'),
                    Forms\Components\TextInput::make('REFCADASTRAL')->maxLength(255)->label('REFCADASTRAL'),
                ])->columnSpan(1)->columns(3),
        
            // Informació Cívica i Altres Dades
            Section::make('Informació Cívica i Altres Dades')
                ->icon('heroicon-o-users')
                ->description('Incloeu informació relacionada amb el codi cívic i altres dades addicionals.')
                ->schema([
                    Forms\Components\TextInput::make('CIV')->maxLength(24)->label('CIV'),
                    Forms\Components\TextInput::make('GUID')->maxLength(32)->label('GUID'),
                    Forms\Components\TextInput::make('PSEUDOCOD')->numeric()->label('PSEUDOCOD'),
                ])->columnSpan(1)->columns(3),
        ])->columns(2);
        
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('DOMCOD')
                    ->label('DOMCOD')
                    ->numeric()
                    ->searchable()
                    //->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('PAISCOD')
                    ->label('PAISCOD')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PROVCOD')
                    ->label('PROVCOD')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MUNICOD')
                    ->label('MUNICOD')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('CARCOD')
                    ->label('CARCOD')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('street.CARSIG')
                    ->searchable()
                    ->label('SIGLES'),
                Tables\Columns\TextColumn::make('street.CARDESC')
                    ->searchable()
                    ->label('NOM CARRER'),
                Tables\Columns\TextColumn::make('PSEUDOCOD')
                    ->label('PSEUDOCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('GISCOD')
                    ->label('GISCOD'),
                Tables\Columns\TextColumn::make('DOMNUM')
                    ->label('DOMNUM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMBIS')
                    ->label('DOMBIS'),
                Tables\Columns\TextColumn::make('DOMNUM2')
                    ->label('DOMNUM2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DOMBIS2')
                    ->label('DOMBIS2'),
                Tables\Columns\TextColumn::make('DOMESC')
                    ->label('DOMESC'),
                Tables\Columns\TextColumn::make('DOMPIS')
                    ->label('DOMPIS'),
                Tables\Columns\TextColumn::make('DOMPTA')
                    ->label('DOMPTA'),
                Tables\Columns\TextColumn::make('DOMBLOC')
                    ->label('DOMBLOC'),
                Tables\Columns\TextColumn::make('DOMPTAL')
                    ->label('DOMPTAL'),
                Tables\Columns\TextColumn::make('DOMKM')
                    ->label('DOMKM')
                    ->numeric(),
                Tables\Columns\TextColumn::make('DOMHM')
                    ->label('DOMHM')
                    ->numeric(),
                Tables\Columns\TextColumn::make('DOMTLOC')
                    ->label('DOMTLOC'),
                Tables\Columns\TextColumn::make('APCORREUS')
                    ->label('APCORREUS')
                    ->numeric(),
                Tables\Columns\TextColumn::make('DOMTIP')
                    ->label('DOMTIP'),
                Tables\Columns\TextColumn::make('DOMOBS')
                    ->label('DOMOBS'),
                Tables\Columns\TextColumn::make('VALDATA')
                    ->label('VALDATA'),
                Tables\Columns\TextColumn::make('BAIXASW')
                    ->label('BAIXASW'),
                Tables\Columns\TextColumn::make('STDAPLADD')
                    ->label('STDAPLADD'),
                Tables\Columns\TextColumn::make('STDAPLMOD')
                    ->label('STDAPLMOD'),
                Tables\Columns\TextColumn::make('STDUGR')
                    ->label('STDUGR'),
                Tables\Columns\TextColumn::make('STDUMOD')
                    ->label('STDUMOD'),
                Tables\Columns\TextColumn::make('STDDGR')
                    ->label('STDDGR'),
                Tables\Columns\TextColumn::make('STDDMOD')
                    ->label('STDDMOD'),
                Tables\Columns\TextColumn::make('STDHGR')
                    ->label('STDHGR'),
                Tables\Columns\TextColumn::make('STDHMOD')
                    ->label('STDHMOD'),
                Tables\Columns\TextColumn::make('DOMCP')
                    ->label('DOMCP'),
                Tables\Columns\TextColumn::make('X')
                    ->label('X')
                    ->numeric(),
                Tables\Columns\TextColumn::make('Y')
                    ->label('Y')
                    ->numeric(),
                Tables\Columns\TextColumn::make('POBLDESC')
                    ->label('POBLDESC'),
                Tables\Columns\TextColumn::make('GUID')
                    ->label('GUID'),
                Tables\Columns\TextColumn::make('SWREVISAT')
                    ->label('SWREVISAT')
                    ->numeric(),
                Tables\Columns\TextColumn::make('REFCADASTRAL')
                    ->label('REFCADASTRAL'),
                Tables\Columns\TextColumn::make('SWPARE')
                    ->label('SWPARE')
                    ->numeric(),
                Tables\Columns\TextColumn::make('CIV')
                    ->label('CIV'),
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
                Tables\Actions\Action::make('syncOracleToMysql')
                    ->label('Sincronitzar dades BPM')
                    ->color('success') // Color del botón
                    ->action(function () {
                        self::syncOracleToMysql();
                    })
                    //->hidden(fn ($record) => !auth()->user()->hasRole('Admin')),
                /*
                ExportAction::make()
                    ->hidden(fn ($record) => !auth()->user()->hasRole('Admin'))
                    ->exporter(DwellingExporter::class)
                    ->label('Exportar habitatges')
                    ->formats([
                        ExportFormat::Csv,
                    ]),

                ImportAction::make()
                    ->hidden(fn ($record) => !auth()->user()->hasRole('Admin'))
                    ->importer(DwellingImporter::class)
                    ->csvDelimiter(';')
                    ->label('Importar habitatges'),*/
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->hidden(fn () => !auth()->user()->hasRole('Admin'))
                    ->exporter(DwellingExporter::class)
                    ->formats([
                        ExportFormat::Csv,
                    ]),
            ]);
    }
    private static function syncOracleToMysql()
    {
        // Asegúrate de que el archivo existe antes de incluirlo
        $filePath = base_path(env('SCRIPT_DWELLING'));
        
        if (file_exists($filePath)) {
            $response = include_once $filePath;
            if($response==true)
            {
                Notification::make()
                ->title('Importació amb èxit')
                ->success()
                ->duration(25000)
                ->sendToDatabase(auth()->user())
                ->send();
            }
            else {
                Notification::make()
                ->title('Error en la importació')
                ->error()
                ->persistent()
                ->sendToDatabase(auth()->user())
                ->send();
            }
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
            'index' => Pages\ListDwellings::route('/'),
            'create' => Pages\CreateDwelling::route('/create'),
            'edit' => Pages\EditDwelling::route('/{record}/edit'),
        ];
    }
}
