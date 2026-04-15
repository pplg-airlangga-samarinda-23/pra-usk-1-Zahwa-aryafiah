<?php
require 'header.php';
require 'koneksi.php';

// Generate stats
$stats = [
    'produk' => $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn(),
    'pelanggan' => $pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn(),
    'penjualan_hari_ini' => $pdo->query("SELECT COUNT(*) FROM penjualan WHERE TanggalPenjualan = DATE('now')")->fetchColumn(),
    'pendapatan_hari_ini' => $pdo->query("SELECT SUM(TotalHarga) FROM penjualan WHERE TanggalPenjualan = DATE('now')")->fetchColumn() ?? 0,
];

// Reformat currency
$pendapatan_formatted = "Rp " . number_format($stats['pendapatan_hari_ini'], 0, ',', '.');
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="margin-bottom: 0.5rem;">Selamat Datang, <?= htmlspecialchars($_SESSION['Username']) ?>!</h1>
        <p>Anda login sebagai <strong style="color: var(--secondary-color);"><?= htmlspecialchars($_SESSION['Role']) ?></strong>.</p>
    </div>
    <div style="text-align: right;">
        <p style="font-size: 1.25rem; font-weight: 600; color: white;"><?= date('l, d F Y') ?></p>
    </div>
</div>

<div class="metric-grid">
    <div class="glass-panel metric-card">
        <div class="metric-title">Total Belanja</div>
        <div class="metric-value"><?= $stats['produk'] ?></div>
        <div class="metric-icon">📦</div>
    </div>
    <div class="glass-panel metric-card">
        <div class="metric-title">Jumlah Pelanggan</div>
        <div class="metric-value"><?= $stats['pelanggan'] ?></div>
        <div class="metric-icon">👥</div>
    </div>
    <div class="glass-panel metric-card">
        <div class="metric-title">Transaksi Hari Ini</div>
        <div class="metric-value"><?= $stats['penjualan_hari_ini'] ?></div>
        <div class="metric-icon">🛒</div>
    </div>
    <div class="glass-panel metric-card">
        <div class="metric-title">Pendapatan Hari Ini</div>
        <div class="metric-value"><?= $pendapatan_formatted ?></div>
        <div class="metric-icon">💵</div>
    </div>
</div>

<div class="glass-panel">
    <h2>Aksi Cepat</h2>
    <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
        <a href="penjualan.php" class="btn btn-primary">➕ Transaksi Baru</a>
        <a href="produk.php" class="btn btn-secondary">📦 Kelola Produk</a>
        <a href="pelanggan.php" class="btn btn-secondary">👥 Kelola Pelanggan</a>
        <?php if ($role == 'Administrator'): ?>
            <a href="laporan.php" class="btn btn-secondary">📊 Lihat Laporan</a>
        <?php endif; ?>
    </div>
</div>

<?php require 'footer.php'; ?>
