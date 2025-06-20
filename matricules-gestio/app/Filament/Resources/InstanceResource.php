<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstanceResource\Pages;
use App\Filament\Resources\InstanceResource\RelationManagers;
use App\Filament\Resources\InstanceResource\RelationManagers\VehiclesRelationManager;
use App\Filament\Resources\InstanceResource\RelationManagers\VehiclesInSameDwellingRelationManager;
use App\Filament\Resources\InstanceResource\RelationManagers\DomiciliRelationManager;
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
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Actions\Action;
use App\Livewire\Dwellings\ListDwellings;
use \DateTime;
use Filament\Forms\Set;
use Carbon\Carbon;
use Filament\Tables\Filters\Filter;

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
                        ->searchable(['PERSCOD','PERSNOM', 'PERSCOG1', 'PERSCOG2'])
                        ->lazy()
                        ->getOptionLabelFromRecordUsing(fn(Person $record):string =>"{$record->nom_person}"),

                        Forms\Components\Select::make('REPRCOD')
                            ->visibleOn('edit')
                            ->label('REPRESENTANT')
                            //->description('quan calgui')
                            ->relationship('personRepresentative', 'PERSCOD')
                            ->lazy()
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
                        ->label('CODI DOMICILI INSTÀNCIA')
                        ->relationship('domicili', 'DOMCOD')
                        ->lazy()
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
                        //->suffixIcon('heroicon-m-magnifying-glass')
                        ->reactive()
                        ->required()
                        ->label('CODI DOMICILI VINCULAT')
                        ->relationship('domiciliAccess', 'DOMCOD')
                        ->lazy()
                        ->searchable()
                        ->getOptionLabelFromRecordUsing(fn(Dwelling $record): string => 
                            "{$record->DOMCOD}, {$record->nom_habitatge}")
                        ->afterStateUpdated(function ($state, $set, $livewire) {
                            $instance = $livewire->record;
                            // Buscar el domicilio seleccionado por el usuario
                            $dom = \App\Models\Dwelling::where('DOMCOD', $state)->first();
                            if ($instance && $dom) {
                                $instance->assignDomicili($dom,1);
                                $livewire->dispatch('refresh');
                            }else {
                                //si treu el codi dom vinculat s'ha de guardar
                                $livewire->dispatch('save');
                            }
                        }),
                            
                    Forms\Components\Select::make('carrersBarriVell')
                        ->visibleOn('edit')
                        ->label('CARRER DESTÍ')
                        ->relationship('carrersBarriVell', 'PAISPROVMUNICARCOD') 
                        ->preload()
                        ->lazy()
                        ->searchable()
                        ->multiple()
                        ->getOptionLabelFromRecordUsing(fn(StreetBarriVell $record): string => "{$record->nom_carrer}")
                        ->options(function () {
                            $options = StreetBarriVell::all()->pluck('nom_carrer', 'PAISPROVMUNICARCOD')->toArray();

                            return ['*' => 'Tots els carrers'] + $options;
                        })
                        ->afterStateUpdated(function ($state, $set,$livewire) {
                            // Si el valor seleccionado es '*', seleccionamos todos los registros
                            if (in_array('*', $state)) {
                                $set('carrersBarriVell', StreetBarriVell::all()->pluck('PAISPROVMUNICARCOD')->toArray());
                            } else {
                                $set('carrersBarriVell', $state); // Ensure we keep only selected values
                            }
                            $livewire->dispatch('save');
                        }),
                        Forms\Components\Select::make('domicili_acces2')
                            ->visibleOn('edit')
                            ->reactive()
                            ->label('CODI DOMICILI VINCULAT 2')
                            ->relationship('domiciliAccess2', 'DOMCOD')
                            ->lazy()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn(Dwelling $record): string => 
                                "{$record->DOMCOD}, {$record->nom_habitatge}")
                            ->afterStateUpdated(function ($state, $set, $livewire) {
                                $instance = $livewire->record;
                                // Buscar el domicilio seleccionado por el usuario
                                $dom = \App\Models\Dwelling::where('DOMCOD', $state)->first();
                                if ($instance && $dom) {
                                    $instance->assignDomicili($dom,2);
                                    $livewire->dispatch('refresh');
                                }else {
                                    //si treu el codi dom vinculat s'ha de guardar
                                    $livewire->dispatch('save');
                                }
                            }),
                        Forms\Components\Placeholder::make('spacer')->label(''),//Afegim un espai en blanc perquè tots els domicilis ocupin el mateix espai i quedin alineats en la mateixa columna 
                        Forms\Components\Select::make('domicili_acces3')
                            ->visibleOn('edit')
                            ->reactive()
                            ->label('CODI DOMICILI VINCULAT 3')
                            ->relationship('domiciliAccess3', 'DOMCOD')
                            ->lazy()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn(Dwelling $record): string => 
                                "{$record->DOMCOD}, {$record->nom_habitatge}")
                            ->afterStateUpdated(function ($state, $set, $livewire) {
                                $instance = $livewire->record;
                                // Buscar el domicilio seleccionado por el usuario
                                $dom = \App\Models\Dwelling::where('DOMCOD', $state)->first();
                                if ($instance && $dom) {
                                    $instance->assignDomicili($dom,3);
                                    $livewire->dispatch('refresh');
                                }else {
                                    //si treu el codi dom vinculat s'ha de guardar
                                    $livewire->dispatch('save');
                                }
                            }),
                        Forms\Components\Placeholder::make('spacer')->label(''), 
                        Forms\Components\Placeholder::make('scroll_button')
                            ->label('')
                            ->visibleOn('edit')
                            ->content(function () {
                            return new HtmlString(
                                '
                                <div style="display: flex; align-items: center; font-size: 12px; margin-bottom: 8px;">
                                    <span style="margin-right: 10px;">
                                        En cas que el domicili vinculat no coincideix amb el de l\'entrada, cercar-lo:
                                    </span>
                                    <button 
                                        onclick="scrollToBottom(event)" 
                                        class="btn" 
                                        style="background-color:rgb(224, 134, 17); color: white; border-radius: 50px; padding: 10px 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); cursor: pointer; display: flex; align-items: center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 15px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                                        </svg>
                                    </button>
                                </div>
                                <script>
                                    function scrollToBottom(event) {
                                        event.preventDefault();
                                        window.scrollTo(0, document.body.scrollHeight);
                                    }
                                </script>'
                            );
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
                            //Variables data inici data fi
                            $today = now()->format('Y-m-d');
                            $dataInici = ($today > "2025-07-01") ? $today : "2025-07-01";
                            if ($state) {
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                                $set('data_fi', '9999-12-31');
                            }
                        }),
                        Forms\Components\Toggle::make('empadronat_no_ivtm')->label('La persona hi està empadronada però no té l\'IVTM domiciliat a Banyoles (2 anys)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            //Variables data inici data fi
                            $today = now()->format('Y-m-d');
                            $dataInici = ($today > "2025-07-01") ? $today : "2025-07-01";
                            if ($state) {
                                $set('data_inici',Carbon::parse($dataInici)->format('Y-m-d'));
                                $set('data_fi', Carbon::parse($dataInici)->addYears(2)->format('Y-m-d'));
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
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                if ($get('noempadronat_viu_barri_vell') === true) {
                                    $persona = $state; 
                                    $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d')); 
                                    if ($persona == 'propietari') {
                                        $set('data_fi', Carbon::parse($dataInici)->addYears(4)->format('Y-m-d'));
                                    } else if ($persona == 'llogater') {
                                        $set('data_fi', Carbon::parse($dataInici)->addYears(2)->format('Y-m-d')); 
                                    }
                                }})
                            ->reactive()
                            ->required(fn ($get) => $get('noempadronat_viu_barri_vell') === true)
                            ->visible(fn ($get) => $get('noempadronat_viu_barri_vell') === true),
                        Forms\Components\Toggle::make('pares_menor_edat')->label('La persona és pare o mare d\'un/a menor resident (4 anys)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > "2025-07-01") ? $today : "2025-07-01";
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                                $set('data_fi', Carbon::parse($dataInici)->addYears(4)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('familiar_adult_major')->label('La persona és familiar d\'una persona d\'edat avançada (4 anys)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                                $set('data_fi', Carbon::parse($dataInici)->addYears(4)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('targeta_aparcament_discapacitat')->label('Persona amb targeta d\'aparcament per a persones amb discapacitat (igual que la targeta)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('vehicle_comercial')->label('Vehicle comercial o empresa proveïdora al Barri Vell, Pl. de les Rodes o Pl. del Carme')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('client_botiga')->label('Client de botiga al Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar la botiga) ')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('empresa_serveis')->label('Empresa de serveis (neteja, aigua, llum, lampisteria,...) ')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('empresa_constructora')->label('Empresa constructora ')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('familiar_resident')->label('Persona amb familiar resident o usuari d\'una residència del Barri Vell, Pl. de les Rodes o Pl. del Carme (ho ha de sol·licitar el mateix centre) (4 anys)')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                $set('data_inici', Carbon::parse($dataInici)->format('Y-m-d'));
                                $set('data_fi', Carbon::parse($dataInici)->addYears(4)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('acces_excepcional')->label('Autorització d\'accés excepcional (dins de les 48 hores abans o després) ')->columnSpan(3)->reactive()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                $set('data_inici',Carbon::parse($dataInici)->format('Y-m-d'));
                            }
                        }),
                        Forms\Components\Toggle::make('altres_motius')
                            ->label(function ($get){
                                $motiu = $get('altres_motius_text');
                                return "Altres: $motiu";
                            })
                            ->afterStateUpdated(function ($set, $state, $get) {
                                if ($state) {
                                //Variables data inici data fi
                                $today = now()->format('Y-m-d');
                                $dataInici = ($today > '2025-07-01') ? $today : '2025-07-01';
                                    $set('data_inici',Carbon::parse($dataInici)->format('Y-m-d'));
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('NUMEXP')
                    ->label('Expedient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('person.nom_person')
                    ->label('Persona')
                    ->extraAttributes([
                        'style' => 'word-wrap: break-word; word-break: normal; white-space: normal; width: 131px;',
                    ]),
                Tables\Columns\TextColumn::make('vehicles.MATRICULA')
                        ->label('Matrícules')
                        ->html()
                        ->formatStateUsing(fn ($state) => str_replace(',', ',<br>', $state)),
                        /*->extraAttributes([
                            'style' => 'word-wrap: break-word; word-break: break-word; white-space: normal; width: 90px;',
                        ]),*/
                    
                    Tables\Columns\TextColumn::make('VALIDAT')
                        ->label('Decret')
                        ->limit(3)
                        ->searchable(),
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
                Tables\Columns\TextColumn::make('PERSCOD')
                    ->label('PERSCOD')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('DECRETAT')->label('Decretat'),
                TernaryFilter::make('is_notificat')->label('Notificat')->default(false),
                Filter::make('no_padro')
                    ->label('No padro')
                    ->default(true)
                    ->query(fn ($query) => $query->where('RESNUME', '!=', 'PADRO')),
            ])
            ->actions([
                //Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('sendToWs') 
                    ->label('Decretar')
                    ->hidden(fn ($record) => $record->DECRETAT)
                    ->action(fn ($record) => Instance::sendToWs($record))
                    ->icon('heroicon-o-arrow-up-circle'),
                Tables\Actions\Action::make('desDect') 
                    ->label('Treure decret')
                    ->hidden(fn ($record) => !$record->DECRETAT)
                    ->action(function ($record) {
                        $record->DECRETAT = false;
                        $record->skipValidation();
                        $record->save();
                    })
                    ->icon('heroicon-o-x-mark'),
                Tables\Actions\Action::make('notificar')
                    ->label('Notificar')
                    ->hidden(fn ($record) => $record->is_notificat)
                    ->action(fn ($record) => Instance::notifyInstance($record))
                    ->icon('heroicon-o-bell-alert'),
                Tables\Actions\Action::make('desNoti')
                    ->label('Treure Notificació')
                    ->hidden(fn ($record) => !$record->is_notificat || !auth()->user()->hasRole('Admin'))
                    ->action(function ($record) {
                        $record->is_notificat = false;
                        $record->skipValidation();
                        $record->save();
                    })
                    ->icon('heroicon-o-bell-slash'),
                /*Tables\Actions\Action::make('exportDocx')
                    ->label('Exp DOCX')
                    ->action(fn ($record) => static::downloadDocx($record))
                    ->icon('heroicon-o-arrow-down-tray'),*/
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
            DomiciliRelationManager::class,
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
    private static function refactorData($dataOriginal)
    {
        return substr($dataOriginal, 6, 2) . '/' . substr($dataOriginal, 4, 2) . '/' . substr($dataOriginal, 0, 4);
    }
    private static function changeFormatData($dateString)
    {
        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        
        if ($date && $date->format('Y') == '9999') {
            return 'indefinit';
        }

        return $date ? $date->format('d-m-Y') : null;
    }

    public static function exportToDocx($record)
    {
       //dd(self::changeFormatData($record->data_fi));
        if($record->VALIDAT!=null && $record->VALIDAT == 'FAVORABLE'){
            $templatePath = storage_path('app/templates/MODEL RESOLUCIO CAMERES.docx');
        }else{
            $templatePath = storage_path('app/templates/MODEL RESOLUCIO DESESTIMACIO.docx');
        }

        // Cargar la plantilla
        $templateProcessor = new TemplateProcessor($templatePath);

        // Reemplazar valores con los datos del registro
        $templateProcessor->setValue('PERSNOM', $record->person->PERSNOM);
        $templateProcessor->setValue('PERSCOG1', $record->person->PERSCOG1);
        $templateProcessor->setValue('PERSCOG2', $record->person->PERSCOG2);
        $templateProcessor->setValue('DNI', self::getDniAnon($record->person->NIFNUM,$record->person->NIFDC,$record->person->PERSPASSPORT));
        $templateProcessor->setValue('CARRER_HABITATGE', trim($record->domiciliAccess->nom_habitatge));
        $templateProcessor->setValue('REGISTRE_ENTRADA', $record->RESNUME);
        $templateProcessor->setValue('MOTIU', self::getTextMotiu($record));       
        $templateProcessor->setValue('DATA_PRESENTACIO', self::refactorData($record->data_presentacio));
        //$templateProcessor->setValue('VALIDAT', $record->VALIDAT);
        $templateProcessor->setValue('DATAFI', self::changeFormatData($record->data_fi));
        $templateProcessor->setValue('DATAINICI', self::changeFormatData($record->data_inici));
        $templateProcessor->setValue('AVUI', date('d/m/Y'));
        $templateProcessor->setValue('CAMERES',self::getCameres($record->carrersBarriVell));
        $templateProcessor->setValue('DOMICILI_VINCULAT1', 
            trim($record->domiciliAccess->nom_habitatge ?? '')
        );

        $templateProcessor->setValue('DOMICILI_VINCULAT2', 
            isset($record->domiciliAccess2->nom_habitatge) && trim($record->domiciliAccess2->nom_habitatge) !== '' 
                ? ', ' . trim($record->domiciliAccess2->nom_habitatge) 
                : ''
        );

        $templateProcessor->setValue('DOMICILI_VINCULAT3', 
            isset($record->domiciliAccess3->nom_habitatge) && trim($record->domiciliAccess3->nom_habitatge) !== '' 
                ? ', ' . trim($record->domiciliAccess3->nom_habitatge) 
                : ''
        );

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
                    $carrers .= $street->nom_carrer . ", ";
                }
            }
            $carrers = rtrim($carrers, ", ");
            $templateProcessor->setValue('CARRER_BARRI_VELL', $carrers);
        }
        return $templateProcessor;
    }
    private static function getCameres($carrers)
    {
        //Inicialitem una array on guardarem els noms dels carrers on hi ha les cameres
        $nomsCameres = [];
        //per cada carrer que tenim a l'instància
        foreach ($carrers as $carrer) {
            //per cada carrer de l'instància mirem les càmeres on esta validat
            foreach ($carrer->coveringCameras as $camera) {
                //Obtenim el nom del carrer de la càmera
                $nomCarrerCamera = $camera->ownerStreet?->street?->nom_carrer;
                //l'afegim a l'array
                if ($nomCarrerCamera) {
                    $nomsCameres[] = $nomCarrerCamera;
                }
            }
        }
        // Eliminar carrers dupicats i retornar en string
        return  implode(", ", array_unique($nomsCameres));
    }
    

    private static function getTextMotiu($record)
    {
        $motius = '';

        if ($record->empadronat_si_ivtm == true) {
            $motius .=  env('EMPADRONAT_SI_IVTM')."\n";
        }
        if ($record->empadronat_no_ivtm == true) {
            $motius .= env('EMPADRONAT_NO_IVTM')."\n";
        }
        if ($record->noempadronat_viu_barri_vell == true) {
            $motius .= env('NOEMPADRONAT_VIU_BARRI_VELL1') . $record->noempadronat_viu_barri_vell_text . env('NOEMPADRONAT_VIU_BARRI_VELL2')."\n";
        }
        if ($record->pares_menor_edat == true) {
            $motius .= env('PARES_MENOR_EDAT')."\n";
        }
        if ($record->familiar_adult_major == true) {
            $motius .= env('FAMILIAR_ADULT_MAJOR')."\n";
        }
        if ($record->targeta_aparcament_discapacitat == true) {
            $motius .= env('TARGETA_APARCAMENT_DISCAPACITAT')."\n";
        }
        if ($record->vehicle_comercial == true) {
            $motius .= env('VEHICLE_COMERCIAL')."\n";
        }
        if ($record->client_botiga == true) {
            $motius .= env('CLIENT_BOTIGA')."\n";
        }
        if ($record->empresa_serveis == true) {
            $motius .= env('EMPRESA_SERVEIS')."\n";
        }
        if ($record->empresa_constructora == true) {
            $motius .= env('EMPRESA_CONSTRUCTORA')."\n";
        }
        if ($record->familiar_resident == true) {
            $motius .= env('FAMILIAR_RESIDENT')."\n";
        }
        if ($record->acces_excepcional == true) {
            $motius .= env('ACCES_EXCEPCIONAL')."\n";
        }
        if ($record->altres_motius == true && !empty($record->altres_motius_text)) {
            $motius .= env('ALTRES_MOTIUS') . $record->altres_motius_text."\n";
        }
        return $motius = rtrim($motius, "\n");
    }

    private static function getDniAnon($dni,$dniDC, $passaport)
    {
        if($dni!=null){
            $document = $dni . $dniDC;
            $min = 4; 
            $max = 7;
        }else{
            $document = $passaport; 
            $min = 3; 
            $max = 6;
        }
        $result = '';
        $digitCount = 0;
        for ($i = 0; $i < strlen($document); $i++) {
            $char = $document[$i];
            if(ctype_digit($char)){
                $digitCount++;
                if ($digitCount >= $min && $digitCount <=$max) {
                    $result .= $char;
                } else{
                    $result .= '*';
                }
            }
            else{
                $result .= '*';
            }
        }
        
        return $result;
    }
}
