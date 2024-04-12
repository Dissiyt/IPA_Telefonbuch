<?php
//LDAP Zugangsdaten
$ldapServer = 'ldap://ictidmldplp02.uhbs.ch';
$port = '636';
$user = '';
$password = '';
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

