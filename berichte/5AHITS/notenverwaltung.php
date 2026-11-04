<?php
$noten = [];
$fehler = '';

// Bisherige Noten kommen ins Formular zurück
if (isset($_POST['noten'])) {
    foreach ($_POST['noten'] as $eingabe) {
        $noten[] = (float) $eingabe;
    }
}

// Neue Note prüfen und hinzufügen
if (isset($_POST['note']) && $_POST['note'] != '') {
    $neueNote = (float)$_POST['note'];

    if ($neueNote > 5) {
        $fehler = 'Die Note darf nicht größer als 5 sein!';
    } else {
        $noten[] = $neueNote;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Notenverwaltung</title>
</head>
<body>
    <h1>Notenverwaltung</h1>

    <form method="post">
        <?php foreach ($noten as $note) { ?>
            <input type="hidden" name="noten[]" value="<?php echo $note; ?>">
        <?php } ?>

        Note:
        <input type="text" name="note" size="4" autofocus>
        <button type="submit">Hinzufügen</button>
        <a href="notenverwaltung.php">Alle löschen</a>
    </form>

    <?php if ($fehler !== '') { ?>
        <p style="color: red;"><?php echo $fehler; ?></p>
    <?php } ?>

    <?php
    if (count($noten) > 0) {
        $durchschnitt = array_sum($noten) / count($noten);
        $beste = min($noten);
        $schlechteste = max($noten);
        $positive = 0;
        foreach ($noten as $note) {
            if ($note <= 4) {
                $positive++;
            }
        }
    ?>
        <h2>Auswertung</h2>
        <table border="1" cellpadding="5">
            <tr><th>Noten</th><td><?php echo implode(', ', $noten); ?></td></tr>
            <tr><th>Durchschnitt</th><td><?php echo round($durchschnitt, 2); ?></td></tr>
            <tr><th>Beste Note</th><td><?php echo $beste; ?></td></tr>
            <tr><th>Schlechteste Note</th><td><?php echo $schlechteste; ?></td></tr>
            <tr><th>Positive Noten</th><td><?php echo $positive; ?></td></tr>
        </table>
    <?php } ?>
</body>
</html>