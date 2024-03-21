
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

    // Definiranje funkcije koja će izvlačiti ime stupca
    $columnName = function ($value) {
        return $value->name;
    };

    // Naziv baze podataka
    $dbName = "radovi";

    // Direktorij za pohranu backupa
    $dir = "backup/$dbName";

    // Provjera postoji li direktorij, ako ne, stvara se
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            die("<p>Can not create directory uploads.</p></body></html>");
        }
    }

    // Trenutno vrijeme
    $time = time();

    // Spajanje na bazu podataka
    $dbc = mysqli_connect("localhost", "root", "", $dbName)
    or die("<p>Ne možemo se spojiti na bazu $dbName.</p></body></html>");

    // Polje za spremanje imena generiranih datoteka
    $files = [];

    // Dohvaćanje popisa tablica iz baze podataka
    $r = mysqli_query($dbc, "SHOW TABLES");

    // Ako ima tablica u bazi podataka
    if (mysqli_num_rows($r) > 0) {
        echo "<p>Backup za bazu podataka '$dbName'.</p>";

        // Petlja kroz sve tablice
        while (list($table) = mysqli_fetch_array($r, MYSQLI_NUM)) {
            $q = "SELECT * FROM $table";

            // Dohvaćanje imena stupaca za tablicu
            $columns = array_map($columnName, $dbc->query($q)->fetch_fields());

            $r2 = mysqli_query($dbc, $q);

            // Ako postoje podaci u tablici
            if (mysqli_num_rows($r2) > 0) {
                $fileName = "{$table}_{$time}";

                // Otvaranje datoteke za pisanje
                if ($fp = fopen("$dir/$fileName.txt", "w9")) {
                    array_push($files, $fileName);

                    // Pisanje podataka u datoteku
                    while ($row = mysqli_fetch_array($r2, MYSQLI_NUM)) {
                        $rowText = "INSERT INTO $table (";

                        for ($i = 0; $i < count($columns); $i++) {
                            // Provjera je li zadnji element
                            if ($i + 1 != count($columns)) {
                                $rowText .= "$columns[$i], ";
                            } else {
                                $rowText .= "$columns[$i]";
                            }
                        }

                        $rowText .= ") VALUES (";

                        for ($i = 0; $i < count($row); $i++) {
                            // Provjera je li zadnji element
                            if ($i + 1 != count($row)) {
                                $rowText .= "'$row[$i]', ";
                            } else {
                                $rowText .= "'$row[$i]'";
                            }
                        }

                        $rowText .= ");\n";
                        fwrite($fp, $rowText);
                    }

                    // Zatvaranje datoteke
                    fclose($fp);

                    // Obavijest o pohrani tablice
                    echo "<p>Tablica '$table' je pohranjena.</p>";

                    // Stvaranje komprimirane verzije backupa
                    if ($fp = gzopen ("$dir/" . $fileName . "sql.gz", 'w9')) {
                        $content = file_get_contents("backup/radovi/$fileName.txt");
                        gzwrite($fp, $content);
                        unlink("backup/radovi/$fileName.txt");
                        gzclose($fp);

                        // Obavijest o kompresiji backupa
                        echo "<p>Tablica '$table' je sažeta.</p>";
                    } else {
                        // Obavijest o grešci pri kompresiji
                        echo "<p>Greška prilikom sažimanja tablice '$table'.</p>";
                    }
                } else {
                    // Obavijest o nemogućnosti otvaranja datoteke
                    echo "<p>Datoteka $dir/{$table}_{$time}.txt se ne može otvoriti.</p>";
                    break;
                }
            }
        }
    } else {
        // Obavijest o nedostatku tablica
        echo "<p>Baza $dbName ne sadrži tablice.</p>";
    }
    ?>
</body>
</html>
