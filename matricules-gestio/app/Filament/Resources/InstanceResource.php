<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstanceResource\Pages;
use App\Filament\Resources\InstanceResource\RelationManagers;
use App\Filament\Resources\InstanceResource\RelationManagers\VehiclesRelationManager;
use App\Filament\Resources\InstanceResource\RelationManagers\VehiclesInSameDwellingRelationManager;
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
use PhpOffice\PhpWord\TemplateProcessor;

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
                            ->label('DECRET FAVORABLE  / DESFAVORABLE')
                            ->options([
                                'FAVORABLE' => 'FAVORABLE',
                                'DESFAVORABLE' => 'DESFAVORABLE',
                                
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
                        ->reactive()
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
                ])->columns(2)->visibleOn('edit'),
                Section::make()
                    ->icon('heroicon-o-flag')
                    ->schema([
                        Forms\Components\Toggle::make('empadronat_si_ivtm')->label('La persona hi està empadronada i té l\'IVTM domiciliat a Banyoles ')->columnSpan(3),
                        Forms\Components\Toggle::make('empadronat_no_ivtm')->label('La persona hi està empadronada però no té l\'IVTM domiciliat a Banyoles')->columnSpan(3),
                        Forms\Components\Toggle::make('noempadronat_viu_barri_vell')
                        ->reactive()
                        ->label(function ($get) {
                            $persona = $get('noempadronat_viu_barri_vell_text');
                            $persona = $persona ? $persona : 'X';
                            return "La persona no hi està empadronada i és $persona d'un immoble al carrer del barri vell";
                        })->columnSpan(2),    
                        Forms\Components\TextInput::make('noempadronat_viu_barri_vell_text')
                            ->label('Propietari / llogater / ...')
                            ->reactive()
                            ->required(fn ($get) => $get('noempadronat_viu_barri_vell') === true)
                            ->visible(fn ($get) => $get('noempadronat_viu_barri_vell') === true),
                        Forms\Components\Toggle::make('pares_menor_edat')->label('La persona és pare o mare d\'un/a menor resident ')->columnSpan(3),
                        Forms\Components\Toggle::make('familiar_adult_major')->label('La persona és familiar d\'una persona d\'edat avançada')->columnSpan(3),
                        Forms\Components\Toggle::make('targeta_aparcament_discapacitat')->label('Persona amb targeta d\'aparcament per a persones amb discapacitat ')->columnSpan(3),
                        Forms\Components\Toggle::make('vehicle_comercial')->label('Vehicle comercial o empresa proveïdora al Barri Vell, Pl. de les Rodes o Pl. del Carme')->columnSpan(3),
                        Forms\Components\Toggle::make('client_botiga')->label('Client de botiga al Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar la botiga) ')->columnSpan(3),
                        Forms\Components\Toggle::make('empresa_serveis')->label('Empresa de serveis (neteja, aigua, llum, lampisteria,...) ')->columnSpan(3),
                        Forms\Components\Toggle::make('empresa_constructora')->label('Empresa constructora ')->columnSpan(3),
                        Forms\Components\Toggle::make('familiar_resident')->label('Persona amb familiar resident o usuari d\'una residència del Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar el mateix centre) ')->columnSpan(3),
                        Forms\Components\Toggle::make('acces_excepcional')->label('Autorització d\'accés excepcional (dins de les 48 hores abans o després) ')->columnSpan(1)->reactive(),
                        //Estan sempre visibles perquè si no altres es menja l'espai i no queda bé
                        Forms\Components\DatePicker::make('acces_excepcional_inici')
                            ->label('Data inici')
                            ->reactive()
                            ->required(fn ($get) => $get('acces_excepcional') === true)
                            ->disabled(fn ($get) => $get('acces_excepcional') !== true)
                            //->visible(fn ($get) => $get('acces_excepcional') === true)
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('acces_excepcional_fi')
                            ->label('Data fi')
                            ->reactive()
                            ->required(fn ($get) => $get('acces_excepcional') === true)
                            ->disabled(fn ($get) => $get('acces_excepcional') !== true)
                            //->visible(fn ($get) => $get('acces_excepcional') === true)
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('altres_motius')
                            ->label(function ($get){
                                $motiu = $get('altres_motius_text');
                                return "Altres: $motiu";
                            })
                            ->reactive()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('altres_motius_text')
                            ->label('Altres motius')
                            ->reactive()
                            ->required(fn ($get) => $get('altres_motius') === true)
                            ->visible(fn ($get) => $get('altres_motius') === true),
                ])->columns(3)->visibleOn('edit'),
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
                Tables\Actions\Action::make('exportDocx')
                    ->label('Exportar DOCX')
                    ->action(fn ($record) => static::exportToDocx($record))
                    ->icon('heroicon-o-arrow-down-tray')
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
            VehiclesRelationManager::class,
            VehiclesInSameDwellingRelationManager::class,
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
    public static function exportToDocx($record)
    {
       // dd($record->person->PERSNOM);
        $templatePath = storage_path('app/templates/template_decret.docx');
        $outputPath = storage_path('app/public/decret_' . $record->RESNUME . '.docx');

        // Cargar la plantilla
        $templateProcessor = new TemplateProcessor($templatePath);

        // Reemplazar valores con los datos del registro
        $templateProcessor->setValue('PERSNOM', $record->person->PERSNOM);
        $templateProcessor->setValue('PERSCOG1', $record->person->PERSCOG1);
        $templateProcessor->setValue('PERSCOG2', $record->person->PERSCOG2);
        $templateProcessor->setValue('DNI', $record->person->NIFNUM . $record->person->NIFDC);
        $templateProcessor->setValue('CARRER_HABITATGE', $record->domicili->street->nom_carrer . $record->domicili->nom_habitatge);
        $templateProcessor->setValue('REGISTRE_ENTRADA', $record->RESNUME);
        $templateProcessor->setValue('MOTIU', self::getTextMotiu($record));
        $templateProcessor->setValue('VALIDAT', $record->VALIDAT);

        $totalVehicles = $record->vehicles->count();
        if ($totalVehicles == 0) {
            $templateProcessor->setValue('MATRICULA', '');
        } else {
            $matriculas = '';
            
            for ($i = 1; $i <= $totalVehicles; $i++) {
                if ($vehicle = $record->vehicles->get($i - 1)) {
                    $matriculas .= $vehicle->MATRICULA . "\n";
                }
            }
            $templateProcessor->setValue('MATRICULA', $matriculas);
        }
        $totalCarrers = $record->carrersBarriVell->count();
        if ($totalCarrers == 0) {
            $templateProcessor->setValue('CARRER_BARRI_VELL', '');
        } else {
            $carrers = '';

            for ($i = 1; $i <= $totalCarrers; $i++) {
                if ($street=$record->carrersBarriVell->get($i-1)) {
                    $carrers .= $street->CARSIG . ' ' .$street->nom_carrer . "\n";
                }
            }
            $templateProcessor->setValue('CARRER_BARRI_VELL', $carrers);
        }
        // Guardar el documento actualizado
        $templateProcessor->saveAs($outputPath);

        // Retornar el archivo para descarga
        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
    private static function getTextMotiu($record)
    {
        $motius = [];
        if ($record->noempadronat_viu_barri_vell === true) {
            $motius[] = 'La persona no hi està empadronada i és ' . $record->noempadronat_viu_barri_vell_text .' d\'un immoble al carrer';
        }
        if ($record->pares_menor_edat === true) {
            $motius[] = 'La persona és pare o mare d\'un/a menor resident';
        }
        if ($record->familiar_adult_major === true) {
            $motius[] = 'La persona és familiar d\'una persona d\'edat avançada';
        }
        if ($record->targeta_aparcament_discapacitat === true) {
            $motius[] = 'Persona amb targeta d\'aparcament per a persones amb discapacitat';
        }
        if ($record->vehicle_comercial === true) {
            $motius[] = 'Vehicle comercial o empresa proveïdora al Barri Vell, Pl. de les Rodes o Pl. del Carme';
        }
        if ($record->client_botiga === true) {
            $motius[] = 'Client de botiga al Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar la botiga)';
        }
        if ($record->empresa_serveis === true) {
            $motius[] = 'Empresa de serveis (neteja, aigua, llum, lampisteria,...)';
        }
        if ($record->empresa_constructora === true) {
            $motius[] = 'Empresa constructora';
        }
        if ($record->familiar_resident === true) {
            $motius[] = 'Persona amb familiar resident o usuari d\'una residència del Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar el mateix centre)';
        }
        if ($record->acces_excepcional === true) {
            $motius[] = 'Autorització d\'accés excepcional (dins de les 48 hores abans o després)';
        }
        if ($record->altres_motius === true && !empty($record->altres_motius_text)) {
            $motius[] = "Altres: " . $record->altres_motius_text;
        }
        return implode(', ', $motius);
    }
}
