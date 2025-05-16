<?php

// Config Oracle
$oracleHost = '172.17.18.33';
$oraclePort = '1521';
$oracleService = 'pdb1_genesys5';
$oracleUser = 'BANYOLES';
$oraclePass = 'AUDIBANY2016';
 
$oracleDSN = "(DESCRIPTION=
    (ADDRESS=(PROTOCOL=TCP)(HOST=$oracleHost)(PORT=$oraclePort))
    (CONNECT_DATA=(SERVICE_NAME=$oracleService))
)";
 
// Config MySQL
$mysqlHost = '127.0.0.1';
$mysqlUser = 'matricules';
$mysqlPass = 'ZSNogL6wH5OSEzP$34';
$mysqlDB   = 'matricules';
 
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
            FROM NCL_DOMICILI";
 
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