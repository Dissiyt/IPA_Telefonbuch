<?php
session_start();
require 'ldap_config.php';
require 'ldap_connect.php';
global $searchBase, $attributes, $ldapConn;
// Setzt die Zeitzone auf Berlin
date_default_timezone_set('Europe/Berlin'); 

// Entfernt führende / nachfolgende Leerzeichen, Backslashes und konvertiert Sonderzeichen in HTML-Entitäten
function sanitize_input($data) {
    $data = trim($data); 
    $data = stripslashes($data);
    $data = htmlspecialchars($data); 
    return $data;
}

$searchTerm = isset($_POST['search']) ? sanitize_input($_POST['search']) : '';
$searchTerms = explode(" ", $searchTerm); // Teilt searchTerm in einzelne Wörter auf
// Filter für die Suche
$filter = "(|";
//Überprüft ob $searchTerm zwei Wörter hat
if (count($searchTerms) == 2) {
    // Flexible Reihenfolge für zwei Wörter (givenName & sn)
    $filter .= "(&(givenname=$searchTerms[0]*)(sn=$searchTerms[1]*))";
    $filter .= "(&(sn=$searchTerms[0]*)(givenname=$searchTerms[1]*))";
} else {
    // Individuelle Suche für jedes Wort
    $attributesToSearch = array("givenname", "sn","aiusbcordlessinternal", "aiusbcordless", "title", "ou", "mail", "aiusbdienste", "costcenter");
    foreach ($attributesToSearch as $param) {
        $filter .= "($param=$searchTerm*)";
    }
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
$_SESSION['results'] = $results;
$results = isset($_SESSION['results']) ? $_SESSION['results'] : array();
header('Location: index.php');
?>