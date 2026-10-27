<?php
// Script Setup & Inisialisasi Database db_lovepage
$ports = [3307, 3306];
$host = '127.0.0.1';
$user = 'root';
$password = '';
$connected = false;
$pdo = null;
$selectedPort = null;

foreach ($ports as $port) {
    try {
        $pdo = new PDO("mysql:host=$host;port=$port", $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 2
        ]);
        $selectedPort = $port;
        $connected = true;
        echo "[+] Berhasil terhubung ke MariaDB/MySQL di port $port\n";
        break;
    } catch (PDOException $e) {
        // coba port berikutnya
    }
}

if (!$connected) {
    die("[-] Gagal terhubung ke MySQL. Pastikan MySQL berjalan di port 3306 atau 3307.\n");
}

try {
    $sqlFile = __DIR__ . '/database/db_lovepage.sql';
    if (!file_exists($sqlFile)) {
        die("[-] File SQL tidak ditemukan di: $sqlFile\n");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    // Jalankan per statement
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `db_lovepage` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `db_lovepage`;");
    
    // Split queries
    $queries = explode(';', $sqlContent);
    foreach ($queries as $query) {
        $trimmed = trim($query);
        if (!empty($trimmed)) {
            $pdo->exec($trimmed);
        }
    }
    
    echo "[+] Database `db_lovepage` dan tabel-tabel berhasil diinisialisasi!\n";
    echo "[+] Port aktif tersimpan: $selectedPort\n";
} catch (Exception $e) {
    echo "[-] Terjadi error saat inisialisasi: " . $e->getMessage() . "\n";
}
