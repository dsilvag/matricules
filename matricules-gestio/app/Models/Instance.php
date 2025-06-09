<?php

namespace App\Models;

use App\Filament\Resources\InstanceResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Exceptions\CustomValidationException;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class Instance extends Model
{
     // Indicar clau primaria
    //protected $primaryKey = 'RESNUME';

    // No estem utilitzant auto increment en la primary key
//    public $incrementing = false;

  //  protected $keyType = 'string';

    protected $skipValidation = false;

    protected $guarded=[];

    protected $fillable = [
        'RESNUME',
        'is_notificat',
        'NUMEXP',
        'DECRETAT',
        'VALIDAT',
        'PERSCOD',
        'REPRCOD',
        'DOMCOD',
        'empadronat_si_ivtm',
        'empadronat_no_ivtm',
        'noempadronat_viu_barri_vell',
        'noempadronat_viu_barri_vell_text',
        'pares_menor_edat',
        'familiar_adult_major',
        'targeta_aparcament_discapacitat',
        'vehicle_comercial',
        'client_botiga',
        'empresa_serveis',
        'empresa_constructora',
        'familiar_resident',
        'acces_excepcional',
        'altres_motius',
        'altres_motius_text',
        'data_inici',
        'data_fi',
        'data_presentacio',
        'domicili_acces'
    ];

    /**
     *  Get all of vehicles for the instance
     *
     *  @return HasMany<int, vehicles>
     */
    /*
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'instances_vehicle','RESNUME','MATRICULA')->withTimestamps();
    }

    *//*
    // Modelo Instance
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'instance_RESNUME', 'RESNUME');
    }*/
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'instance_id', 'id');
    }


    public function vehiclesInSameDwelling()
    {
        return $this->hasMany(Vehicle::class, 'instance_id', 'id')
            ->whereHas('instance', function($query) {
                $query->where('DOMCOD', $this->DOMCOD)
                      ->where('RESNUME', '!=', $this->RESNUME);
            });
    }
    
    public function person()
    {
        return $this->belongsTo(Person::class, 'PERSCOD');
    }

    public function personRepresentative()
    {
        return $this->belongsTo(Person::class, 'REPRCOD');
    }

    public function domicili()
    {
        return $this->belongsTo(Dwelling::class, 'DOMCOD', 'DOMCOD');
    }

    public function domiciliAccess()
    {
        return $this->belongsTo(Dwelling::class, 'domicili_acces');
    }
    public function domiciliAccess2()
    {
        return $this->belongsTo(Dwelling::class, 'domicili_acces2');
    }
    public function domiciliAccess3()
    {
        return $this->belongsTo(Dwelling::class, 'domicili_acces3');
    }

    public function carrersBarriVell()
    {
        return $this->belongsToMany(StreetBarriVell::class, 'instance_street','instance_id','PAISPROVMUNICARCOD')->withTimestamps();
    }

    public static function booted(): void
    {
        static::creating(function ($record) {
            
            $params = array(
                'arg0' => array(
                    'aplicacio' => $_ENV['APLICACIO_WS'],
                    'nivell' => $_ENV['NIVELL_WS'],
                    'usuari' => $_ENV['USUARI_WS'],
                    'numeroRegistreEntrada' => $record->RESNUME,
                ),
            );
                
            try {
                $client = new \SoapClient($_ENV['WEB_SERVICE_REGISTRE_ENTRADA']);
                $response = $client->doRecuperarRegistreEntrada($params);
                //dd($response);
                if (isset($response->return->registreRelacionat)) {
                   $record->NUMEXP=$response->return->registreRelacionat;
                }else{
                    self::sendErrorNotification('Numexp inexistent','El número d\'expedient no existeix al sistema.','NUMEXP');
                }
                if (isset($response->return->codiPersona)) {
                    $record->PERSCOD=$response->return->codiPersona;
                }else{
                    self::sendErrorNotification('Perscod inexistent','El codi persona no existeix al sistema.','PERSCOD');
                }
                if (isset($response->return->codiRepresentant)) {
                    $record->REPRCOD=$response->return->codiRepresentant;
                }
                if (isset($response->return->codiDomiciliPersona)) {
                    $domcod = $response->return->codiDomiciliPersona;
                
                    if (\App\Models\Dwelling::where('DOMCOD', $domcod)->exists()) {
                        $record->DOMCOD = $domcod;
                        if(\App\Models\StreetBarriVell::where('PAISPROVMUNICARCOD',$record->domicili->PAISPROVMUNICARCOD)->exists()){
                            $record->domicili_acces = $domcod;
                        }
                    } else {
                        self::sendErrorNotification(
                            'Domcod no trobat a la base de dades',
                            'El codi de domicili proporcionat pel servei no existeix a la taula habitatges.',
                            'DOMCOD'
                        );
                    }
                }               
                if (isset($response->return->dataPresentacio)) {
                    $record->data_presentacio=$response->return->dataPresentacio;
                }else{
                    self::sendErrorNotification('Data presentacio inexistent','La data presentacio no existeix al sistema.','data_presentacio');
                }
            } catch (\SoapFault $e) {
                if (strpos($e->getMessage(), 'REG_REG_ENTRADA_INEXISTENT') !== false) {
                    self::sendErrorNotification('Resnume inexistent','El número de registre no és vàlid.','RESNUM');
                } else {
                    self::sendErrorNotification('Error Soap',$e->getMessage(),'unknown');
                }
            } catch (Exception $e) {
                self::sendErrorNotification('Error general',$e->getMessage(),'unknown');
            }
        });
        static::created(function ($record){
            if(isset($record->domicili_acces)){
                //Mira si el carrer que te assignat aquest domcod es un carrer del barri vell
                //si compleix el requisit hauríem d'assignar aquest carrer al camp carrer destí (hi ha una taula entremig)
                if(\App\Models\StreetBarriVell::where('PAISPROVMUNICARCOD',$record->domiciliAccess->PAISPROVMUNICARCOD)->exists()){
                    DB::table('instance_street')->insert([
                        'instance_id' => $record->id,
                        'PAISPROVMUNICARCOD' => $record->domiciliAccess->PAISPROVMUNICARCOD,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
        static::updating(function ($record) {
            if (!$record->skipValidation) {
                $comptador = self::isToggleActive($record);
                if($comptador<1)
                {
                    self::sendErrorNotification('Motiu no seleccionat','Si us plau, selecciona almenys un motiu abans de guardar.','motiu');
                }
                if($comptador>1)
                {
                    self::sendErrorNotification('Més d\'un motiu seleccionat','Només es pot seleccionar un motiu. Si us plau, desmarca la resta abans de guardar.','motiu');
                }
                if($record->data_inici > $record->data_fi)
                {
                    self::sendErrorNotification('Error dates','La data fi no pot ser més petita que la data inici','data_fi');
                }
                //Al modificar la data inici o data fi s'ha de modificar la data del vehicles assignats a l'instància
                if($record->isDirty('data_inici') || $record->isDirty('data_fi') || $record->isDirty('VALIDAT'))
                {
                    //dd($record->vehicles());
                    if($record->VALIDAT!='DESFAVORABLE'){
                        $record->vehicles()->update([
                            'DATAINICI' => $record->data_inici,
                            'DATAEXP' => $record->data_fi,
                        ]);
                    }
                    else{
                        $record->vehicles()->update([
                            'DATAINICI' => now()->subDay()->format('Y-m-d'),
                            'DATAEXP' => now()->subDay()->format('Y-m-d'),
                        ]);
                    }
                }
            }
        });
        static::deleting(function ($record) {
            //dd($record);
        });

    }
    public function assignDomicili(Dwelling $dom, $num)
    {
        //Assignem el domicili i guardem
        $this->skipValidation();
        if($num==3){
            $this->domicili_acces3 = $dom->DOMCOD;
        }
        else if($num==2){
            $this->domicili_acces2 = $dom->DOMCOD;
        }
        else{
            $this->domicili_acces = $dom->DOMCOD;
        }
        $this->save();

        $this->checkAndAssignBarriVell($dom);
    }
    protected function checkAndAssignBarriVell(Dwelling $dom): void
    {
        //mirem si es un carrer del barri vell
        $carrerCode = $dom->PAISPROVMUNICARCOD;

        $carrer = \App\Models\StreetBarriVell::where('PAISPROVMUNICARCOD', $carrerCode)->first();

        if ($carrer) {
            $alreadyAssigned = $this->carrersBarriVell()
                ->where('street_barri_vells.PAISPROVMUNICARCOD', $carrerCode)
                ->exists();

            if (!$alreadyAssigned) {
                $this->carrersBarriVell()->attach($carrer->PAISPROVMUNICARCOD);
            }
        }

        $this->skipValidation();
        $this->save();
    }
    private static function isToggleActive($record)
    {
        $comptador = 0;

        if ($record->empadronat_si_ivtm === true) {
            $comptador++;
        }
        if ($record->empadronat_no_ivtm === true) {
            $comptador++;
        }
        if ($record->noempadronat_viu_barri_vell === true) {
            $comptador++;
        }
        if ($record->pares_menor_edat === true) {
            $comptador++;
        }
        if ($record->familiar_adult_major === true) {
            $comptador++;
        }
        if ($record->targeta_aparcament_discapacitat === true) {
            $comptador++;
        }
        if ($record->vehicle_comercial === true) {
            $comptador++;
        }
        if ($record->client_botiga === true) {
            $comptador++;
        }
        if ($record->empresa_serveis === true) {
            $comptador++;
        }
        if ($record->empresa_constructora === true) {
            $comptador++;
        }
        if ($record->familiar_resident === true) {
            $comptador++;
        }
        if ($record->acces_excepcional === true) {
            $comptador++;
        }
        if ($record->altres_motius === true) {
            $comptador++;
        }
        
        return $comptador;
    }
    public static function sendErrorNotification($title,$message,$field)
    {
        Notification::make()
            ->title($title)
            ->body($message)
            ->danger()
            ->persistent()
            ->send();
            throw ValidationException::withMessages([
                $field => [$message]
            ]);
    }
    public static function sendToWS($record)
    {
        if($record->VALIDAT==null){
            self::sendErrorNotification('Instància no validada','Per poder enviar la instància, cal omplir el camp FAVORABLE / DESFAVORABLE del formulari.','VALIDAT');
        }
        //dd(InstanceResource::exportBase64($record));
        $params = array(
            'arg0' => array(
                'aplicacio' => $_ENV['APLICACIO_WS'],
                'nivell' => $_ENV['NIVELL_WS'],
                'usuari' => $_ENV['USUARI_WS'],
                'aplcod' => 'SDE',
                'descriptor' => 'DEC AUTORITZACIO BARRI VELL ' . $record->RESNUME . ' ' . date('dmY'),
                'doccod' => 'GENE',
                'docnompc' => 'DEC AUTORITZACIO BARRI VELL ' . $record->RESNUME . '_' . time() . '.docx',
                'docorigen' => 'EXPED',
                'doctip' => '0024',
                'fitxerAnnexat' => InstanceResource::exportBase64($record),
                'identificador' => $record->NUMEXP,
                'modelcod' => 'DECR',
                'sdenum' => $record->NUMEXP, 
            ),
        );
        try {
            InstanceResource::exportBase64($record); //document amb base64
            $client = new \SoapClient($_ENV['WEB_SERVICE_ANNEXAR_DOC']);
            $response = $client->doAnnexarDocumentExp($params);
            $record->skipValidation();
            $record->DECRETAT=true;
            $record->save();
            Notification::make()
                ->title('Document enviat correctament')
                ->success()
                ->send();
            $outputPath = storage_path('app/public/decret_' . $record->RESNUME . '.docx');
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }
        } catch (\SoapFault $e) {
            self::sendErrorNotification('Error Soap',$e->getMessage(),'unknown');
        } catch (Exception $e) {
            self::sendErrorNotification('Error general',$e->getMessage(),'unknown');
        }
    }

    public static function notifyInstance($record)
    {
        if($record->DECRETAT!=true)
        {
            self::sendErrorNotification('Instància no decretada','Per tal de poder notificar la instància, és necessari emetre una resolució.','VALIDAT');
        }
        if($record->is_notificat==true)
        {
            self::sendErrorNotification('Notificació','Aquesta instància ja ha estat notificada','is_notificat');
        }else{
            $record->skipValidation();
            $record->is_notificat=true;
            $record->save();
            Notification::make()
                ->title('Notificació enviada correctament')
                ->success()
                ->send();
        }
    }

    public function skipValidation()
    {
        $this->skipValidation = true;
    }
}
