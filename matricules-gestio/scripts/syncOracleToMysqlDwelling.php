<?php

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
 
try {
    // Connexió Oracle
    $connOracle = oci_connect($oracleUser, $oraclePass, $oracleDSN, 'AL32UTF8');
    if (!$connOracle) {
        throw new Exception("Error Oracle: " . oci_error()['message']);
    }
 
    // Connexió MySQL
    $connMySQL = new mysqli($mysqlHost, $mysqlUser, $mysqlPass, $mysqlDB);
    if ($connMySQL->connect_error) {
        throw new Exception("Error MySQL: " . $connMySQL->connect_error);
    }
 
    // Consulta Oracle
    $sql = "SELECT DOMCOD, 
                   PAISCOD || '-' || PROVCOD || '-' || MUNICOD || '-' || CARCOD AS PAISPROVMUNICARCOD,
                   PAISCOD, PROVCOD, MUNICOD, CARCOD, PSEUDOCOD, GISCOD, DOMNUM,
                   DOMBIS, DOMNUM2, DOMBIS2, DOMESC, DOMPIS, DOMPTA, DOMBLOC, DOMPTAL, DOMKM, DOMHM,
                   DOMTLOC, APCORREUS, DOMTIP, DOMOBS, VALDATA, BAIXASW, STDAPLADD, STDAPLMOD, STDUGR, STDUMOD,
                   STDDGR, STDDMOD, STDHGR, STDHMOD, DOMCP, X, Y, POBLDESC, GUID, SWREVISAT, REFCADASTRAL, SWPARE,
                   CIV
            FROM NCL_DOMICILI
            WHERE CARCOD IS NOT NULL";
 
    $stid = oci_parse($connOracle, $sql);
    oci_execute($stid);
 
    $count = 0;
 
    while (($row = oci_fetch_assoc($stid)) !== false) {
        // Escapar i preparar valors
        $fields = [];
        $values = [];
        $updates = [];
 
        foreach ($row as $col => $val) {
            $colLower = strtolower($col);
            $fields[] = "`$colLower`";
 
            if ($val === null) {
                $escapedVal = "NULL";
            } else {
                $escapedVal = "'" . $connMySQL->real_escape_string($val) . "'";
            }
 
            $values[] = $escapedVal;
 
            if ($colLower !== 'domcod') {  // domcod és PK, no s'actualitza
                $updates[] = "`$colLower` = VALUES(`$colLower`)";
            }
        }
        $now = date('Y-m-d H:i:s');
		$fields[] = "`created_at`";
		$fields[] = "`updated_at`";
		$values[] = "'" . $connMySQL->real_escape_string($now) . "'";
		$values[] = "'" . $connMySQL->real_escape_string($now) . "'";
        
        $sqlInsert = "INSERT INTO dwellings (" . implode(',', $fields) . ") VALUES (" . implode(',', $values) . ")
                      ON DUPLICATE KEY UPDATE " . implode(',', $updates);
 
        if (!$connMySQL->query($sqlInsert)) {
            echo "Error inserint DOMCOD {$row['DOMCOD']}: " . $connMySQL->error . "\n";
        } else {
            $count++;
        }
    }
 
    echo "Importats $count registres.\n";
 
    oci_free_statement($stid);
    oci_close($connOracle);
    $connMySQL->close();
    return true;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
    return false;
}