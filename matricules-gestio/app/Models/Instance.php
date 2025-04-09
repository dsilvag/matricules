<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Exceptions\CustomValidationException;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class Instance extends Model
{
     // Indicar clau primaria
    protected $primaryKey = 'RESNUME';

    // No estem utilitzant auto increment en la primary key
    public $incrementing = false;

    protected $guarded=[];

    protected $fillable = [
        'RESNUME',
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
        'data_fi'
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

    */
    // Modelo Instance
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'instance_RESNUME', 'RESNUME');
    }

    public function vehiclesInSameDwelling()
    {
        return $this->hasMany(Vehicle::class, 'instance_RESNUME', 'RESNUME')
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

    public function carrersBarriVell()
    {
        return $this->belongsToMany(StreetBarriVell::class, 'instance_street','RESNUME','CARCOD')->withTimestamps();
    }

    public static function booted(): void
    {
        static::creating(function ($record) {
            $wsdl = "https://g5.banyoles.cat/GenesysWS/services/RegistreEntradaWS?wsdl";
            $params = array(
                'arg0' => array(
                    'aplicacio' => 'WEB',
                    'nivell' => '9999',
                    'usuari' => 'robot',
                    'numeroRegistreEntrada' => $record->RESNUME,
                ),
            );
                
            try {
                $client = new \SoapClient($wsdl);
                $response = $client->doRecuperarRegistreEntrada($params);
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
                    $record->REPRCOD=$response->return->codiPersona;
                }
                if (isset($response->return->codiDomiciliPersona)) {
                    $record->DOMCOD=$response->return->codiDomiciliPersona;
                }else{
                    self::sendErrorNotification('Domcod inexistent','El codi domicili no existeix al sistema.','DOMCOD');
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
        static::updating(function ($record) {
            $comptador = self::isToggleActive($record);
            if($comptador<1)
            {
                self::sendErrorNotification('Motiu no seleccionat','Si us plau, selecciona almenys un motiu abans de guardar.','motiu');
            }
            if($comptador>1)
            {
                self::sendErrorNotification('Més d\'un motiu seleccionat','Només es pot seleccionar un motiu. Si us plau, desmarca la resta abans de guardar.','motiu');
            }
        });

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
            ->send();
            throw ValidationException::withMessages([
                $field => [$message]
            ]);
    }
    public static function sendToWS()
    {
        //Configurar penjar word al web service
    }
}
