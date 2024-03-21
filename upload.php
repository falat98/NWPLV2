<?php
// Početak sesije
session_start();

// Naziv datoteke
$filename = $_FILES['file']['name'];

// Lokacija spremanja datoteke
$location = "uploads/" . $filename;

// Ekstenzija datoteke
$imageFileType = pathinfo($location, PATHINFO_EXTENSION);

// Dopuštene ekstenzije
$valid_extensions = array("jpg", "jpeg", "png", "pdf");

// Provjera valjanosti formata datoteke
if (!in_array(strtolower($imageFileType), $valid_extensions)) {
    echo "<p>Invalid format ($imageFileType).</p>";
    die();
}

// Dohvaćanje sadržaja datoteke
$content = file_get_contents($_FILES['file']['tmp_name']);

// Ključ za enkripciju
$encryption_key = md5('kljuc za enkripciju');

// Kriptografski algoritam
$cipher = "AES-128-CTR";

// Dohvaćanje duljine inicijalizacijskog vektora
$iv_length = openssl_cipher_iv_length($cipher);

// Dodatne opcije
$options = 0;

// Generiranje slučajnog inicijalizacijskog vektora
$encryption_iv = random_bytes($iv_length);

// Enkripcija podataka
$encrypted = openssl_encrypt($content, $cipher, $encryption_key, $options, $encryption_iv);

// Kodiranje enkriptiranih podataka u base64
$encryptedData = base64_encode($encrypted);

// Spremanje inicijalizacijskog vektora u sesiju
$_SESSION['iv'] = $encryption_iv;

// Izdvajanje naziva datoteke bez ekstenzije
$fileNameWithoutExt = substr($filename, 0, strpos($filename, "."));

// Provjera postoji li direktorij "uploads/"
if (!is_dir("uploads/")) {
    if (!mkdir("uploads/", 0777, true)) {
        die("<p>Can not create directory $dir.</p>");
    }
}

// Naziv datoteke na serveru s dodanom ekstenzijom .txt
$fileNameOnServer = "uploads/${fileNameWithoutExt}.$imageFileType.txt";

// Upisivanje enkriptiranih podataka u datoteku
file_put_contents($fileNameOnServer, $encryptedData);

// Obavijest o uspješnom uploadu datoteke
echo "File uploaded successfully";
?>
