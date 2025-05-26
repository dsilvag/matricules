<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StreetBarriVellResource\Pages;
use App\Filament\Resources\StreetBarriVellResource\RelationManagers;
use App\Models\StreetBarriVell;
use App\Models\Street;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use App\Models\Camera;

class StreetBarriVellResource extends Resource
{
    protected static ?string $model = StreetBarriVell::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $pluralModelLabel = 'Carrers barri vell';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('PAISPROVMUNICARCOD')
                    ->label('Carrer')
                    ->relationship('street', 'CARDESC')
                    //->preload()
                    ->lazy()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(
                        fn (Street $record): string => "{$record->PAISPROVMUNICARCOD}, {$record->nom_carrer}"
                    ),                    
                Forms\Components\Toggle::make('isCamera')
                    ->label('És un carrer càmera?')
                    ->inline(false),
                Select::make('coveringCameras')
                    ->label('Càmeres validades')
                    ->relationship('coveringCameras', 'owner_CARCOD')
                    ->lazy()
                    ->preload()
                    ->searchable()
                    ->multiple()
                    ->getOptionLabelFromRecordUsing(fn(Camera $record): string => "{$record->ownerStreet->street->nom_carrer}"),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('street.PAISCOD')
                ->label('PAISCOD')
                ->numeric()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('street.PROVCOD')
                ->label('PROVCOD')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('street.MUNICOD')
                ->label('MUNICOD')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('street.CARCOD')
                ->label('CARCOD')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('street.CARSIG')
                ->label('CARSIG')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARPAR')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('CARPAR')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARDESC')
                ->label('CARDESC')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('street.CARDESC2')
                ->label('CARDESC2')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
           /* Tables\Columns\TextColumn::make('user')
                ->label('USER')
                ->copyable()
                ->copyMessage('Copiado al portapapeles')
                ->copyMessageDuration(1500),*/
            Tables\Columns\TextColumn::make('coveringCameras')
                ->label('CÀMERES VALIDADES')
                ->getStateUsing(function (StreetBarriVell $record) {
                    $coveredStreetNames = $record->coveringCameras->map(function ($camera) {
                        return $camera->ownerStreet->nom_carrer ?? 'Sin nombre';
                    });
                    return $coveredStreetNames->implode(', ');
                }),
            Tables\Columns\TextColumn::make('street.STDUGR')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('STDUGR')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDUMOD')
                ->toggleable(isToggledHiddenByDefault: true)    
                ->label('STDUMOD')
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDDGR')
                ->label('STDDGR')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDDMOD')
                ->label('STDDMOD')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDHGR')
                ->label('STDHGR')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.STDHMOD')
                ->label('STDHMOD')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.VALDATA')
                ->label('VALDATA')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.BAIXASW')
                ->label('BAIXASW')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.INICIFI')
                ->label('INICIFI')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.OBSERVACIONS')
                ->label('OBSERVACIONS')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGCOD')
                ->label('ORGCOD')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGDATA')
                ->label('ORGDATA')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ORGOBS')
                ->label('ORGOBS')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.PLACA')
                ->label('PLACA')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.GENERIC')
                ->label('GENERIC')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.ESPECIFIC')
                ->label('ESPECIFIC')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.TEMATICA')
                ->label('TEMATICA')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.SEXE')
                ->label('SEXE')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.LOCAL')
                ->label('LOCAL')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('street.created_at')
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
           ->headerActions([
                /*Tables\Actions\Action::make('penjar_tots')
                    ->label('Penjar vehicles')
                    ->color('success')
                    ->action(fn () => StreetBarriVell::penjarVehicles())
                    ->icon('heroicon-o-bolt'),*/
                Tables\Actions\Action::make('penjar_instancia')
                    ->label('Penjar vehicles')
                    ->color('success')
                    ->action(fn () => StreetBarriVell::penjarVehiclesInstancies())
                    ->icon('heroicon-o-bolt'),
                Tables\Actions\Action::make('penjar_padro')
                    ->label('Penjar vehicles padro')
                    ->color('success')
                    ->hidden(fn () => !auth()->user()->hasRole('Admin'))
                    ->action(fn () => StreetBarriVell::penjarVehiclesPadro())
                    ->icon('heroicon-o-bolt'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('obtenirLListaCotxes') 
                    ->label('Validar llista de cotxes')
                    ->action(fn ($record) => StreetBarriVell::obtenirLListaCotxes($record, true,false))
                    ->icon('heroicon-o-arrow-up-circle'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStreetBarriVells::route('/'),
            'create' => Pages\CreateStreetBarriVell::route('/create'),
            'edit' => Pages\EditStreetBarriVell::route('/{record}/edit'),
        ];
    }
}
