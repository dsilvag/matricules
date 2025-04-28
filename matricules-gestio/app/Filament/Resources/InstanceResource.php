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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Support\Enums\Alignment;

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
                            /*
                        Forms\Components\ToggleButtons::make('DECRETAT')
                            ->visibleOn('edit')
                            ->label('DECRETAT')
                            ->boolean()
                            ->inline(),*/
                        Forms\Components\Radio::make('VALIDAT')
                            ->visibleOn('edit')
                            ->label('DECRET FAVORABLE  / DESFAVORABLE')
                            ->options([
                                'FAVORABLE' => 'FAVORABLE',
                                'DESFAVORABLE' => 'DESFAVORABLE',
                                
                            ]),
                        ])->columns(2)->visibleOn('edit'),

                Section::make()
                    ->icon('heroicon-o-identification')
                    ->schema([    
                        Forms\Components\Select::make('PERSCOD')
                        ->visibleOn('edit')
                        ->required()
                        ->label('SOL.LICITANT')
                        ->relationship('person', 'PERSCOD') 
                        //->preload()
                        ->searchable(['PERSCOD','PERSNOM', 'PERSCOG1', 'PERSCOG2'])
                        ->getOptionLabelFromRecordUsing(fn(Person $record):string =>"{$record->nom_person}"),

                        Forms\Components\Select::make('REPRCOD')
                            ->visibleOn('edit')
                            ->label('REPRESENTANT')
                            //->description('quan calgui')
                            ->relationship('personRepresentative', 'PERSCOD')
                            //->preload()
                            ->searchable(['PERSCOD','PERSNOM', 'PERSCOG1', 'PERSCOG2'])
                            ->getOptionLabelFromRecordUsing(fn(Person $record):string =>"{$record->nom_person}"),
                    ])->columns(2)->visibleOn('edit'),
                    
                Section::make()
                    ->icon('heroicon-o-globe-europe-africa')
                    ->description('Dades contacte')
                    ->schema([  
                    Forms\Components\Select::make('DOMCOD')
                        ->visibleOn('edit')
                        ->reactive()
                        ->required()
                        ->label('CODI DOMICILI')
                        ->relationship('domicili', 'DOMCOD')
                        ->getOptionLabelFromRecordUsing(fn(Dwelling $record): string => 
                            "{$record->DOMCOD}, {$record->nom_habitatge}")
                        ->searchable(),
                ])->columns(1)->visibleOn('edit'),
                Section::make()
                    ->icon('heroicon-o-globe-europe-africa')
                    ->description('Domicili accés')
                    ->schema([  
                    Forms\Components\Select::make('domicili_acces')
                        ->visibleOn('edit')
                        ->reactive()
                        ->required()
                        ->label('CODI DOMICILI ACCÉS')
                        ->relationship('domiciliAccess', 'DOMCOD')
                        ->getOptionLabelFromRecordUsing(fn(Dwelling $record): string => 
                            "{$record->DOMCOD}, {$record->nom_habitatge}")
                        ->searchable(),
                    
                    Forms\Components\Select::make('carrersBarriVell')
                        ->visibleOn('edit')
                        ->label('CARRERS VALIDATS')
                        ->relationship('carrersBarriVell', 'CARCOD') 
                        ->preload()
                        ->searchable()
                        ->multiple()
                        ->getOptionLabelFromRecordUsing(fn(StreetBarriVell $record): string => "{$record->nom_carrer}")
                        ->options(function () {
                            $options = StreetBarriVell::all()->pluck('nom_carrer', 'CARCOD')->toArray();

                            return ['*' => 'Tots els carrers'] + $options;
                        })
                        ->afterStateUpdated(function ($state, $set) {
                            // Si el valor seleccionado es '*', seleccionamos todos los registros
                            if (in_array('*', $state)) {
                                $set('carrersBarriVell', StreetBarriVell::all()->pluck('CARCOD')->toArray());
                            }
                        }),
                ])->columns(2)->visibleOn('edit'),
                 Section::make()
                    ->icon('heroicon-o-flag')
                    ->description('Selecciona un motiu')
                    ->schema([
                        /**
                         * Per a cada Toggle hi ha una data per defecte(és el dia d'avui), i depenent de si l'usuari selecciona una opció o una altra, es permet modificar les dates o no.
                         */
                        Forms\Components\Toggle::make('empadronat_si_ivtm')->label('La persona hi està empadronada i té l\'IVTM domiciliat a Banyoles (indefinit)')->columnSpan(3)->reactive() 
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                                $set('data_fi', '9999-12-31');
                            }
                        }),
                        Forms\Components\Toggle::make('empadronat_no_ivtm')->label('La persona hi està empadronada però no té l\'IVTM domiciliat a Banyoles (2 anys)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                                $set('data_fi', now()->addYears(2)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('noempadronat_viu_barri_vell')
                        ->reactive()
                        ->label(function ($get) {
                            $persona = $get('noempadronat_viu_barri_vell_text');
                            $persona = $persona ? $persona : 'X';
                            return "La persona no hi està empadronada i és $persona d'un immoble al carrer del barri vell (2 o 4 anys)";
                        })
                        ->columnSpan(2),    
                        Forms\Components\Select::make('noempadronat_viu_barri_vell_text')
                            ->label('Propietari / Llogater')
                            ->options([
                                'propietari' => 'Propietari',
                                'llogater' => 'Llogater',
                            ])
                            ->afterStateUpdated(function ($set, $state, $get) {
                                if ($get('noempadronat_viu_barri_vell') === true) {
                                    $persona = $state; 
                                    $set('data_inici', now()->format('Y-m-d')); 
                                    if ($persona == 'propietari') {
                                        $set('data_fi', now()->addYears(4)->format('Y-m-d'));
                                    } else if ($persona == 'llogater') {
                                        $set('data_fi', now()->addYears(2)->format('Y-m-d')); 
                                    }
                                }})
                            ->reactive()
                            ->required(fn ($get) => $get('noempadronat_viu_barri_vell') === true)
                            ->visible(fn ($get) => $get('noempadronat_viu_barri_vell') === true),
                        Forms\Components\Toggle::make('pares_menor_edat')->label('La persona és pare o mare d\'un/a menor resident (4 anys)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                                $set('data_fi', now()->addYears(4)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('familiar_adult_major')->label('La persona és familiar d\'una persona d\'edat avançada (4 anys)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                                $set('data_fi', now()->addYears(4)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('targeta_aparcament_discapacitat')->label('Persona amb targeta d\'aparcament per a persones amb discapacitat (igual que la targeta)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('vehicle_comercial')->label('Vehicle comercial o empresa proveïdora al Barri Vell, Pl. de les Rodes o Pl. del Carme')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('client_botiga')->label('Client de botiga al Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar la botiga) ')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('empresa_serveis')->label('Empresa de serveis (neteja, aigua, llum, lampisteria,...) ')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('empresa_constructora')->label('Empresa constructora ')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('familiar_resident')->label('Persona amb familiar resident o usuari d\'una residència del Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar el mateix centre) (4 anys)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                                $set('data_fi', now()->addYears(4)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('acces_excepcional')->label('Autorització d\'accés excepcional (dins de les 48 hores abans o després) ')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                $set('data_inici', now()->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('altres_motius')
                            ->label(function ($get){
                                $motiu = $get('altres_motius_text');
                                return "Altres: $motiu";
                            })
                            ->afterStateUpdated(function ($set, $state, $get) {
                                if ($state) {
                                    $set('data_inici', now()->format('Y-m-d'));
                                }
                            })
                            ->reactive()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('altres_motius_text')
                            ->label('Altres motius')
                            ->reactive()
                            ->required(fn ($get) => $get('altres_motius') === true)
                            ->visible(fn ($get) => $get('altres_motius') === true),

                        Section::make()
                            ->schema([
                                Forms\Components\DatePicker::make('data_inici')
                                    ->label('Data inici')
                                    ->reactive()
                                    ->required()
                                    ->readOnly(fn ($get) => !(
                                        $get('targeta_aparcament_discapacitat') || 
                                        $get('vehicle_comercial') || 
                                        $get('client_botiga') || 
                                        $get('empresa_serveis') || 
                                        $get('empresa_constructora') || 
                                        $get('acces_excepcional') || 
                                        $get('altres_motius')
                                    ))
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('data_fi')
                                    ->label('Data fi')
                                    ->reactive()
                                    ->required()
                                    ->readOnly(fn ($get) => !(
                                        $get('targeta_aparcament_discapacitat') || 
                                        $get('vehicle_comercial') || 
                                        $get('client_botiga') || 
                                        $get('empresa_serveis') || 
                                        $get('empresa_constructora') || 
                                        $get('acces_excepcional') || 
                                        $get('altres_motius')
                                    ))
                                    ->columnSpan(1),
                            ])->columns(2)->visibleOn('edit'),
                ])->columns(3)->visibleOn('edit'),                
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('RESNUME')
                    ->label('Entrada')
                    ->sortable()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('NUMEXP')
                    ->label('Expedient')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('person.nom_person')
                    ->label('Persona')
                    ->extraAttributes([
                        'style' => 'word-wrap: break-word; word-break: normal; white-space: normal; width: 200px;',
                    ]),
                Tables\Columns\TextColumn::make('vehicles.MATRICULA')
                        ->label('Matrícules')
                        ->html() // Permite contenido HTML
                        ->formatStateUsing(fn ($state) => str_replace(',', ',<br>', $state))
                        ->extraAttributes([
                            'style' => 'word-wrap: break-word; word-break: normal; white-space: normal; width: 200px;',
                        ]),
                    
                    //->searchable(isIndividual: true),
                /*Tables\Columns\TextColumn::make('personRepresentative.nom_person')
                    ->label('REPRESENTANT')
                    ->extraAttributes([
                        'style' => 'word-wrap: break-word; word-break: normal; white-space: normal;',
                    ]),
                    //->searchable(isIndividual: true),
                /*Tables\Columns\TextColumn::make('carrersBarriVell.street.CARSIG')
                    ->label('CARSIG')
                    ->sortable()
                    ->searchable(isIndividual: true),*/
                /*Tables\Columns\TextColumn::make('carrersBarriVell.street.CARDESC')
                    ->label('CARRERS VALIDATS')
                    ->extraAttributes([
                        'style' => 'word-wrap: break-word; word-break: normal; white-space: normal;',
                    ])
                    ->searchable(isIndividual: true),*/
                    Tables\Columns\TextColumn::make('VALIDAT')
                        ->label('Decret')
                        ->searchable()
                        ->searchable(isIndividual: true),
                    Tables\Columns\IconColumn::make('DECRETAT')
                        ->label('Decretat')
                        ->sortable()
                        ->alignment(Alignment::Center)
                        ->boolean(),
                Tables\Columns\IconColumn::make('is_notificat')
                        ->label('Notificat')
                        ->boolean()
                        ->alignment(Alignment::Center)
                        ->sortable(),
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
                TernaryFilter::make('DECRETAT')->label('Decretat'),
                TernaryFilter::make('is_notificat')->label('Notificat')->default(false),
            ])
            ->actions([
                //Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('notificar')
                    ->label('Notificar')
                    ->action(fn ($record) => Instance::notifyInstance($record))
                    ->icon('heroicon-o-bell-alert'),
                Tables\Actions\Action::make('sendToWs') 
                    ->label('Penjar decret')
                    ->action(fn ($record) => Instance::sendToWs($record))
                    ->icon('heroicon-o-arrow-up-circle'),
                Tables\Actions\Action::make('exportDocx')
                    ->label('Exportar DOCX')
                    ->action(fn ($record) => static::downloadDocx($record))
                    ->icon('heroicon-o-arrow-down-tray'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                /*->action(function (Collection $records) {
                    dd($records); // This should now show only selected records
                    foreach ($records as $record) {
                        $record->delete();
                    }
                })
                ->requiresConfirmation(),*/
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

    public static function downloadDocx ($record)
    {
        $outputPath = storage_path('app/public/decret_' . $record->RESNUME . '.docx');
        $templateProcessor = static::exportToDocx($record);
        
        // Guardar el documento actualizado
        $templateProcessor->saveAs($outputPath);

        // Retornar el archivo para descarga
        return response()->download($outputPath)->deleteFileAfterSend(true);
    }

    public static function exportBase64($record)
    {
        $outputPath = storage_path('app/public/decret_' . $record->RESNUME . '.docx');
        $templateProcessor = static::exportToDocx($record);
        // Guardar el documento actualizado
        $templateProcessor->saveAs($outputPath);
        $fileContent = file_get_contents($outputPath);
        // Convierte el contenido del archivo a base64
        return base64_encode($fileContent);
    }

    public static function exportToDocx($record)
    {
       //dd(trim($record->domicili->nom_habitatge));
        if($record->VALIDAT!=null && $record->VALIDAT == 'FAVORABLE'){
            $templatePath = storage_path('app/templates/MODEL RESOLUCIO CAMERES.docx');
        }else{
            $templatePath = storage_path('app/templates/MODEL RESOLUCIÓ DESESTIMACIÓ.docx');
        }

        // Cargar la plantilla
        $templateProcessor = new TemplateProcessor($templatePath);

        // Reemplazar valores con los datos del registro
        $templateProcessor->setValue('PERSNOM', $record->person->PERSNOM);
        $templateProcessor->setValue('PERSCOG1', $record->person->PERSCOG1);
        $templateProcessor->setValue('PERSCOG2', $record->person->PERSCOG2);
        $templateProcessor->setValue('DNI', $record->person->NIFNUM . $record->person->NIFDC);
        $templateProcessor->setValue('CARRER_HABITATGE', trim($record->domiciliAccess->nom_habitatge));
        $templateProcessor->setValue('REGISTRE_ENTRADA', $record->RESNUME);
        $templateProcessor->setValue('MOTIU', self::getTextMotiu($record));
        //format data
        $dataOriginal = $record->data_presentacio;
        $dataFormat = substr($dataOriginal, 6, 2) . '/' . substr($dataOriginal, 4, 2) . '/' . substr($dataOriginal, 0, 4);
        $templateProcessor->setValue('DATA_PRESENTACIO', $dataFormat);
        //$templateProcessor->setValue('VALIDAT', $record->VALIDAT);
        $templateProcessor->setValue('DATAFI', $record->data_fi);
        $templateProcessor->setValue('DATAINICI', $record->data_inici);
        $templateProcessor->setValue('AVUI', date('d/m/Y'));

        $totalVehicles = $record->vehicles->count();
        if ($totalVehicles == 0) {
            $templateProcessor->setValue('MATRICULA', '');
        } else {
            $matriculas = '';
            
            for ($i = 1; $i <= $totalVehicles; $i++) {
                if ($vehicle = $record->vehicles->get($i - 1)) {
                    $matriculas .= $vehicle->MATRICULA . ", ";
                }
            }
            $matriculas = rtrim($matriculas, ", ");
            $templateProcessor->setValue('MATRICULA', $matriculas);
        }
        $totalCarrers = $record->carrersBarriVell->count();
        if ($totalCarrers == 0) {
            $templateProcessor->setValue('CARRER_BARRI_VELL', '');
        } else {
            $carrers = '';

            for ($i = 1; $i <= $totalCarrers; $i++) {
                if ($street=$record->carrersBarriVell->get($i-1)) {
                    $carrers .= $street->CARSIG . ' ' .$street->nom_carrer . ", ";
                }
            }
            $carrers = rtrim($carrers, ", ");
            $templateProcessor->setValue('CARRER_BARRI_VELL', $carrers);
        }
        return $templateProcessor;
    }
    private static function getTextMotiu($record)
    {
        $motius = '';

        if ($record->empadronat_si_ivtm == true) {
            $motius .= 'La persona hi està empadronada i té l\'IVTM domiciliat a Banyoles'."\n";
        }
        if ($record->empadronat_no_ivtm == true) {
            $motius .= 'La persona hi està empadronada però no té l\'IVTM domiciliat a Banyoles'."\n";
        }
        if ($record->noempadronat_viu_barri_vell == true) {
            $motius .= 'La persona no hi està empadronada i és ' . $record->noempadronat_viu_barri_vell_text .' d\'un immoble al carrer'."\n";
        }
        if ($record->pares_menor_edat == true) {
            $motius .= 'La persona és pare o mare d\'un/a menor resident'."\n";
        }
        if ($record->familiar_adult_major == true) {
            $motius .= 'La persona és familiar d\'una persona d\'edat avançada'."\n";
        }
        if ($record->targeta_aparcament_discapacitat == true) {
            $motius .= 'Persona amb targeta d\'aparcament per a persones amb discapacitat'."\n";
        }
        if ($record->vehicle_comercial == true) {
            $motius .= 'Vehicle comercial o empresa proveïdora al Barri Vell, Pl. de les Rodes o Pl. del Carme'."\n";
        }
        if ($record->client_botiga == true) {
            $motius .= 'Client de botiga al Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar la botiga)'."\n";
        }
        if ($record->empresa_serveis == true) {
            $motius .= 'Empresa de serveis (neteja, aigua, llum, lampisteria,...)'."\n";
        }
        if ($record->empresa_constructora == true) {
            $motius .= 'Empresa constructora'."\n";
        }
        if ($record->familiar_resident == true) {
            $motius .= 'Persona amb familiar resident o usuari d\'una residència del Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar el mateix centre)'."\n";
        }
        if ($record->acces_excepcional == true) {
            $motius .= 'Autorització d\'accés excepcional (dins de les 48 hores abans o després)'."\n";
        }
        if ($record->altres_motius == true && !empty($record->altres_motius_text)) {
            $motius .= "Altres: " . $record->altres_motius_text."\n";
        }
        return $motius = rtrim($motius, "\n");
    }
}
