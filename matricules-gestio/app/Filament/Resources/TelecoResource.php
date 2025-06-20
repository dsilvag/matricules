<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelecoResource\Pages;
use App\Filament\Resources\TelecoResource\RelationManagers;
use App\Models\Teleco;
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
use App\Filament\Exports\TelecoExporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Imports\TelecoImporter;
use Filament\Tables\Actions\ImportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class TelecoResource extends Resource
{
    protected static ?string $model = Teleco::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dades del Contacte')
                    ->icon('heroicon-o-phone') // Icono de teléfono
                    ->description('Informació sobre el contacte telefònic o d\'altres canals.')
                    ->schema([
                        Forms\Components\TextInput::make('PERSCOD')
                            ->required()
                            ->numeric()
                            ->label('PERSCOD'),
                        Forms\Components\TextInput::make('NUMORDRE')
                            ->required()
                            ->numeric()
                            ->label('NUMORDRE'),
                        Forms\Components\TextInput::make('TIPCONTACTE')
                            ->maxLength(4)
                            ->label('TIPCONTACTE'),
                        Forms\Components\TextInput::make('CONTACTE')
                            ->maxLength(255)
                            ->label('CONTACTE'),
                    ])
                    ->columns(4),
        
                Section::make('Observacions i Modificacions')
                    ->icon('heroicon-o-pencil') // Icono de lápiz
                    ->description('Detalls i observacions relacionades amb el contacte.')
                    ->schema([
                        Forms\Components\TextInput::make('OBSERVACIONS')
                            ->maxLength(255)
                            ->label('OBSERVACIONS'),
                        Forms\Components\TextInput::make('STDUGR')
                            ->maxLength(20)
                            ->label('STDUGR'),
                        Forms\Components\TextInput::make('STDUMOD')
                            ->maxLength(20)
                            ->label('STDUMOD'),
                        Forms\Components\TextInput::make('STDDGR')
                            ->maxLength(8)
                            ->label('STDDGR'),
                    ])
                    ->columns(4),
        
                Section::make('Modificacions del Contacte')
                    ->icon('heroicon-o-arrow-path') // Icono de refresco
                    ->description('Modificacions a la informació del contacte o actualitzacions.')
                    ->schema([
                        Forms\Components\TextInput::make('STDDMOD')
                            ->maxLength(8)
                            ->label('STDDMOD'),
                        Forms\Components\TextInput::make('STDHGR')
                            ->maxLength(6)
                            ->label('STDHGR'),
                        Forms\Components\TextInput::make('STDHMOD')
                            ->maxLength(6)
                            ->label('STDHMOD'),
                        Forms\Components\TextInput::make('VALDATA')
                            ->maxLength(8)
                            ->label('VALDATA'),
                    ])
                    ->columns(4),
        
                Section::make('Dades d\'estat')
                    ->icon('heroicon-o-check-circle') // Icono de estado
                    ->description('Informació sobre l\'estat i la validesa del registre.')
                    ->schema([
                        Forms\Components\TextInput::make('BAIXASW')
                            ->maxLength(1)
                            ->label('BAIXASW'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PERSCOD')
                    ->label('PERSCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('NUMORDRE')
                    ->label('NUMORDRE')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('TIPCONTACTE')
                    ->label('TIPCONTACTE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CONTACTE')
                    ->label('CONTACTE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('OBSERVACIONS')
                    ->label('OBSERVACIONS'),
                Tables\Columns\TextColumn::make('STDUGR')
                    ->label('STDUGR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDUMOD')
                    ->label('STDUMOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDDGR')
                    ->label('STDDGR'),
                Tables\Columns\TextColumn::make('STDDMOD')
                    ->label('STDDMOD'),
                Tables\Columns\TextColumn::make('STDHGR')
                    ->label('STDHGR'),
                Tables\Columns\TextColumn::make('STDHMOD')
                    ->label('STDHMOD'),
                Tables\Columns\TextColumn::make('VALDATA')
                    ->label('VALDATA'),
                Tables\Columns\TextColumn::make('BAIXASW')
                    ->label('BAIXASW'),
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
                    })//->hidden(fn ($record) => !auth()->user()->hasRole('Admin')),
                /*
                ExportAction::make()
                    ->hidden(fn ($record) => !auth()->user()->hasRole('Admin'))
                    ->exporter(TelecoExporter::class)
                    ->label('Exportar Telecos')
                    ->formats([
                        ExportFormat::Csv,
                    ]),

                ImportAction::make()
                    ->hidden(fn ($record) => !auth()->user()->hasRole('Admin'))
                    ->importer(TelecoImporter::class)
                    ->csvDelimiter(';')
                    ->label('Importar Telecos'),*/
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->hidden(fn () => !auth()->user()->hasRole('Admin'))
                    ->exporter(TelecoExporter::class)
                    ->formats([
                        ExportFormat::Csv,
                    ]),
            ]);
    }

    private static function syncOracleToMysql()
    {
        // Asegúrate de que el archivo existe antes de incluirlo
        $filePath = base_path(env('SCRIPT_TELECO'));
        
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
            'index' => Pages\ListTelecos::route('/'),
            'create' => Pages\CreateTeleco::route('/create'),
            'edit' => Pages\EditTeleco::route('/{record}/edit'),
        ];
    }
}
