<?php
require 'header.php';
require 'koneksi.php';

// Handle Delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM produk WHERE ProdukID = ?");
    if ($stmt->execute([$id])) {
        echo "<script>window.location='produk.php?pesan=hapus_sukses';</script>";
    }
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    if (isset($_POST['id']) && $_POST['id'] != '') {
        // Update
        $stmt = $pdo->prepare("UPDATE produk SET NamaProduk = ?, Harga = ?, Stok = ? WHERE ProdukID = ?");
        $stmt->execute([$nama, $harga, $stok, $_POST['id']]);
        $pesan = 'edit_sukses';
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO produk (NamaProduk, Harga, Stok) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $harga, $stok]);
        $pesan = 'tambah_sukses';
    }
    header("Location: produk.php?pesan=$pesan");
    exit();
}

// Get Data
$produk = $pdo->query("SELECT * FROM produk ORDER BY ProdukID DESC")->fetchAll();
?>

<div class="glass-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Kelola Produk / Stok Barang</h2>
        <button onclick="document.getElementById('modalTambah').classList.add('show')" class="btn btn-primary">➕ Tambah Produk</button>
    </div>

    <?php if (isset($_GET['pesan'])): ?>
        <div class="alert alert-success">
            <?= $_GET['pesan'] == 'tambah_sukses' ? 'Produk berhasil ditambahkan!' : ($_GET['pesan'] == 'edit_sukses' ? 'Produk berhasil diubah!' : 'Produk berhasil dihapus!') ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produk as $p): ?>
                <tr>
                    <td><?= $p['ProdukID'] ?></td>
                    <td><?= htmlspecialchars($p['NamaProduk']) ?></td>
                    <td>Rp <?= number_format($p['Harga'], 0, ',', '.') ?></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; background: <?= $p['Stok'] > 5 ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' ?>; color: <?= $p['Stok'] > 5 ? '#6EE7B7' : '#FCA5A5' ?>; font-weight: bold;">
                            <?= $p['Stok'] ?>
                        </span>
                    </td>
                    <td>
                        <button onclick="editModal(<?= $p['ProdukID'] ?>, '<?= addslashes($p['NamaProduk']) ?>', <?= $p['Harga'] ?>, <?= $p['Stok'] ?>)" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Edit</button>
                        <a href="produk.php?hapus=<?= $p['ProdukID'] ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($produk)): ?>
                <tr><td colspan="5" style="text-align: center;">Belum ada data produk.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="modalTambah" class="modal">
    <div class="glass-panel modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Tambah Produk</h2>
            <button onclick="closeModalCustom()" class="close-btn">&times;</button>
        </div>
        <form action="" method="POST">
            <input type="hidden" id="form-id" name="id">
            <div class="form-group">
                <label for="nama_produk">Nama Produk</label>
                <input type="text" id="nama_produk" name="nama_produk" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="stok">Stok Awal</label>
                <input type="number" id="stok" name="stok" class="form-control" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary w-full">Simpan</button>
                <button type="button" onclick="closeModalCustom()" class="btn btn-secondary w-full">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function editModal(id, nama, harga, stok) {
    document.getElementById('modalTitle').innerText = 'Edit Produk';
    document.getElementById('form-id').value = id;
    document.getElementById('nama_produk').value = nama;
    document.getElementById('harga').value = harga;
    document.getElementById('stok').value = stok;
    document.getElementById('modalTambah').classList.add('show');
}
function closeModalCustom() {
    document.getElementById('modalTambah').classList.remove('show');
    document.getElementById('form-id').value = '';
    document.getElementById('nama_produk').value = '';
    document.getElementById('harga').value = '';
    document.getElementById('stok').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Produk';
}
</script>

<?php require 'footer.php'; ?>
