<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
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
use App\Filament\Exports\PersonExporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use App\Filament\Imports\PersonImporter;
use Filament\Tables\Actions\ImportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;


class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $pluralModelLabel = 'Persones';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Informació Personal
                Section::make('Informació Personal')
                    ->icon('heroicon-o-user')
                    ->description('Dades personals de la persona.')
                    ->schema([
                        Forms\Components\TextInput::make('PERSCOD')->required()->numeric()->label('PERSCOD'),
                        Forms\Components\TextInput::make('PAISCOD')->numeric()->label('PAISCOD'),
                        Forms\Components\TextInput::make('PROVCOD')->numeric()->label('PROVCOD'),
                        Forms\Components\TextInput::make('MUNICOD')->numeric()->label('MUNICOD'),
                        Forms\Components\TextInput::make('PERSNOM')->required()->maxLength(255)->label('PERSNOM'),
                        Forms\Components\TextInput::make('PERSCOG1')->maxLength(25)->label('PERSCOG1'),
                        Forms\Components\TextInput::make('PERSCOG2')->maxLength(25)->label('PERSCOG2'),
                        Forms\Components\TextInput::make('PERSPAR1')->maxLength(6)->label('PERSPAR1'),
                        Forms\Components\TextInput::make('PERSPAR2')->maxLength(6)->label('PERSPAR2'),
                        Forms\Components\TextInput::make('NIFNUMP')->maxLength(10)->label('NIFNUMP'),
                        Forms\Components\TextInput::make('NIFNUM')->maxLength(8)->label('NIFNUM'),
                        Forms\Components\TextInput::make('NIFDC')->maxLength(1)->label('NIFDC'),
                        Forms\Components\TextInput::make('NIFSW')->required()->maxLength(1)->label('NIFSW'),
                        Forms\Components\TextInput::make('PERSDCONNIF')->maxLength(8)->label('PERSDCONNIF'),
                        Forms\Components\TextInput::make('PERSDCANNIF')->maxLength(8)->label('PERSDCANNIF'),
                        Forms\Components\TextInput::make('PERSNACIONA')->numeric()->label('PERSNACIONA'),
                        Forms\Components\TextInput::make('PERSPASSPORT')->maxLength(20)->label('PERSPASSPORT'),
                        Forms\Components\TextInput::make('PERSNDATA')->maxLength(8)->label('PERSNDATA'),
                        Forms\Components\TextInput::make('PERSPARE')->maxLength(20)->label('PERSPARE'),
                        Forms\Components\TextInput::make('PERSMARE')->maxLength(20)->label('PERSMARE'),
                        Forms\Components\TextInput::make('PERSSEXE')->maxLength(1)->label('PERSSEXE'),
                        Forms\Components\TextInput::make('PERSSW')->maxLength(1)->label('PERSSW'),
                        Forms\Components\TextInput::make('IDIOCOD')->maxLength(1)->label('IDIOCOD'),
                        Forms\Components\TextInput::make('PERSVNUM')->numeric()->label('PERSVNUM'),
                    ])
                    ->columnSpan(2)
                    ->columns(4),

                // Temps i Modificacions
                Section::make('Temps i Modificacions')
                    ->icon('heroicon-o-clock')
                    ->description('Dades relacionades amb les modificacions temporals.')
                    ->schema([
                        Forms\Components\TextInput::make('STDAPLADD')->maxLength(5)->label('STDAPLADD'),
                        Forms\Components\TextInput::make('STDAPLMOD')->maxLength(5)->label('STDAPLMOD'),
                        Forms\Components\TextInput::make('STDUGR')->maxLength(20)->label('STDUGR'),
                        Forms\Components\TextInput::make('STDUMOD')->maxLength(20)->label('STDUMOD'),
                        Forms\Components\TextInput::make('STDDGR')->maxLength(8)->label('STDDGR'),
                        Forms\Components\TextInput::make('STDDMOD')->maxLength(8)->label('STDDMOD'),
                        Forms\Components\TextInput::make('STDHGR')->maxLength(6)->label('STDHGR'),
                        Forms\Components\TextInput::make('STDHMOD')->maxLength(6)->label('STDHMOD'),
                    ])
                    ->columnSpan(2)
                    ->columns(2),

                // Estat i Validació
                Section::make('Estat i Validació')
                    ->icon('heroicon-o-check-circle')
                    ->description('Dades relacionades amb l\'estat i validació del registre.')
                    ->schema([
                        Forms\Components\TextInput::make('VALDATA')->maxLength(8)->label('VALDATA'),
                        Forms\Components\TextInput::make('BAIXASW')->maxLength(1)->label('BAIXASW'),
                    ])
                    ->columnSpan(1)
                    ->columns(2),

                // Informació Addicional
                Section::make('Informació Addicional')
                    ->icon('heroicon-o-clipboard')
                    ->description('Informació addicional relacionada amb el registre.')
                    ->schema([
                        Forms\Components\TextInput::make('GUID')->maxLength(32)->label('GUID'),
                        Forms\Components\TextInput::make('CONTVNUM')->numeric()->label('CONTVNUM'),
                        Forms\Components\TextInput::make('NIFORIG')->maxLength(10)->label('NIFORIG'),
                        Forms\Components\TextInput::make('PERSCODOLD')->maxLength(30)->label('PERSCODOLD'),
                    ])
                    ->columnSpan(1)
                    ->columns(4),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PERSCOD')
                    ->label('PERSCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PAISCOD')
                    ->label('PAISCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PROVCOD')
                    ->label('PROVCOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('MUNICOD')
                    ->label('MUNICOD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PERSNOM')
                    ->label('PERSNOM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSCOG1')
                    ->label('PERSCOG1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSCOG2')
                    ->label('PERSCOG2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSPAR1')
                    ->label('PERSPAR1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSPAR2')
                    ->label('PERSPAR2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('NIFNUMP')
                    ->label('NIFNUMP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('NIFNUM')
                    ->label('NIFNUM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('NIFDC')
                    ->label('NIFDC')
                    ->searchable(),
                Tables\Columns\TextColumn::make('NIFSW')
                    ->label('NIFSW')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSDCONNIF')
                    ->label('PERSDCONNIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSDCANNIF')
                    ->label('PERSDCANNIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSNACIONA')
                    ->label('PERSNACIONA')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PERSPASSPORT')
                    ->label('PERSPASSPORT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSNDATA')
                    ->label('PERSNDATA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSPARE')
                    ->label('PERSPARE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSMARE')
                    ->label('PERSMARE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSSEXE')
                    ->label('PERSSEXE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSSW')
                    ->label('PERSSW')
                    ->searchable(),
                Tables\Columns\TextColumn::make('IDIOCOD')
                    ->label('IDIOCOD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSVNUM')
                    ->label('PERSVNUM')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('STDAPLADD')
                    ->label('STDAPLADD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('STDAPLMOD')
                    ->label('STDAPLMOD')
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
                Tables\Columns\TextColumn::make('CONTVNUM')
                    ->label('CONTVNUM')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('NIFORIG')
                    ->label('NIFORIG')
                    ->searchable(),
                Tables\Columns\TextColumn::make('PERSCODOLD')
                    ->label('PERSCODOLD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('VALDATA')
                    ->label('VALDATA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('BAIXASW')
                    ->label('BAIXASW')
                    ->searchable(),
                Tables\Columns\TextColumn::make('GUID')
                    ->label('GUID')
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
                    ->hidden(fn ($record) => !auth()->user()->hasRole('Admin'))
                    ->exporter(PersonExporter::class)
                    ->label('Exportar persones')
                    ->formats([
                        ExportFormat::Csv,
                    ]),

                ImportAction::make()
                    ->hidden(fn ($record) => !auth()->user()->hasRole('Admin'))
                    ->importer(PersonImporter::class)
                    ->csvDelimiter(';')
                    ->label('Importar persones'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->hidden(fn () => !auth()->user()->hasRole('Admin'))
                    ->exporter(PersonExporter::class)
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
