<?php
session_set_cookie_params(0);
session_start();
$results = isset($_SESSION['results']) ? $_SESSION['results'] : array();

$limit = 20;
$totalResults = count($results);
$totalPages = ceil($totalResults / $limit);
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Berechnung des Startindex für array_slice()
$startIndex = ($page - 1) * $limit;

// Erhalten der Ergebnisse für die aktuelle Seite
$pageResults = array_slice($results, $startIndex, $limit);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Importieren von Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" type="image/svg+xml" href="assets/USB_Identifier.svg">
    <link rel="stylesheet" type="text/css" href="style/main.css" />

    <title>Telefonbuch</title>
</head>
<body>
<!-- Importieren von Bootstrap und Bootstrap jquerry-->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<div class="navbar">
    <nav class="navbar navbar-expand-lg navbar-light">
        <a href="https://intranet.uhbs.ch/">
            <img class="navbar-brand" src="assets/UBS_Logo.png" width="220" height="70" class="d-inline-block align-top " id="logo-desktop"/>
            <img class="navbar-brand" src="assets/USB_Identifier_white.svg" width="62" height="50" class="d-none d-sm-inline-block align-top" id="logo-mobile" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link active" href="./index.php">USB Telefonbuch <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link" href="https://usbch.sharepoint.com/sites/0031" target="_blank">UKBB Telefonbuch</a>
                <a class="nav-item nav-link" href="https://intranet.uhbs.ch/fileadmin/user_upload/6_bereiche/dict/pagernummern_notfall.pdf" target="_blank">Notpagerliste</a>
            </div>
        </div>
    </nav>
</div>

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
    <?php if (isset($_SESSION['results'])): ?>
    <?php if (count($results) <= 1): ?>
        <p class="text-danger">Keine Ergebnisse gefunden.</p>
    <?php else: ?>
    <h2 class="text-start">Ergebnisse</h2>
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
        <?php foreach ($pageResults as $key => $entry): ?>
            <?php if ($key === 'count') continue; ?>
            <tr>
                <!-- Ausgabe der Daten aus dem LDAP, Prüfung ob die einzelnen Parameter Daten enthalten.-->
                <td><?php echo isset($entry['sn'][0]) ? $entry['sn'][0] : ''; ?></td>
                <td><?php echo isset($entry['givenname'][0]) ? $entry['givenname'][0] : ''; ?></td>
                <td>    <?php
                    if (isset($entry['aiusbcordlessinternal'][0])) {
                        echo '<a href="webextel://' . $entry['aiusbcordlessinternal'][0] . '">' . $entry['aiusbcordlessinternal'][0] . '</a>';
                    } elseif (isset($entry['aiusbtelinternal'][0])) {
                        echo '<a href="webextel://' . $entry['aiusbtelinternal'][0] . '">' . $entry['aiusbtelinternal'][0] . '</a>';
                    } else {
                        echo '';
                    }
                    ?></td>
                <td><?php if (isset($entry['mail'][0])) {
                        echo '<a href="mailto:' . $entry['mail'][0] . '">' . strtolower($entry['mail'][0]) . '</a>';
                    } else {
                        echo '';
                    } ?></td>
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
    <div class="pagination">
        <?php if ($totalPages > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="pager-link pager-arrow">&lsaquo;</a>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <a href="?page=<?php echo $i; ?>" class="pager-link <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <!-- Wenn eine Seite 3 Positionen von der aktiven Seite entfernt ist, wird eine ellipsis (....) eingefügt -->
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                    <span class="pager-ellipsis">...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <a href="?page=<?php echo $page + 1; ?>" class="pager-link pager-arrow">&rsaquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
