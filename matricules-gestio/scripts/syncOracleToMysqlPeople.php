<?php 
use App\Jobs\SendOracleEmailJob;
// Config Oracle
$oracleHost = env('DB_ORACLE_HOST');
$oraclePort = env('DB_ORACLE_PORT');
$oracleService = env('DB_ORACLE_SERVICE_NAME');
$oracleUser = env('DB_ORACLE_USERNAME');
$oraclePass = env('DB_ORACLE_PASSWORD');

$oracleDSN = "(DESCRIPTION=
    (ADDRESS=(PROTOCOL=TCP)(HOST=$oracleHost)(PORT=$oraclePort))
    (CONNECT_DATA=(SERVICE_NAME=$oracleService))
)";

// Config MySQL
$mysqlHost = env('DB_HOST');
$mysqlUser = env('DB_USERNAME');
$mysqlPass = env('DB_PASSWORD');
$mysqlDB   = env('DB_DATABASE');

$logFile = storage_path('app/private/people.txt');
 
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
        PERSCOD, PAISCOD, PROVCOD, MUNICOD, PERSNOM, PERSCOG1, PERSCOG2,
        PERSPAR1, PERSPAR2, NIFNUMP, NIFNUM, NIFDC, NIFSW, PERSDCONNIF, PERSDCANNIF,
        PERSNACIONA, PERSPASSPORT, PERSNDATA, PERSPARE, PERSMARE, PERSSEXE, PERSSW,
        IDIOCOD, PERSVNUM, STDAPLADD, STDAPLMOD, STDUGR, STDUMOD, STDDGR, STDDMOD,
        STDHGR, STDHMOD, CONTVNUM, NIFORIG, PERSCODOLD, VALDATA, BAIXASW, GUID
        FROM NCL_PERSONA";  // Ajusta el nombre si es diferente

    $stid = oci_parse($connOracle, $sql);
    oci_execute($stid);

    $count = 0;

    while (($row = oci_fetch_assoc($stid)) !== false) {
        $fields = [];
        $values = [];
        $updates = [];

        foreach ($row as $col => $val) {
            $colLower = strtolower($col);
            
            // Truncar PERSCODOLD si es demasiado largo (más de 30 caracteres)
            if ($colLower === 'perscodold' && $val !== null && strlen($val) > 30) {
                $val = substr($val, 0, 30);  // Truncar a 30 caracteres
            }

            $fields[] = "`$colLower`";

            if ($val === null) {
                $escapedVal = "NULL";
            } else {
                $escapedVal = "'" . $connMySQL->real_escape_string($val) . "'";
            }

            $values[] = $escapedVal;

            if ($colLower !== 'perscod') { // perscod es PK
                $updates[] = "`$colLower` = VALUES(`$colLower`)";
            }
        }
        $now = date('Y-m-d H:i:s');
		$fields[] = "`created_at`";
		$fields[] = "`updated_at`";
		$values[] = "'" . $connMySQL->real_escape_string($now) . "'";
		$values[] = "'" . $connMySQL->real_escape_string($now) . "'";

        $sqlInsert = "INSERT INTO people (" . implode(',', $fields) . ") VALUES (" . implode(',', $values) . ")
                      ON DUPLICATE KEY UPDATE " . implode(',', $updates);

        if (!$connMySQL->query($sqlInsert)) {
            $errorMsg = "Error inserint perscod {$row['PERSCOD']}: " . $connMySQL->error . "\n";
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] " . $errorMsg, FILE_APPEND);
        } else {
            $count++;
        }
    }

    echo "Importados $count registros.\n";

    oci_free_statement($stid);
    oci_close($connOracle);
    $connMySQL->close();
    if ((file_exists($logFile) && filesize($logFile) > 0)) {
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'people'));
    }
    if(env('DEBUG_MAIL'))
    {
        $errorMsg = "Importats $count registres.\n";
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] " . $errorMsg, FILE_APPEND);
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'people'));
    }
    return true;
} catch (Exception $e) {
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] Error general: " . $e->getMessage() . "\n";
    file_put_contents($logFile, $errorMessage, FILE_APPEND);
    echo "Error: " . $e->getMessage() . "\n";
    if ((file_exists($logFile) && filesize($logFile) > 0) || env('DEBUG_MAIL')) {
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'people'));
    }
    exit(1);
    return false;
}
