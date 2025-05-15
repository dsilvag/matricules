<?php
// Configuració ORACLE
$oracleHost = '172.17.18.33';
$oraclePort = '1521';
$oracleService = 'pdb1_genesys5';
$oracleUser = 'BANYOLES';
$oraclePass = 'AUDIBANY2016';
 
$oracleDSN = "(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = $oracleHost)(PORT = $oraclePort))
    (CONNECT_DATA = (SERVICE_NAME = $oracleService))
)";
 
// Configuració MYSQL
$mysqlHost = '127.0.0.1';
$mysqlUser = 'matricules';
$mysqlPass = 'ZSNogL6wH5OSEzP$34';
$mysqlDB   = 'matricules';
 
try {
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
 
        $columnsStr = implode(', ', $cols);
        $valuesStr = implode(', ', $vals);
        $updatesStr = implode(', ', $updates);
 
        $sqlInsert = "
            INSERT INTO streets ($columnsStr)
            VALUES ($valuesStr)
            ON DUPLICATE KEY UPDATE $updatesStr
        ";
 
        if (!$connMySQL->query($sqlInsert)) {
            echo "❌ Error inserint/clau duplicada: " . $connMySQL->error . "\n";
        } else {
            $count++;
        }
    }
 
    echo "✅ Importació completada amb èxit: $count registres inserits/actualitzats.\n";
 
    oci_free_statement($stid);
    oci_close($connOracle);
    $connMySQL->close();
 
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}