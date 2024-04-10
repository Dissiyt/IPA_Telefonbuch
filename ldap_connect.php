<?php
require 'ldap_config.php';
global $ldapServer, $port, $user, $password, $searchBase, $attributes;
date_default_timezone_set('Europe/Berlin');
//Ldap activity log, loggt Ldap Aktivitäten wenn die Funktion aufgerufen wird.
function logLdapActivity($message_type, $message) {
    $log_file = './logs/ldap.log';
    $log_entry = date('Y-m-d H:i:s') . " - [$message_type] " . $message . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}
//Error Handler für LDAP Errors
function ldapErrorHandler($errno, $errstr)
{
    $error_message = "LDAP Error $errno: $errstr";
    $log_file = './logs/ldap.log';
    //Schreibt Errors in Logfile
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $error_message . "\n", FILE_APPEND);
}
set_error_handler('ldapErrorHandler');

//Verbingung zu LDAP Server
$ldapConn = ldap_connect($ldapServer, $port);

$ldapBind = ldap_bind($ldapConn, $user, $password);
if ($ldapBind) {
   logLdapActivity('SUCCESS', 'Connected to LDAP server.');
} else {
    return null;
}

?>