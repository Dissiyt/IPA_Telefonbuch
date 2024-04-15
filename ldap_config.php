<?php
//Import von Dotenv und laden der .env Datei
require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/ldap.env');

//LDAP Zugangsdaten
$ldapServer = 'ldap://ictidmldplp02.uhbs.ch';
$port = '636';
$user =  $_ENV['LDAP_USER'];
$password = $_ENV['LDAP_PASSWORD'];
//LDAP Suchparameter und Attribute
$searchBase = 'ou=IdentityActive5,ou=SHADOW,o=AI';
$attributes = array("dn",
    "aigender",
    "personalTitle",
    "aiusbtelinternal",
    "telephonenumber",
    "aiusbfaxinternal",
    "aiusbcordlessinternal",
    "aiusbcordless",
    "aiusbabtdie",
    "mail",
    "aiusbpagerinternal",
    "pager",
    "aiusbdienste",
    "personaltitle",
    "costcenter",
    "title",
    "ou",
    "aiusbtelLastname",
    "aiusbtelFirstname",
    "sn",
    "givenname",
    "aiuniquepersonid");
?>

