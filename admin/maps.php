<?php
session_start();
include "../koneksi.php";
include "../function.php";

// Auth check
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$error   = '';
$success = '';

// ── TAMBAH MAP ──────────────────────────────
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_map']));

    if (empty($nama)) {
        $error = 'Nama map wajib diisi.';
    } else {
        $cek = mysqli_query($conn, "SELECT * FROM maps WHERE nama_map = '$nama'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Map dengan nama tersebut sudah ada.';
        } else {
            mysqli_query($conn, "INSERT INTO maps (nama_map) VALUES ('$nama')");
            $success = 'Map berhasil ditambahkan.';
        }
    }
}

// ── HAPUS MAP ───────────────────────────────
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $hapusId = intval($_GET['hapus']);
    // Cek apakah map masih dipakai
    $cekUsed = mysqli_query($conn, "SELECT * FROM matches WHERE map_id = '$hapusId' LIMIT 1");
    if (mysqli_num_rows($cekUsed) > 0) {
        $error = 'Map tidak bisa dihapus karena masih digunakan di match.';
    } else {
        mysqli_query($conn, "DELETE FROM maps WHERE id_map = '$hapusId'");
        $success = 'Map berhasil dihapus.';
    }
}

// ── EDIT MAP ────────────────────────────────
if (isset($_POST['edit'])) {
    $editId   = intval($_POST['id_map']);
    $editNama = mysqli_real_escape_string($conn, trim($_POST['edit_nama']));

    if (empty($editNama)) {
        $error = 'Nama map tidak boleh kosong.';
    } else {
        mysqli_query($conn, "
            UPDATE maps SET nama_map = '$editNama' WHERE id_map = '$editId'
        ");
        $success = 'Map berhasil diupdate.';
    }
}

// Ambil data maps
$maps = mysqli_query($conn, "SELECT * FROM maps ORDER BY nama_map ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Map — Valorant Match Tracker</title>
  <link rel="stylesheet" href="../asset (frontend)/css/style.css">
  <link rel="stylesheet" href="../asset (frontend)/css/admin.css">
</head>
<body>
<div class="grid-bg"></div>
<div class="page-wrapper">

  <?php include "sidebar.php"; ?>

  <main class="main-content">
    <div class="page-header">
      <h1 class="page-title">MANAGE <span>MAP</span></h1>
      <p class="page-subtitle">Kelola daftar map yang tersedia</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">✔ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Form Tambah -->
    <div class="inline-form-card" style="margin-bottom:24px;">
      <div class="card-title" style="margin-bottom:14px;">Tambah Map Baru</div>
      <form method="POST" action="">
        <div class="form-row-inline">
          <div class="form-group">
            <label class="form-label">Nama Map</label>
            <input type="text" name="nama_map" class="form-control" placeholder="contoh: Ascent" required>
          </div>
          <div>
            <label class="form-label">&nbsp;</label>
            <button type="submit" name="tambah" class="btn btn-primary">➕ Tambah</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Table Maps -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Daftar Map</span>
        <span class="text-muted" style="font-size:0.8rem;">
          <?= mysqli_num_rows($maps) ?> map terdaftar
        </span>
      </div>
      <div class="table-wrapper">
        <?php
        mysqli_data_seek($maps, 0);
        if (mysqli_num_rows($maps) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Map</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($map = mysqli_fetch_assoc($maps)): ?>
            <tr>
              <td class="text-muted font-ui"><?= $no++ ?></td>
              <td class="font-ui fw-bold"><?= htmlspecialchars($map['nama_map']) ?></td>
              <td>
                <div class="action-btns">
                  <button class="btn btn-secondary btn-sm"
                    onclick="openEdit(<?= $map['id_map'] ?>, '<?= htmlspecialchars(addslashes($map['nama_map'])) ?>')">
                    ✏️ Edit
                  </button>
                  <a href="maps.php?hapus=<?= $map['id_map'] ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Hapus map <?= htmlspecialchars(addslashes($map['nama_map'])) ?>?')">
                    🗑️ Hapus
                  </a>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
          <div class="empty-icon">🗺️</div>
          <p>Belum ada map. Tambahkan map di atas.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="editModal" style="display:none;">
  <div class="modal-box">
    <div class="modal-title">✏️ Edit Map</div>
    <form method="POST" action="">
      <input type="hidden" name="id_map" id="editId">
      <div class="form-group">
        <label class="form-label">Nama Map</label>
        <input type="text" name="edit_nama" id="editNama" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeEdit()">Batal</button>
        <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEdit(id, nama) {
  document.getElementById('editId').value   = id;
  document.getElementById('editNama').value = nama;
  document.getElementById('editModal').style.display = 'flex';
}
function closeEdit() {
  document.getElementById('editModal').style.display = 'none';
}
document.getElementById('editModal').addEventListener('click', function(e) {
  if (e.target === this) closeEdit();
});
</script>
</body>
</html>
