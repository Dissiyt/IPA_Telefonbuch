<?php
session_start();
require 'ldap_config.php';
require 'ldap_connect.php';
global $searchBase, $attributes, $ldapConn;
// Setzt die Zeitzone auf Berlin
date_default_timezone_set('Europe/Berlin'); 

// Entfernt führende / nachfolgende Leerzeichen, Backslashes und konvertiert Sonderzeichen in HTML-Entitäten.
// Entfernt alle Zeichen die nicht in $allowed_chars sind
//Return des sanitized Strings
function sanitizeInput($data) {
    $data = trim($data); 
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $allowed_chars = "a-zA-Z0-9 .@äüöÄÜÖ_-";
    return preg_replace("/[^" . $allowed_chars . "]/", '', $data);
}

$searchTerm = isset($_POST['search']) ? sanitizeInput($_POST['search']) : '';
// Teilt searchTerm in einzelne Wörter auf
$searchTerms = explode(" ", $searchTerm);
// Filter für die Suche
$filter = "(|";
if (count($searchTerms) >= 1) {
    //Falls $searchTerm ein Wort oder mehr hat, wird $searchTerm als eine Einheit gesucht
    $attributesToSearch = array("givenname", "sn","aiusbcordlessinternal", "aiusbcordless", "title", "ou", "mail", "aiusbdienste", "costcenter");
    foreach ($attributesToSearch as $param) {
        $filter .= "($param=$searchTerm*)";
    }
    }
//Überprüft ob $searchTerm zwei Wörter hat
if (count($searchTerms) == 2) {
    // Flexible Reihenfolge für zwei Wörter (givenName & sn)
    $filter .= "(&(givenname=$searchTerms[0]*)(sn=$searchTerms[1]*))";
    $filter .= "(&(sn=$searchTerms[0]*)(givenname=$searchTerms[1]*))";
}
$filter .= ")";

//Ausführen der LDAP Suche
$search = ldap_search($ldapConn, $searchBase,  $filter, $attributes);
$results = ldap_get_entries($ldapConn, $search);
//Überprüft ob Ergebnisse gefunden wurden
if ($results['count'] == 0) {
    logLdapActivity('WARNING', 'LDAP search returned no results.');
} else {
    logLdapActivity('SUCCESS', 'LDAP search returned results.');
}
//Schliessen der LDAP Verbindung nach der Suche
ldap_close($ldapConn);
//Weiterleitung zu index.php die Ergebnisse werden in einer Session gespeichert
$_SESSION['results'] = $results;
$results = isset($_SESSION['results']) ? $_SESSION['results'] : array();
header('Location: index.php');
?>