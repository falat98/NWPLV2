<?php
// Početak sesije
session_start();

// Dobivanje imena datoteke iz GET zahtjeva
$file = $_GET['file'];

// Ključ za dekripciju
$decryption_key = md5('kljuc za enkripciju');

// Kriptografski algoritam
$cipher = "AES-128-CTR";

// Dodatne opcije
$options = 0;

// Inicijalizacijski vektor za dekripciju
$decryption_iv = $_SESSION['iv'];

// Dohvaćanje sadržaja enkriptirane datoteke
$contentEncrypted = file_get_contents("uploads/$file.txt");

// Dekodiranje enkriptiranog sadržaja iz base64
$contentDecrypted = base64_decode($contentEncrypted);

// Dekriptiranje podataka
$data = openssl_decrypt($contentDecrypted, $cipher, $decryption_key, $options, $decryption_iv);

// Postavljanje putanje do datoteke za pisanje dekriptiranih podataka
$file = "uploads/$file";

// Upisivanje dekriptiranih podataka u datoteku
file_put_contents($file, $data);

// Osiguranje da se statistika datoteke osvježi
clearstatcache();

// Ako datoteka postoji
if(file_exists($file)) {
    // Postavljanje HTTP zaglavlja za preuzimanje datoteke
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file, true);

    // Brisanje datoteke nakon preuzimanja
    unlink($file);

    // Prekid izvođenja skripte
    die();
}
?>
