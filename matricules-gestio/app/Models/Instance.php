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
        'DOMCOD'
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
}
