<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Notifications\Notification;
use App\Models\Camera;
use Illuminate\Database\Eloquent\Collection;
use  App\Jobs\PenjarVehiclesJob;

class StreetBarriVell extends Model
{
    use HasFactory;

    // Indicar clau primaria
    protected $primaryKey = 'PAISPROVMUNICARCOD';

   // No estem utilitzant auto increment en la primary key
    public $incrementing = false;

    protected $keyType = 'string';


   protected $guarded=[];

   protected $fillable = [
       'PAISPROVMUNICARCOD',
       'isCamera',
    ];
    public function ownedCamera()
    {
        return $this->hasOne(Camera::class, 'owner_PAISPROVMUNICARCOD', 'PAISPROVMUNICARCOD');
    }

    public function coveringCameras()
    {
        return $this->belongsToMany(Camera::class, 'camera_street', 'PAISPROVMUNICARCOD', 'camera_id');
    }
    /**
     *  Get the Street 
     *
     *  @return BelongsTo<int, Street>
     */
    public function street()
    {
        return $this->belongsTo(Street::class, 'PAISPROVMUNICARCOD','PAISPROVMUNICARCOD')
        ->where('PAISCOD', 108)
        ->where('PROVCOD', 17)
        ->where('MUNICOD', 15);
    }

    public function getNomCarrerAttribute()
    {
        return $this->street ? $this->street->nom_carrer : null;
        //$this->street()->getNomCarrerAttribute();
    }

    public function instances()
    {
        return $this->belongsToMany(Instance::class, 'instance_street', 'PAISPROVMUNICARCOD', 'instance_id');
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
                $q->where('street_barri_vells.PAISPROVMUNICARCOD', $this->PAISPROVMUNICARCOD);
            });
        })->get();
    }
    public static function penjarVehicles()
    {
        //Tots els carrers del barri vell
        $streetsBarriVell = self::all();
    
        foreach ($streetsBarriVell as $street) {
            //Segons el chat es mes optim
            foreach ([false, true] as $padro) {
                PenjarVehiclesJob::dispatch($street, $padro);
            }
            /*
            // obtenir llista de cotxes i penjar vehicles
            self::obtenirLListaCotxes($street, $notis,false);
            //Penjar llista padro
            self::obtenirLListaCotxes($street,$notis,true);*/
        }
    }
    public static function penjarVehiclesPadro()
    {
        //Tots els carrers del barri vell
        $streetsBarriVell = self::all();
    
        foreach ($streetsBarriVell as $street) {
            //self::obtenirLListaCotxes($street,$notis,true);
            PenjarVehiclesJob::dispatch($street, true);
        }
        Notification::make()
            ->title('Vehicles Padro penjats')
            ->info()
            ->send();
    }
    public static function penjarVehiclesInstancies()
    {
        //Tots els carrers del barri vell
        $streetsBarriVell = self::all();
    
        foreach ($streetsBarriVell as $street) {
            //self::obtenirLListaCotxes($street,$notis,false);
            PenjarVehiclesJob::dispatch($street, false);
        }
        Notification::make()
            ->title('Vehicles instàncies penjats')
            ->info()
            ->send();
    }
    public static function obtenirLListaCotxes($record, $notis, $isPadro)
    {
        $token = self::createToken($record,$isPadro);
        $resultats = [
            'carrer' => $record->nom_carrer,
            'eliminats' => 0,
            'insertats' => 0,
            'errors' => 0,
            'isPadro' => $isPadro,
            'detall_errors' => []
        ];
        // Si quan creem el token dona error
        if (self::isError($token)) {
            self::sendError('Token error', $token, $notis);
            $resultats['errors']++;
            $resultats['detall_errors'][] = ['context' => 'Token', 'error' => $token];
            return $resultats;
        }
        //obtenim llista vehicles de la llista alphanet
        $vehiclesId = self::getVehiclesId($token);
        if (!is_array($vehiclesId)) {
            self::sendError('Vehicles error', $vehiclesId, $notis);
            $resultats['errors']++;
            $resultats['detall_errors'][] = ['context' => 'GetVehiclesId', 'error' => $vehiclesId];
            return $resultats;
        }
        //Si la llista esta buida
        if (empty($vehiclesId) && $notis) {
            self::sendNotification('Llista buida', 'La llista de vehicles està buida.', 'info');
        }
    
        // Netejar vehicles anteriors
        foreach ($vehiclesId as $vehicle) {
            $result = self::deleteVehicles($token, $vehicle);
            if (self::isError($result)) {
                self::sendError('Vehicles delete', $result, true); // Siempre se notifica
            }
        }

        foreach ($vehiclesId as $vehicle) {
            $res = self::deleteVehicles($token, $vehicle);
            if (self::isError($res)) {
                self::sendError('Vehicles delete', $res, true);
                $resultats['errors']++;
                $resultats['detall_errors'][] = ['context' => 'DeleteVehicle', 'error' => $res];
            } else {
                $resultats['eliminats']++;
            }
        }
        if(!$isPadro){
            //Per cada instancia
            foreach ($record->instances as $instance) {
                //Mirem si la instancia es favorable, esta validada i notificada
                if ($instance->is_notificat && $instance->VALIDAT === 'FAVORABLE') {
                    //Per cada instancia mirem els vehicles que hi han associats
                    foreach ($instance->vehicles as $vehicle) {
                        //Mirem que no hagi expirat la caducitat del vehicle
                        if (now()->format('Y-m-d') <= $vehicle->DATAEXP) {
                            //inserim el vehicle
                            $response = self::insertVehicle($token, $vehicle, $instance);
                            //si ens ha donat algun error 
                            if (self::isError($response)) {
                                if ($notis && $resultats['errors'] === 0) {
                                    self::sendError('Vehicles insert', $response, true);
                                }
                                $resultats['errors']++;
                                $resultats['detall_errors'][] = ['context' => 'InsertVehicle', 'error' => $response];
                            } else {
                                $resultats['insertats']++;
                            }
                        }
                    }
                }
            }
        }else{
            $vehicles = self::obtenirVehiclesPadro($record->street->CARCOD);
            foreach ($vehicles as $v) {
                $response = self::insertVehiclePadro($token, $v);
                if (self::isError($response)) {
                    if ($notis && $resultats['errors'] === 0) {
                        self::sendError('Vehicles insert', $response, true);
                    }
                    $resultats['errors']++;
                    $resultats['detall_errors'][] = ['context' => 'InsertVehiclePadro', 'error' => $response];
                } else {
                    $resultats['insertats']++;
                }
            }
        }
        // Missatges finals
        if ($notis) {
            if ($resultats['errors'] === 0 && $resultats['insertats'] > 0) {
                self::sendNotification('Vehicles inserits', 'S\'han afegit els vehicles correctament.', 'success');
            } elseif ($resultats['insertats'] < 1) {
                self::sendNotification('Llista buida', 'No hi ha cap vehicle assignat a aquest(s) carrer(s).', 'warning');
            }
        }
        return $resultats;
    }
    private static function isError($response)
    {
        return str_starts_with($response, "Error");
    }

    private static function sendError($title, $message, $notify)
    {
        if ($notify) {
            \App\Models\Instance::sendErrorNotification($title, $message, 'unknown');
        }
    }

    private static function sendNotification($title, $message, $type)
    {
        Notification::make()
            ->title($title)
            ->body($message)
            ->$type()
            ->send();
    }
    
    private static function createToken($record, $isPadro): string
    {
        $url = 'https://adm.alphadatamanager.com:8080/alpha-data-manager/oauth/token';
        $user=$record->user;
        if($isPadro){
            // Separem el mail
            $emailParts = explode('@', $user);
            //Mirem que sigui un mail valid
            if (count($emailParts) === 2) {
                $user = $emailParts[0] . '-padro@' . $emailParts[1];
            }
        }
        $data = [
            'grant_type' => 'password',
            'username' => $user,
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
        $vehicleIds = [];
        $page = 0; // Comença des de la pàgina 0
        $size = 10; // Num resultats per pàg
        $hasNextPage = true; // variable per determinar si hi han més pàgines

        while ($hasNextPage) {
            $requestUrl = $url . "?page=" . $page . "&size=" . $size . "&sort=updatedOn,asc";

            $ch = curl_init($requestUrl);

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

            // Si hi han dades les processem
            if (isset($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as $vehicle) {
                    if (isset($vehicle['id'])) {
                        $vehicleIds[] = $vehicle['id'];
                    }
                }
            }

            // Verifiquem si hi han més pàgines
            $hasNextPage = ($data['page'] + 1) < $data['totalPages']; // Si no hem arribat a l'última pàgina seguim
            // Si hi han més pàgines incrementem el núm pàg.
            if ($hasNextPage) {
                $page++;
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
    private static function insertVehicle($token,$vehicle,$instance)
    {
        $url = "https://adm.alphadatamanager.com:8080/alpha-data-manager/api/1.0/portal";
        $ch = curl_init($url);
        $comentaris = $instance->NUMEXP;
        $propietari = $instance->person->NIFNUM . $instance->person->NIFDC . ' ' .$instance->person->PERSNOM . ' ' . $instance->person->PERSCOG1 . ' ' . $instance->person->PERSCOG2;
        $data = [
            "plate" => $vehicle->MATRICULA,
            "comments"=> $comentaris,
            "owner" => $propietari,
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
    private static function insertVehiclePadro($token,$vehicle)
    {
        $url = "https://adm.alphadatamanager.com:8080/alpha-data-manager/api/1.0/portal";
        $ch = curl_init($url);
        $comentaris = $vehicle['domcod'] . ' - ' . $vehicle['carsig'] . ' ' . $vehicle['cardesc'] . ' ' . $vehicle['domnum'];

        if (!empty($vehicle['dombis'])) {
            $comentaris .= ' ' . $vehicle['dombis'];
        }

        if (!empty($vehicle['domnum2'])) {
            $comentaris .= ' - ' . $vehicle['domnum2'];
            if (!empty($vehicle['dombis2'])) {
                $comentaris .= ' ' . $vehicle['dombis2'];
            }
        }

        if (!empty($vehicle['dombloc'])) {
            $comentaris .= ' Bloc ' . $vehicle['dombloc'];
        }

        if (!empty($vehicle['domptal'])) {
            $comentaris .= ' Pt.' . $vehicle['domptal'];
        }

        if (!empty($vehicle['domesc'])) {
            $comentaris .= ' Esc. ' . $vehicle['domesc'];
        }

        if (!empty($vehicle['dompis'])) {
            $comentaris .= ' Pis ' . $vehicle['dompis'];
        }

        if (!empty($vehicle['dompta'])) {
            $comentaris .= ' Porta ' . $vehicle['dompta'];
        }
        $propietari = $vehicle['nifnum'] . $vehicle['nifdc'] . ' ' .$vehicle['persnom'] . ' ' . $vehicle['perscog1'] . ' ' . $vehicle['perscog2'];
        $data = [
            "plate" => trim($vehicle['matricula']),
            "comments"=> $comentaris,
            "owner" => $propietari,
            "startsOn" => date('Y-m-d', strtotime('-1 day')),
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

    public static function obtenirVehiclesPadro(int $carcod): array
    {
        // Configuració Oracle
        $oracleHost = env('DB_ORACLE_HOST');
        $oraclePort = env('DB_ORACLE_PORT');
        $oracleService = env('DB_ORACLE_SERVICE_NAME');
        $oracleUser = env('DB_ORACLE_USERNAME');
        $oraclePass = env('DB_ORACLE_PASSWORD');

        $oracleDSN = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$oracleHost)(PORT=$oraclePort))(CONNECT_DATA=(SERVICE_NAME=$oracleService)))";

        // Conexió Oracle
        $conn = oci_connect($oracleUser, $oraclePass, $oracleDSN, 'AL32UTF8');
        if (!$conn) {
            throw new \Exception("Error Oracle: " . oci_error()['message']);
        }

        //Query
        $sql = "
            SELECT 
                H.PERSCOD, T.PERSCOD AS T_PERSCOD, H.PERSNOM, H.PERSCOG1, H.PERSCOG2,
                H.NIFNUM, H.NIFDC, V.MATRICULA, V.MOVTIP,
                H.DOMCOD, H.CARCOD, H.CARSIG, H.CARDESC, H.TRAMPAR,
                H.DOMNUM, H.DOMBIS, H.DOMNUM2, H.DOMBIS2, H.DOMKM, H.DOMHM,
                H.DOMBLOC, H.DOMPTAL, H.DOMESC, H.DOMPIS, H.DOMPTA,
                V.OBJCOD, H.NIFSW, H.PERSPASSPORT
            FROM GTR_VEHICLE V
            JOIN GTR_TIT_OBJ T ON V.OBJCOD = T.OBJCOD
            JOIN HAB_MOVHABS H ON T.PERSCOD = H.PERSCOD
            WHERE H.DATAFINAL = ' '
              AND V.MATRICULA IS NOT NULL
              AND H.MOVTIP <> 'B'
              AND V.MOVTIP <> 'B'
              AND H.CARCOD = $carcod
              AND (
                    ($carcod = 66 AND H.DOMNUM < 35) OR
                    ($carcod = 103 AND H.DOMNUM > 12) OR
                    ($carcod = 138 AND H.DOMNUM < 37) OR
                    ($carcod = 105 AND H.DOMNUM < 68) OR
                    ($carcod NOT IN (66, 103, 138, 105))
                )      
              
        ";

        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        $resultado = [];
        //Afegim cada row a l'array
        while (($row = oci_fetch_assoc($stid)) !== false) {
            $resultado[] = [
                'matricula'   => $row['MATRICULA'],
                'domcod'      => $row['DOMCOD'],
                'carsig'      => $row['CARSIG'],
                'cardesc'     => $row['CARDESC'],
                'domnum'      => $row['DOMNUM'],
                'dombis'      => $row['DOMBIS'] ?? null,
                'domnum2'     => $row['DOMNUM2'] ?? null,
                'dombis2'     => $row['DOMBIS2'] ?? null,
                'dombloc'     => $row['DOMBLOC'] ?? null,
                'domptal'     => $row['DOMPTAL'] ?? null,
                'domesc'      => $row['DOMESC'] ?? null,
                'dompis'      => $row['DOMPIS'] ?? null,
                'dompta'      => $row['DOMPTA'] ?? null,
                'persnom'     => $row['PERSNOM'],
                'perscog1'    => $row['PERSCOG1'],
                'perscog2'    => $row['PERSCOG2'],
                'nifnum'      => $row['NIFNUM'],
                'nifdc'       => $row['NIFDC'],
            ];
        }

        oci_free_statement($stid);
        oci_close($conn);
        //Retornem una array amb el resultat
        return $resultado;
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
                $camera->owner_PAISPROVMUNICARCOD = $record->PAISPROVMUNICARCOD;
                $camera->save();
            }
        });
        static::updating(function ($record) {
            if($record->getOriginal('isCamera')!=$record->isCamera){
                if($record->isCamera==true){
                    $camera = new \App\Models\Camera();
                    $camera->owner_PAISPROVMUNICARCOD = $record->PAISPROVMUNICARCOD;
                    $camera->save();
                }
                //si es posa iscamera false eliminem
                if($record->isCamera==false){
                    $camera = \App\Models\Camera::where('owner_PAISPROVMUNICARCOD', $record->PAISPROVMUNICARCOD)->first();

                    if ($camera) {
                        $camera->coveredStreets()->detach();
                        $camera->delete(); 
                    }
                }
            }
        });
    }
}
