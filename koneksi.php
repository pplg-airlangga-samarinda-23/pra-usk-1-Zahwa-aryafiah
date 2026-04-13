<?php
$dbFile = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialisasi Tabel jika belum ada
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            UserID INTEGER PRIMARY KEY AUTOINCREMENT,
            Username VARCHAR(50) UNIQUE NOT NULL,
            Password VARCHAR(255) NOT NULL,
            Role TEXT CHECK(Role IN ('Administrator', 'Petugas')) NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS pelanggan (
            PelangganID INTEGER PRIMARY KEY AUTOINCREMENT,
            NamaPelanggan VARCHAR(255) NOT NULL,
            Alamat TEXT NOT NULL,
            NomorTelepon VARCHAR(15) NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS produk (
            ProdukID INTEGER PRIMARY KEY AUTOINCREMENT,
            NamaProduk VARCHAR(255) NOT NULL,
            Harga DECIMAL(10,2) NOT NULL,
            Stok INTEGER NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS penjualan (
            PenjualanID INTEGER PRIMARY KEY AUTOINCREMENT,
            TanggalPenjualan DATE NOT NULL,
            TotalHarga DECIMAL(10,2) NOT NULL,
            PelangganID INTEGER NOT NULL,
            FOREIGN KEY (PelangganID) REFERENCES pelanggan(PelangganID)
        );
        
        CREATE TABLE IF NOT EXISTS detailpenjualan (
            DetailID INTEGER PRIMARY KEY AUTOINCREMENT,
            PenjualanID INTEGER NOT NULL,
            ProdukID INTEGER NOT NULL,
            JumlahProduk INTEGER NOT NULL,
            Subtotal DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (PenjualanID) REFERENCES penjualan(PenjualanID),
            FOREIGN KEY (ProdukID) REFERENCES produk(ProdukID)
        );
    ");

    // Insert Default User if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $petugasPass = password_hash('petugas123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (Username, Password, Role) VALUES ('admin', '$adminPass', 'Administrator')");
        $pdo->exec("INSERT INTO users (Username, Password, Role) VALUES ('petugas', '$petugasPass', 'Petugas')");
    }

} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
