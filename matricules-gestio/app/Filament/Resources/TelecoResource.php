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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('PERSCOD')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('NUMORDRE')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('TIPCONTACTE')
                    ->maxLength(4),
                Forms\Components\TextInput::make('CONTACTE')
                    ->maxLength(255),
                Forms\Components\TextInput::make('OBSERVACIONS')
                    ->maxLength(255),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PERSCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('NUMORDRE')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('TIPCONTACTE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CONTACTE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('OBSERVACIONS')
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
                    ->exporter(TelecoExporter::class)
                    ->label('Export Telecos')
                    ->formats([
                        ExportFormat::Csv,
                    ]),

                ImportAction::make()
                    ->importer(TelecoImporter::class)
                    ->csvDelimiter(';')
                    ->label('Import Telecos'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(TelecoExporter::class)
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
            'index' => Pages\ListTelecos::route('/'),
            'create' => Pages\CreateTeleco::route('/create'),
            'edit' => Pages\EditTeleco::route('/{record}/edit'),
        ];
    }
}
