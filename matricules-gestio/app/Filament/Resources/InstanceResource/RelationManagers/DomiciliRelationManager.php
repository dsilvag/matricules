<?php

namespace App\Filament\Resources\InstanceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Dwelling; 
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Models\Instance;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;


class DomiciliRelationManager extends RelationManager
{
    protected static string $relationship = 'domicili';

    protected static ?string $title = 'Buscar domicili';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('DOMCOD')
            ->columns([
                Tables\Columns\TextColumn::make('DOMCOD')
                    ->label('DOMCOD')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PAISCOD')
                    ->label('PAISCOD')
                    ->numeric()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)      
                    ->sortable(),
                Tables\Columns\TextColumn::make('PROVCOD')
                    ->label('PROVCOD')
                    ->numeric()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('MUNICOD')
                    ->label('MUNICOD')
                    ->numeric()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('CARCOD')
                    ->label('CARCOD')
                    ->numeric()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('street.CARSIG')
                    ->searchable()
                    ->label('SIGLES'),
                Tables\Columns\TextColumn::make('street.CARDESC')
                    ->searchable()
                    ->label('NOM CARRER'),
                Tables\Columns\TextColumn::make('DOMNUM')
                    ->searchable()
                    ->label('DOMNUM'),
                Tables\Columns\TextColumn::make('DOMPIS')
                    ->searchable()
                    ->label('DOMPIS'),
                Tables\Columns\TextColumn::make('DOMPTA')
                    ->searchable()
                    ->label('DOMPTA'),
                Tables\Columns\TextColumn::make('DOMBLOC')
                    ->searchable()
                    ->label('DOMBLOC'),
                Tables\Columns\TextColumn::make('DOMPTAL')
                    ->searchable()
                    ->label('DOMPTAL'),
                Tables\Columns\TextColumn::make('DOMBIS')
                    ->searchable()
                    ->label('DOMBIS'),
                Tables\Columns\TextColumn::make('DOMNUM2')
                    ->searchable()
                    ->label('DOMNUM2'),
                Tables\Columns\TextColumn::make('DOMBIS2')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('DOMBIS2'),
                Tables\Columns\TextColumn::make('DOMESC')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('DOMESC'),
            ])
            ->filters([
                Filter::make('PAISCOD')
                ->label('Banyoles')
                ->query(fn ($query) => $query->where('PAISCOD', 108)->where('PROVCOD', 17)->where('MUNICOD', 15))
                ->default(true),
            ])
            ->actions(
                collect([1, 2, 3])->map(function ($num) {
                    return Action::make("assignDomicili{$num}")
                        ->label((string) $num)
                        ->icon('heroicon-m-pencil')
                        ->hidden(fn () => $this->ownerRecord->is_notificat)
                        ->action(function (Dwelling $dom) use ($num) {
                            self::assignDwelling($dom, $this->ownerRecord, $num);
                            self::assignStreet($dom,$this->ownerRecord);
                            $this->dispatch('refresh');
                        });
                })->toArray(),
                position: ActionsPosition::BeforeColumns
            )

            ->bulkActions([

            ]);
    }
    public function getTableQuery(): Builder
    {
        return Dwelling::query();
    }
    private function assignDwelling($dom, $instance,$num)
    {
        if($num==2){
            $instance->skipValidation();
            $instance->domicili_acces2 = $dom->DOMCOD;
            $instance->save();
        }elseif($num==3){
            $instance->skipValidation();
            $instance->domicili_acces3 = $dom->DOMCOD;
            $instance->save();
        }else{
            $instance->skipValidation();
            $instance->domicili_acces = $dom->DOMCOD;
            $instance->save();
        }
    }
    private function assignStreet($dom, $instance)
    {
        //Guardar la instància per si hi ha algun domicili sense guardar
        $instance->skipValidation();
        $instance->save();

        //Obtenir el codi del carrer
        $carrerCode = $dom->PAISPROVMUNICARCOD;

        // Verificar si es del barri vell
        $carrer = \App\Models\StreetBarriVell::where('PAISPROVMUNICARCOD', $carrerCode)->first();

        if ($carrer) {
            // Verificar si ja esta assignada a la instancia
            $alreadyAssigned = $instance->carrersBarriVell()
                ->where('street_barri_vells.PAISPROVMUNICARCOD', $carrerCode)
                ->exists();

            if (!$alreadyAssigned) {
                // Assignar el carrer a la instància
                $instance->carrersBarriVell()->attach($carrer->PAISPROVMUNICARCOD);
            }
        }
    }
}
