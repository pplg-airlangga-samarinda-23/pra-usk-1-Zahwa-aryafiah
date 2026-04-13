<?php
require 'header.php';
require 'koneksi.php';

// Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if (isset($_POST['add_cart'])) {
    $produk_id = $_POST['produk_id'];
    $jumlah = (int)$_POST['jumlah'];
    
    // Get produk info
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE ProdukID = ?");
    $stmt->execute([$produk_id]);
    $produk = $stmt->fetch();
    
    if ($produk) {
        if ($produk['Stok'] < $jumlah) {
            $error = "Stok tidak mencukupi untuk " . htmlspecialchars($produk['NamaProduk']);
        } else {
            // Check if already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['ProdukID'] == $produk_id) {
                    // Check if total requested exceeds stock
                    if ($item['Jumlah'] + $jumlah > $produk['Stok']) {
                        $error = "Stok tidak mencukupi jika ditambah.";
                    } else {
                        $item['Jumlah'] += $jumlah;
                        $item['Subtotal'] = $item['Jumlah'] * $item['Harga'];
                    }
                    $found = true; break;
                }
            }
            if (!$found && !isset($error)) {
                $_SESSION['cart'][] = [
                    'ProdukID' => $produk_id,
                    'NamaProduk' => $produk['NamaProduk'],
                    'Harga' => $produk['Harga'],
                    'Jumlah' => $jumlah,
                    'Subtotal' => $jumlah * $produk['Harga']
                ];
            }
            if (!isset($error)) {
                header("Location: penjualan.php");
                exit();
            }
        }
    }
}

// Handle Clear Cart Item
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        // reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header("Location: penjualan.php");
    exit();
}

// Handle Checkout
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $error = "Keranjang belanja kosong!";
    } else {
        $pelanggan_id = $_POST['pelanggan_id'];
        if (empty($pelanggan_id)) {
            $error = "Silakan pilih pelanggan!";
        } else {
            // Calculate Total
            $totalHarga = 0;
            foreach ($_SESSION['cart'] as $item) {
                $totalHarga += $item['Subtotal'];
            }
            
            try {
                $pdo->beginTransaction();
                
                // Insert Penjualan
                $stmt = $pdo->prepare("INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID) VALUES (DATE('now'), ?, ?)");
                $stmt->execute([$totalHarga, $pelanggan_id]);
                $penjualan_id = $pdo->lastInsertId();
                
                // Insert Detail & Reduce Stock
                foreach ($_SESSION['cart'] as $item) {
                    $stmt_detail = $pdo->prepare("INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) VALUES (?, ?, ?, ?)");
                    $stmt_detail->execute([$penjualan_id, $item['ProdukID'], $item['Jumlah'], $item['Subtotal']]);
                    
                    $stmt_stock = $pdo->prepare("UPDATE produk SET Stok = Stok - ? WHERE ProdukID = ?");
                    $stmt_stock->execute([$item['Jumlah'], $item['ProdukID']]);
                }
                
                $pdo->commit();
                $_SESSION['cart'] = []; // Clear cart
                header("Location: penjualan.php?sukses=" . $penjualan_id);
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Gagal memproses transaksi: " . $e->getMessage();
            }
        }
    }
}

// Get Lists
$pelanggan_list = $pdo->query("SELECT * FROM pelanggan ORDER BY NamaPelanggan")->fetchAll();
$produk_list = $pdo->query("SELECT * FROM produk WHERE Stok > 0 ORDER BY NamaProduk")->fetchAll();
?>

<div class="glass-panel">
    <h2>Transaksi Kasir (Penjualan)</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['sukses'])): ?>
        <div class="alert alert-success">
            Transaksi berhasil disimpan dengan ID #<?= $_GET['sukses'] ?>! 
            <a href="dashboard.php" style="color:var(--primary-hover); text-decoration:underline;">Kembali ke dashboard</a>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        <!-- Area Form Input -->
        <div style="flex: 1; min-width: 300px;">
            <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem;">
                <h3>1. Pilih Produk</h3>
                <form action="" method="POST" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label for="produk_id">Pilih Produk</label>
                        <select id="produk_id" name="produk_id" class="form-control" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($produk_list as $prod): ?>
                                <option value="<?= $prod['ProdukID'] ?>">
                                    <?= htmlspecialchars($prod['NamaProduk']) ?> (Stok: <?= $prod['Stok'] ?>) - Rp <?= number_format($prod['Harga'], 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah Beli</label>
                        <input type="number" id="jumlah" name="jumlah" class="form-control" value="1" min="1" required>
                    </div>
                    <button type="submit" name="add_cart" class="btn btn-primary w-full">Tambah ke Keranjang  🛒</button>
                </form>
            </div>
            
            <div class="glass-panel" style="padding: 1.5rem;">
                <h3>3. Selesaikan Transaksi</h3>
                <form action="" method="POST" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label for="pelanggan_id">Pilih Pelanggan</label>
                        <select id="pelanggan_id" name="pelanggan_id" class="form-control" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php foreach ($pelanggan_list as $pel): ?>
                                <option value="<?= $pel['PelangganID'] ?>"><?= htmlspecialchars($pel['NamaPelanggan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small style="display:block; margin-top:0.5rem;"><a href="pelanggan.php" style="color:var(--text-secondary);">+ Tambah Pelanggan Baru</a></small>
                    </div>
                    <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item) $total += $item['Subtotal'];
                    ?>
                    <button type="submit" name="checkout" class="btn btn-secondary w-full" style="background:var(--secondary-color); font-weight:bold; font-size:1.1rem; padding: 1rem;">
                        💳 Bayar (Rp <?= number_format($total, 0, ',', '.') ?>)
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Area Keranjang -->
        <div style="flex: 2; min-width: 400px;">
            <div class="glass-panel" style="padding: 1.5rem; height: 100%;">
                <h3>2. Keranjang Belanja</h3>
                <div class="table-container" style="margin-top: 1rem;">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($_SESSION['cart'])): ?>
                                <tr><td colspan="5" style="text-align:center;">Keranjang kosong.</td></tr>
                            <?php else: ?>
                                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($item['NamaProduk']) ?></strong></td>
                                        <td>Rp <?= number_format($item['Harga'], 0, ',', '.') ?></td>
                                        <td><?= $item['Jumlah'] ?></td>
                                        <td style="color: var(--secondary-color); font-weight:bold;">Rp <?= number_format($item['Subtotal'], 0, ',', '.') ?></td>
                                        <td>
                                            <a href="penjualan.php?remove=<?= $index ?>" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">X</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($_SESSION['cart'])): ?>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align:right; font-size:1.1rem;">TOTAL:</th>
                                <th colspan="2" style="font-size:1.2rem; color:var(--text-primary);">Rp <?= number_format($total, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
