<?php
$passwort = '';
$fehler = '';
$laenge = 12;
$gesendet = isset($_POST['laenge']);

if ($gesendet) {
    $laenge = (int) $_POST['laenge'];

    // Zeichen-Pool je nach gewählten Optionen zusammenbauen
    $zeichen = '';
    if (isset($_POST['gross'])) {
        $zeichen .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    if (isset($_POST['klein'])) {
        $zeichen .= 'abcdefghijklmnopqrstuvwxyz';
    }
    if (isset($_POST['zahlen'])) {
        $zeichen .= '0123456789';
    }
    if (isset($_POST['sonder'])) {
        $zeichen .= '!@#$%&*?+-_=';
    }

    if ($laenge < 4 || $laenge > 64) {
        $fehler = 'Die Länge muss zwischen 4 und 64 liegen.';
    } elseif ($zeichen === '') {
        $fehler = 'Bitte mindestens eine Option auswählen.';
    } else {
        // Zufällige Zeichen aus dem Pool ziehen
        for ($i = 0; $i < $laenge; $i++) {
            $passwort .= $zeichen[random_int(0, strlen($zeichen) - 1)];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Passwort-Generator</title>
</head>
<body>
    <h1>Passwort-Generator</h1>

    <form method="post">
        Länge:
        <input type="number" name="laenge" min="4" max="64" value="<?php echo $laenge; ?>">
        <br><br>

        <label>
            <input type="checkbox" name="gross" <?php if (!$gesendet || isset($_POST['gross'])) { echo 'checked'; } ?>>
            Großbuchstaben (A-Z)
        </label><br>
        <label>
            <input type="checkbox" name="klein" <?php if (!$gesendet || isset($_POST['klein'])) { echo 'checked'; } ?>>
            Kleinbuchstaben (a-z)
        </label><br>
        <label>
            <input type="checkbox" name="zahlen" <?php if (!$gesendet || isset($_POST['zahlen'])) { echo 'checked'; } ?>>
            Zahlen (0-9)
        </label><br>
        <label>
            <input type="checkbox" name="sonder" <?php if (!$gesendet || isset($_POST['sonder'])) { echo 'checked'; } ?>>
            Sonderzeichen (!@#$%...)
        </label><br><br>

        <button type="submit">Passwort erzeugen</button>
    </form>

    <?php if ($fehler !== '') { ?>
        <p style="color: red;"><?php echo $fehler; ?></p>
    <?php } ?>

    <?php if ($passwort !== '') { ?>
        <h2>Dein Passwort</h2>
        <p style="font-family: monospace; font-size: 1.4em;"><?php echo htmlspecialchars($passwort); ?></p>
    <?php } ?>
</body>
</html>