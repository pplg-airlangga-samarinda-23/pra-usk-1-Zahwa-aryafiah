<?php
require 'header.php';
require 'koneksi.php';

// Handle Delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE PelangganID = ?");
    if ($stmt->execute([$id])) {
        header("Location: pelanggan.php?pesan=hapus_sukses");
        exit();
    }
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $nomor = $_POST['nomor_telepon'];
    
    if (isset($_POST['id']) && $_POST['id'] != '') {
        $stmt = $pdo->prepare("UPDATE pelanggan SET NamaPelanggan = ?, Alamat = ?, NomorTelepon = ? WHERE PelangganID = ?");
        $stmt->execute([$nama, $alamat, $nomor, $_POST['id']]);
        $pesan = 'edit_sukses';
    } else {
        $stmt = $pdo->prepare("INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $alamat, $nomor]);
        $pesan = 'tambah_sukses';
    }
    header("Location: pelanggan.php?pesan=$pesan");
    exit();
}

$pelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY PelangganID DESC")->fetchAll();
?>

<div class="glass-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Kelola Pelanggan</h2>
        <button onclick="document.getElementById('modalTambah').classList.add('show')" class="btn btn-primary">➕ Tambah Pelanggan</button>
    </div>

    <?php if (isset($_GET['pesan'])): ?>
        <div class="alert alert-success">
            <?= $_GET['pesan'] == 'tambah_sukses' ? 'Pelanggan berhasil ditambahkan!' : ($_GET['pesan'] == 'edit_sukses' ? 'Pelanggan berhasil diubah!' : 'Pelanggan berhasil dihapus!') ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>No Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pelanggan as $p): ?>
                <tr>
                    <td><?= $p['PelangganID'] ?></td>
                    <td><?= htmlspecialchars($p['NamaPelanggan']) ?></td>
                    <td><?= htmlspecialchars($p['Alamat']) ?></td>
                    <td><?= htmlspecialchars($p['NomorTelepon']) ?></td>
                    <td>
                        <button onclick="editModal(<?= $p['PelangganID'] ?>, '<?= addslashes($p['NamaPelanggan']) ?>', '<?= addslashes($p['Alamat']) ?>', '<?= addslashes($p['NomorTelepon']) ?>')" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Edit</button>
                        <a href="pelanggan.php?hapus=<?= $p['PelangganID'] ?>" onclick="return confirm('Yakin ingin menghapus pelanggan ini?')" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($pelanggan)): ?>
                <tr><td colspan="5" style="text-align: center;">Belum ada data pelanggan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalTambah" class="modal">
    <div class="glass-panel modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Tambah Pelanggan</h2>
            <button onclick="closeModalCustom()" class="close-btn">&times;</button>
        </div>
        <form action="" method="POST">
            <input type="hidden" id="form-id" name="id">
            <div class="form-group">
                <label for="nama_pelanggan">Nama Lengkap</label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="nomor_telepon">Nomor Telepon</label>
                <input type="text" id="nomor_telepon" name="nomor_telepon" class="form-control" required>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary w-full">Simpan</button>
                <button type="button" onclick="closeModalCustom()" class="btn btn-secondary w-full">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function editModal(id, nama, alamat, nomor) {
    document.getElementById('modalTitle').innerText = 'Edit Pelanggan';
    document.getElementById('form-id').value = id;
    document.getElementById('nama_pelanggan').value = nama;
    document.getElementById('alamat').value = alamat;
    document.getElementById('nomor_telepon').value = nomor;
    document.getElementById('modalTambah').classList.add('show');
}
function closeModalCustom() {
    document.getElementById('modalTambah').classList.remove('show');
    document.getElementById('form-id').value = '';
    document.getElementById('nama_pelanggan').value = '';
    document.getElementById('alamat').value = '';
    document.getElementById('nomor_telepon').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Pelanggan';
}
</script>

<?php require 'footer.php'; ?>
