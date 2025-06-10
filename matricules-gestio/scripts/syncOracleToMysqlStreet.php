<?php
use App\Jobs\SendOracleEmailJob;
// Configuració ORACLE
$oracleHost = env('DB_ORACLE_HOST');
$oraclePort = env('DB_ORACLE_PORT');
$oracleService = env('DB_ORACLE_SERVICE_NAME');
$oracleUser = env('DB_ORACLE_USERNAME');
$oraclePass = env('DB_ORACLE_PASSWORD');
 
$oracleDSN = "(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = $oracleHost)(PORT = $oraclePort))
    (CONNECT_DATA = (SERVICE_NAME = $oracleService))
)";
 
// Configuració MYSQL
$mysqlHost = env('DB_HOST');
$mysqlUser = env('DB_USERNAME');
$mysqlPass = env('DB_PASSWORD');
$mysqlDB   = env('DB_DATABASE');
 
$logFile = storage_path('app/private/street.txt');

try {
    file_put_contents($logFile, '');
    // Connexió Oracle
    $connOracle = oci_connect($oracleUser, $oraclePass, $oracleDSN, 'AL32UTF8');
    if (!$connOracle) {
        $e = oci_error();
        throw new Exception("Error Oracle: " . $e['message']);
    }
 
    // Connexió MySQL
    $connMySQL = new mysqli($mysqlHost, $mysqlUser, $mysqlPass, $mysqlDB);
    if ($connMySQL->connect_error) {
        throw new Exception("Error MySQL: " . $connMySQL->connect_error);
    }
 
    // Consulta Oracle
    $sql = <<<SQL
SELECT 
    PAISCOD || '-' || PROVCOD || '-' || MUNICOD || '-' || CARCOD AS PAISPROVMUNICARCOD,
    PAISCOD, PROVCOD, MUNICOD, CARCOD, CARSIG, CARPAR, CARDESC, CARDESC2,
    STDUGR, STDUMOD, STDDGR, STDDMOD, STDHGR, STDHMOD,
    VALDATA, BAIXASW, INICIFI, OBSERVACIONS, ORGCOD,
    ORGDATA, ORGOBS, PLACA, GENERIC, ESPECIFIC,
    TEMATICA, SEXE, LOCAL
FROM NCL_CARRER
SQL;
 
    $stid = oci_parse($connOracle, $sql);
    oci_execute($stid);
 
    $count = 0;
    while ($row = oci_fetch_assoc($stid)) {
        // Escapem els valors
        $cols = [];
        $vals = [];
        $updates = [];
 
        foreach ($row as $col => $val) {
            $colLower = strtolower($col);
            $cols[] = "`$colLower`";
            $safeVal = $val === null ? "NULL" : "'" . $connMySQL->real_escape_string($val) . "'";
            $vals[] = $safeVal;
            if ($colLower !== 'paisprovmunicarcod') {
                $updates[] = "`$colLower` = VALUES(`$colLower`)";
            }
        }
        
        $now = date('Y-m-d H:i:s');
        $cols[] = "`created_at`";
        $vals[] = "'" . $connMySQL->real_escape_string($now) . "'";
        $cols[] = "`updated_at`";
        $vals[] = "'" . $connMySQL->real_escape_string($now) . "'";
        $updates[] = "`updated_at` = VALUES(`updated_at`)";


        $columnsStr = implode(', ', $cols);
        $valuesStr = implode(', ', $vals);
        $updatesStr = implode(', ', $updates);
 
        $sqlInsert = "
            INSERT INTO streets ($columnsStr)
            VALUES ($valuesStr)
            ON DUPLICATE KEY UPDATE $updatesStr
        ";
 
        if (!$connMySQL->query($sqlInsert)) {
            $errorMsg="Error inserint PAISPROVMUNICARCOD {$row['PAISPROVMUNICARCOD']}: " . $connMySQL->error . "\n";
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] " . $errorMsg, FILE_APPEND);
        } else {
            $count++;
        }
    }
 
    echo "✅ Importació completada amb èxit: $count registres inserits/actualitzats.\n";
 
    oci_free_statement($stid);
    oci_close($connOracle);
    $connMySQL->close();
    if ((file_exists($logFile) && filesize($logFile) > 0)) {
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'street'));
    }
    if(env('DEBUG_MAIL')){
        $errorMsg="Importats $count registres.\n";
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] " . $errorMsg, FILE_APPEND);
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'street'));
    }
    return true;
 
} catch (Exception $e) {
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] Error general: " . $e->getMessage() . "\n";
    file_put_contents($logFile, $errorMessage, FILE_APPEND);
    if ((file_exists($logFile) && filesize($logFile) > 0) || env('DEBUG_MAIL')) {
        $logContent = file_get_contents($logFile);
        dispatch(new SendOracleEmailJob($logContent, 'street'));
    }
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
    return false;
}