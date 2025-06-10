<?php
use App\Jobs\SendOracleEmailJob;
// Configuración Oracle
$oracleHost = env('DB_ORACLE_HOST');
$oraclePort = env('DB_ORACLE_PORT');
$oracleService = env('DB_ORACLE_SERVICE_NAME');
$oracleUser = env('DB_ORACLE_USERNAME');
$oraclePass = env('DB_ORACLE_PASSWORD');

$oracleDSN = "(DESCRIPTION=
    (ADDRESS=(PROTOCOL=TCP)(HOST=$oracleHost)(PORT=$oraclePort))
    (CONNECT_DATA=(SERVICE_NAME=$oracleService))
)";

// Configuración MySQL
$mysqlHost = env('DB_HOST');
$mysqlUser = env('DB_USERNAME');
$mysqlPass = env('DB_PASSWORD');
$mysqlDB   = env('DB_DATABASE');

$logFile = storage_path('app/private/teleco.txt');

try {
    file_put_contents($logFile, '');
    // Conexión Oracle
    $connOracle = oci_connect($oracleUser, $oraclePass, $oracleDSN, 'AL32UTF8');
    if (!$connOracle) {
        throw new Exception("Error Oracle: " . oci_error()['message']);
    }

    // Conexión MySQL
    $connMySQL = new mysqli($mysqlHost, $mysqlUser, $mysqlPass, $mysqlDB);
    if ($connMySQL->connect_error) {
        throw new Exception("Error MySQL: " . $connMySQL->connect_error);
    }

    // Consulta Oracle
    $sql = "SELECT 
        PERSCOD, NUMORDRE, TIPCONTACTE, CONTACTE, OBSERVACIONS, STDUGR, STDUMOD, 
        STDDGR, STDDMOD, STDHGR, STDHMOD, VALDATA, BAIXASW
        FROM NCL_TELECO";  // Ajusta el nombre de la tabla si es necesario

    $stid = oci_parse($connOracle, $sql);
    oci_execute($stid);

    $count = 0;

    while (($row = oci_fetch_assoc($stid)) !== false) {
        // Verificar si el registro ya existe en MySQL
        $perscod = $row['PERSCOD'];
        $numordre = $row['NUMORDRE'];

        // Consulta para verificar si ya existe el registro con la combinación PERSCOD y NUMORDRE
        $checkQuery = "SELECT 1 FROM telecos WHERE PERSCOD = ? AND NUMORDRE = ?";
        $stmt = $connMySQL->prepare($checkQuery);
        $stmt->bind_param("ii", $perscod, $numordre);  // Asegúrate de que sean enteros
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Si el registro existe, realizar un UPDATE
            $updateFields = [];
            $updateValues = [];
            foreach ($row as $col => $val) {
                $colLower = strtolower($col);  // Convertir a minúsculas para coincidir con los nombres de columna en MySQL
                if (!in_array($colLower, ['perscod', 'numordre'])) {  // No actualizar los campos clave
                    $updateFields[] = "`$colLower` = ?";
                    $updateValues[] = $val;
                }
            }
            // Realizar el UPDATE
            $updateQuery = "UPDATE telecos SET " . implode(", ", $updateFields) . " WHERE PERSCOD = ? AND NUMORDRE = ?";
            $stmtUpdate = $connMySQL->prepare($updateQuery);
            $updateValues[] = $perscod;
            $updateValues[] = $numordre;
            $stmtUpdate->bind_param(str_repeat("s", count($updateValues)-2) . "ii", ...$updateValues);  // Vincula los parámetros
            $stmtUpdate->execute();
        } else {
            // Si no existe, realizar el INSERT
            $fields = [];
            $values = [];
            foreach ($row as $col => $val) {
                $colLower = strtolower($col);  // Convertir a minúsculas para coincidir con los nombres de columna en MySQL
                $fields[] = "`$colLower`";

                if ($val === null) {
                    $escapedVal = "NULL";
                } else {
                    $escapedVal = "'" . $connMySQL->real_escape_string($val) . "'";
                }

                $values[] = $escapedVal;
            }
            
            $now = date('Y-m-d H:i:s');
			$fields[] = "`created_at`";
			$fields[] = "`updated_at`";
			$values[] = "'" . $connMySQL->real_escape_string($now) . "'";
			$values[] = "'" . $connMySQL->real_escape_string($now) . "'";

            // Inserción sin incluir el campo `id`, que es autonumérico
            $sqlInsert = "INSERT INTO telecos (" . implode(',', $fields) . ") 
                          VALUES (" . implode(',', $values) . ")";
            if (!$connMySQL->query($sqlInsert)) {
                $errorMsg = "Error inserint PERSCOD {$perscod}, NUMORDRE {$numordre}: " . $connMySQL->error . "\n";
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] " . $errorMsg, FILE_APPEND);
            }
        }

        $count++;
    }

    echo "Importados $count registros.\n";

    oci_free_statement($stid);
    oci_close($connOracle);
    $connMySQL->close();
    if ((file_exists($logFile) && filesize($logFile) > 0)) {
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'teleco'));
    }
    if(env('DEBUG_MAIL'))
    {
        $errorMsg = "Importats $count registres.\n";
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] " . $errorMsg, FILE_APPEND);
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'teleco'));
    }
    return true;
} catch (Exception $e) {
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] Error general: " . $e->getMessage() . "\n";
    file_put_contents($logFile, $errorMessage, FILE_APPEND);
    if ((file_exists($logFile) && filesize($logFile) > 0) || env('DEBUG_MAIL')) {
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'teleco'));
    }
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
    return false;
}
