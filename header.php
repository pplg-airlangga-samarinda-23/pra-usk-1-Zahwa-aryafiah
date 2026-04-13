<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}
$role = $_SESSION['Role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KasirPro</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" class="nav-brand">
            <span style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">KasirPro</span>
        </a>
        <div class="nav-links">
            <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
            
            <?php if ($role == 'Administrator' || $role == 'Petugas'): ?>
                <a href="produk.php" class="<?= basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'active' : '' ?>">Produk</a>
                <a href="pelanggan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'pelanggan.php' ? 'active' : '' ?>">Pelanggan</a>
                <a href="penjualan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'penjualan.php' ? 'active' : '' ?>">Kasir</a>
            <?php endif; ?>

            <?php if ($role == 'Administrator'): ?>
                <a href="laporan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>">Laporan</a>
                <a href="registrasi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'registrasi.php' ? 'active' : '' ?>">Registrasi Akun</a>
            <?php endif; ?>

            <a href="logout.php" class="btn btn-secondary" style="margin-left: 1rem;">Logout (<?= htmlspecialchars($_SESSION['Username']) ?>)</a>
        </div>
    </nav>
    <div class="container animate-fade-in" style="flex-grow: 1;">
