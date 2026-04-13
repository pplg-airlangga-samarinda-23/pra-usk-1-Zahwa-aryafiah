<?php
require 'header.php';
require 'koneksi.php';

// Proteksi, re-verify role is Administrator
if ($_SESSION['Role'] != 'Administrator') {
    echo "<script>alert('Akses Ditolak! Anda bukan Administrator.'); window.location='dashboard.php';</script>";
    exit();
}

$laporan = $pdo->query("
    SELECT p.PenjualanID, p.TanggalPenjualan, p.TotalHarga, pel.NamaPelanggan 
    FROM penjualan p
    JOIN pelanggan pel ON p.PelangganID = pel.PelangganID
    ORDER BY p.PenjualanID DESC
")->fetchAll();
?>

<div class="glass-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Laporan Transaksi Penjualan</h2>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Laporan</button>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Nama Pelanggan</th>
                    <th>Menu Pembelian</th>
                    <th>Total Belanja</th>
                </tr>
            </thead>
            <tbody>
                <?php $grand_total = 0; ?>
                <?php foreach ($laporan as $row): ?>
                <tr>
                    <td>#<?= $row['PenjualanID'] ?></td>
                    <td><?= date('d M Y', strtotime($row['TanggalPenjualan'])) ?></td>
                    <td><?= htmlspecialchars($row['NamaPelanggan']) ?></td>
                    <td>
                        <ul style="padding-left: 1rem; margin: 0; font-size: 0.875rem; color: var(--text-secondary);">
                            <?php
                            $stmt_detail = $pdo->prepare("
                                SELECT d.JumlahProduk, pr.NamaProduk, d.Subtotal 
                                FROM detailpenjualan d
                                JOIN produk pr ON d.ProdukID = pr.ProdukID
                                WHERE d.PenjualanID = ?
                            ");
                            $stmt_detail->execute([$row['PenjualanID']]);
                            $details = $stmt_detail->fetchAll();
                            foreach ($details as $d):
                            ?>
                                <li><?= $d['JumlahProduk'] ?>x <?= htmlspecialchars($d['NamaProduk']) ?> (Rp <?= number_format($d['Subtotal'], 0, ',', '.') ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td style="font-weight: bold; color: var(--secondary-color);">Rp <?= number_format($row['TotalHarga'], 0, ',', '.') ?></td>
                </tr>
                <?php $grand_total += $row['TotalHarga']; ?>
                <?php endforeach; ?>
                <?php if (empty($laporan)): ?>
                <tr><td colspan="5" style="text-align: center;">Belum ada history transaksi.</td></tr>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($laporan)): ?>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: right; font-size: 1.1rem; color: white;">TOTAL PENDAPATAN KESELURUHAN:</th>
                    <th style="font-size: 1.2rem; color: var(--secondary-color);">Rp <?= number_format($grand_total, 0, ',', '.') ?></th>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<style>
@media print {
    body { background: white; color: black; }
    .navbar, .btn { display: none !important; }
    .glass-panel { 
        backdrop-filter: none; 
        background: white; 
        border: none; 
        box-shadow: none; 
        padding: 0; 
    }
    table, th, td { border: 1px solid #ccc; color: black; }
    th { background: #f0f0f0; color: black; }
    .alert { display: none; }
}
</style>

<?php require 'footer.php'; ?>
