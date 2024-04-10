<?php
session_set_cookie_params(0);
session_start();
$results = isset($_SESSION['results']) ? $_SESSION['results'] : array();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- Importieren von Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Telefonbuch</title>
</head>
<body>
<!-- Importieren von Bootstrap und Bootstrap jquerry-->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<div class="headers">
    <h1 class="text-center">Telefonbuch des USB</h1>
</div>

<!-- Formular für die Suche-->
<div class="container-fluid d-flex justify-content-center">
    <form method="post" class="sform" action="ldap_search.php">
        <br>
        <input type="text" id="search" name="search" class="search" placeholder="Suche nach Name, Abteilung, Dienstleistung, Telefon, Kostenstelle">
        <input type="submit" value="Suchen" class="sbutton">
    </form>
</div>

<div class="results">
    <h2>Ergebnisse</h2>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Vorname</th>
            <th scope="col">Telefon</th>
            <th scope="col">Mail</th>
            <th scope="col">Teams</th>
            <th scope="col">Abteilung</th>
            <th scope="col">Funktion</th>
        </tr>
        </thead>
        <tbody>
        <!-- foreach Schleife für die Ergebnisse für jedes Ergebniss wird eine Tabelle generiert-->
        <?php foreach ($results as $key => $entry): ?>
            <?php if ($key === 'count') continue; ?>
            <tr>
                <!-- Ausgabe der Daten aus dem LDAP, Prüfung ob die einzelnen Parameter Daten enthalten.-->
                <td><?php echo isset($entry['sn'][0]) ? $entry['sn'][0] : ''; ?></td>
                <td><?php echo isset($entry['givenname'][0]) ? $entry['givenname'][0] : ''; ?></td>
                <td>    <?php echo isset($entry['aiusbcordlessinternal'][0]) ? $entry['aiusbcordlessinternal'][0] : ''; ?></td>
                <td><?php echo isset($entry['mail'][0]) ? $entry['mail'][0] : ''; ?></td>
                <td>Open in Teams</td>
                <!-- Prüfung ob ou und aiusbdienste Daten enthalten-->
                <td><?php echo isset($entry['ou'][0]) ? $entry['ou'][0] : ''; ?>
                    <?php  if (isset($entry['ou'][0]) && isset($entry['aiusbdienste'][0])) {
                        echo "|";
                    } ?>
                    <?php echo isset($entry['aiusbdienste'][0]) ? $entry['aiusbdienste'][0] : ''; ?>
                </td>
                <td><?php echo isset($entry['title'][0]) ? $entry['title'][0] : ''; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
