<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Notifications\Notification;
use App\Models\Camera;
use Illuminate\Database\Eloquent\Collection;

class StreetBarriVell extends Model
{
    use HasFactory;

    // Indicar clau primaria
    protected $primaryKey = 'CARCOD';

   // No estem utilitzant auto increment en la primary key
    public $incrementing = false;

    protected $keyType = 'int';

   protected $guarded=[];

   protected $fillable = [
       'CARCOD',
       'isCamera',
    ];
    public function ownedCamera()
    {
        return $this->hasOne(Camera::class, 'owner_CARCOD', 'CARCOD');
    }

    public function coveringCameras()
    {
        return $this->belongsToMany(Camera::class, 'camera_street', 'CARCOD', 'camera_id');
    }
    /**
     *  Get the Street 
     *
     *  @return BelongsTo<int, Street>
     */
    public function street()
    {
        return $this->belongsTo(Street::class, 'CARCOD','CARCOD');
    }

    public function getNomCarrerAttribute()
    {
        return $this->street ? $this->street->nom_carrer : null;
        //$this->street()->getNomCarrerAttribute();
    }

    public function instances()
    {
        return $this->belongsToMany(Instance::class, 'instance_street', 'CARCOD', 'instance_id');
    }
    /*
    public function vehicles()
    {
        return $this->hasManyThrough(Vehicle::class, Instance::class, 'RESNUME', 'instance_RESNUME', 'CARCOD', 'RESNUME');
    }*/

    public function vehicles(): Collection
    {
        return Vehicle::whereHas('instance', function ($query) {
            $query->whereHas('carrersBarriVell', function ($q) {
                $q->where('street_barri_vells.CARCOD', $this->CARCOD);
            });
        })->get();
    }
    public static function penjarVehicles()
    {
        $streets = self::all();
    
        foreach ($streets as $index => $street) {
            // Nomes el primer carrer tindrà les notificacions activades
            $notis = $index === 0;
            self::obtenirLListaCotxes($street, $notis);
        }
    }
    
    public static function obtenirLListaCotxes($record, $notis)
    {
        $token = self::createToken($record);
    
        // Si el token te algun error
        if (str_starts_with($token, "Error")) {
            if ($notis) {
                \App\Models\Instance::sendErrorNotification('Token error', $token, 'unknown');
            }
            return;
        }
    
        $vehiclesId = self::getVehiclesId($token);
    
        if (!is_array($vehiclesId)) {
            if ($notis) {
                \App\Models\Instance::sendErrorNotification('Vehicles error', $vehiclesId, 'unknown');
            }
            return;
        }
    
        if (count($vehiclesId) < 1 && $notis) {
            Notification::make()
                ->title('Llista buida')
                ->body('La llista de vehicles està buida.')
                ->info()
                ->send();
        }
    
        // Netejar vehicles anteriors
        foreach ($vehiclesId as $vehicle) {
            $deleteVehicle = self::deleteVehicles($token, $vehicle);
            if (str_starts_with($deleteVehicle, "Error")) {
                \App\Models\Instance::sendErrorNotification('Vehicles delete', $deleteVehicle, 'unknown');
            }
        }
    
        $instances = $record->instances;
        $numErrors = 0;
        $nVehiclesInsert = 0;
    
        foreach ($instances as $instance) {
            if ($instance->is_notificat && $instance->VALIDAT === 'FAVORABLE') {
                foreach ($instance->vehicles as $vehicle) {
                    if (now()->format('Y-m-d') <= $vehicle->DATAEXP) {
                        $v = self::insertVehicle($token, $vehicle);
    
                        if (str_starts_with($v, "Error")) {
                            if ($notis && ($numErrors > 0 || $nVehiclesInsert > 0)) {
                                \App\Models\Instance::sendErrorNotification('Vehicles insert', $v, 'unknown');
                            }
                            $numErrors++;
                        } else {
                            $nVehiclesInsert++;
                        }
                    }
                }
            }
        }
    
        // Missatges finals
        if ($notis) {
            if ($numErrors === 0 && $nVehiclesInsert > 0) {
                Notification::make()
                    ->title('Vehicles inserits')
                    ->body('S\'han afegit els vehicles correctament.')
                    ->success()
                    ->send();
            } elseif ($nVehiclesInsert < 1) {
                Notification::make()
                    ->title('Llista buida')
                    ->body('No hi ha vehicles assignats aquest carrer.')
                    ->warning()
                    ->send();
            }
        }
    }
    
    private static function createToken($record): string
    {
        $url = 'https://adm.alphadatamanager.com:8080/alpha-data-manager/oauth/token';

        $data = [
            'grant_type' => 'password',
            'username' => $record->user,
            'password' => env('CONTRASENYA_LLISTES')
        ];

        $username = env('USERNAME_AUTH');
        $password = env('PASSWORD_AUTH');

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            return 'Error en cURL: ' . curl_error($ch);
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['access_token'])) {
            return $responseData['access_token'];

        } else {
            return "Error:" . json_encode($responseData);
        }
    }
    private static function getVehiclesId($token)
    {
        $url = "https://adm.alphadatamanager.com:8080/alpha-data-manager/api/1.0/portal";
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPGET, true);        
        
        $headers = [
            "Authorization: Bearer " . $token
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return "Error: " . $error_msg;
        }
        
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        $vehicleIds = [];
        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $vehicle) {
                if (isset($vehicle['id'])) {
                    $vehicleIds[] = $vehicle['id'];
                }
            }
        } 
        return $vehicleIds;
    }
    private static function deleteVehicles($token,$vehicleId): string
    {
        $url ="https://adm.alphadatamanager.com:8080/alpha-data-manager/api/1.0/portal/" . $vehicleId;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $headers = [
            "Authorization: Bearer " . $token
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response, true);

        if (isset($data['value']) && $data['value'] === true) {
            return $data['value'];
        } else {
            return "Error a l'eliminar el vehicle. Codi d'estat' HTTP: " . $httpCode . ". Resposta: " . $response;
        }
    }
    private static function insertVehicle($token,$vehicle)
    {
        $url = "https://adm.alphadatamanager.com:8080/alpha-data-manager/api/1.0/portal";
        $ch = curl_init($url);
        $data = [
            "plate" => $vehicle->MATRICULA,
            "startsOn" => $vehicle->DATAINICI,
            "expiresOn" => $vehicle->DATAEXP
        ];
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $token,
            "Content-Type: application/json",
            "Content-Length: " . strlen($payload)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data= json_decode($response, true);
        if ($httpCode == 201 && isset($data['id'])) {
            return "Vehicle inserit amb èxit. ID: " . $data['id'];
        } else {
            return "Error: Codi d'estat HTTP: " . $httpCode . ". Resposta: " . $response;
        }
    }

    public static function booted(): void
    {
        static::creating(function ($record) 
        {
            $record->user = strtolower(str_replace([' ', "'"], '', substr("cameres_".$record->street->CARSIG, 0, -1) . $record->street->CARDESC . "@ajbanyoles.org"));
        });
        static::created(function($record)
        {
            if($record->isCamera==true){
                $camera = new \App\Models\Camera();
                $camera->owner_CARCOD = $record->CARCOD;
                $camera->save();
            }
        });
        static::updating(function ($record) {
            if($record->getOriginal('isCamera')!=$record->isCamera){
                if($record->isCamera==true){
                    $camera = new \App\Models\Camera();
                    $camera->owner_CARCOD = $record->CARCOD;
                    $camera->save();
                }
                //si es posa iscamera false eliminem
                if($record->isCamera==false){
                    $camera = \App\Models\Camera::where('owner_CARCOD', $record->CARCOD)->first();

                    if ($camera) {
                        $camera->coveredStreets()->detach();
                        $camera->delete(); 
                    }
                }
            }
        });
    }
}
