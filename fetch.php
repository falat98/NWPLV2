<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    // Početak sesije
    session_start();

    // Funkcija koja provjerava je li datoteka ekstenzije .txt
    $txtFile = function ($value) {
        return (pathinfo($value, PATHINFO_EXTENSION) === 'txt');
    };

    // Provjera postoji li direktorij "uploads/"
    if (!is_dir("uploads/")) {
        echo "<p>No files for decrypting.</p>";
        die();
    }

    // Dobivanje popisa datoteka u direktoriju "uploads/" filtriranih prema ekstenziji .txt
    $files = array_diff(scandir("uploads/"), array('..', '.'));
    $files = array_filter($files, $txtFile);

    // Ako nema datoteka za dekripciju
    if (count($files) === 0) {
        echo "<p>No files for decrypting</p>";
    } else {
        // Ispisivanje popisa datoteka za dekripciju
        echo "<ul>";
        foreach ($files as $file) {
            $nameWithoutExt = substr($file, 0, strlen($file) - 4);

            echo "<li> <a href=\"download.php?file=$nameWithoutExt\">$nameWithoutExt</a></li>";
        }
        echo "</ul>";
    }
    ?>
</body>
</html>




