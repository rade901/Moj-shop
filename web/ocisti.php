<?php
// Ispravljena putanja bez dvostrukog 'web'
$dir = __DIR__ . '/uploads/products/';

if (is_dir($dir)) {
    $files = glob($dir . '*'); // Dohvati sve datoteke iz mape
    foreach ($files as $file) {
        if (is_file($file)) {
            if (unlink($file)) {
                echo "Uspješno obrisano: " . basename($file) . "<br>";
            } else {
                echo "Neuspješno brisanje (Provjerite dozvole): " . basename($file) . "<br>";
            }
        }
    }
} else {
    echo "Mapa ne postoji na putanji: " . $dir;
}
