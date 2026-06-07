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

// ── TAMBAH AGENT ────────────────────────────
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $role = mysqli_real_escape_string($conn, trim($_POST['role']));

    if (empty($nama) || empty($role)) {
        $error = 'Nama agent dan role wajib diisi.';
    } else {
        // Cek duplikat
        $cek = mysqli_query($conn, "SELECT * FROM agents WHERE nama_agent = '$nama'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Agent dengan nama tersebut sudah ada.';
        } else {
            mysqli_query($conn, "
                INSERT INTO agents (nama_agent, role_agent)
                VALUES ('$nama', '$role')
            ");
            $success = 'Agent berhasil ditambahkan.';
        }
    }
}

// ── HAPUS AGENT ─────────────────────────────
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $hapusId = intval($_GET['hapus']);
    // Cek apakah agent masih dipakai di matches
    $cekUsed = mysqli_query($conn, "SELECT * FROM matches WHERE agent_id = '$hapusId' LIMIT 1");
    if (mysqli_num_rows($cekUsed) > 0) {
        $error = 'Agent tidak bisa dihapus karena masih digunakan di match.';
    } else {
        mysqli_query($conn, "DELETE FROM agents WHERE id_agent = '$hapusId'");
        $success = 'Agent berhasil dihapus.';
    }
}

// ── EDIT AGENT ──────────────────────────────
if (isset($_POST['edit'])) {
    $editId   = intval($_POST['id_agent']);
    $editNama = mysqli_real_escape_string($conn, trim($_POST['edit_nama']));
    $editRole = mysqli_real_escape_string($conn, trim($_POST['edit_role']));

    if (empty($editNama) || empty($editRole)) {
        $error = 'Nama dan role tidak boleh kosong.';
    } else {
        mysqli_query($conn, "
            UPDATE agents SET
              nama_agent  = '$editNama',
              role_agent  = '$editRole'
            WHERE id_agent = '$editId'
        ");
        $success = 'Agent berhasil diupdate.';
    }
}

// Data agents
$agents = mysqli_query($conn, "SELECT * FROM agents ORDER BY nama_agent ASC");

$roleOptions = ['Duelist', 'Initiator', 'Controller', 'Sentinel'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Agent — Valorant Match Tracker</title>
  <link rel="stylesheet" href="../asset (frontend)/css/style.css">
  <link rel="stylesheet" href="../asset (frontend)/css/admin.css">
</head>
<body>
<div class="grid-bg"></div>
<div class="page-wrapper">

  <?php include "sidebar.php"; ?>

  <main class="main-content">
    <div class="page-header">
      <h1 class="page-title">MANAGE <span>AGENT</span></h1>
      <p class="page-subtitle">Kelola daftar agent yang tersedia</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">✔ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Form Tambah -->
    <div class="inline-form-card" style="margin-bottom:24px;">
      <div class="card-title" style="margin-bottom:14px;">Tambah Agent Baru</div>
      <form method="POST" action="">
        <div class="form-row-inline">
          <div class="form-group">
            <label class="form-label">Nama Agent</label>
            <input type="text" name="nama" class="form-control" placeholder="contoh: Jett" required>
          </div>
          <div class="form-group">
            <label class="form-label">Role Agent</label>
            <select name="role" class="form-control" required>
              <option value="">-- Pilih Role --</option>
              <?php foreach ($roleOptions as $r): ?>
              <option value="<?= $r ?>"><?= $r ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="form-label">&nbsp;</label>
            <button type="submit" name="tambah" class="btn btn-primary">➕ Tambah</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Table Agents -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Daftar Agent</span>
        <span class="text-muted" style="font-size:0.8rem;">
          <?= mysqli_num_rows($agents) ?> agent terdaftar
        </span>
      </div>
      <div class="table-wrapper">
        <?php
        // reset pointer
        mysqli_data_seek($agents, 0);
        if (mysqli_num_rows($agents) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Agent</th>
              <th>Role</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($agent = mysqli_fetch_assoc($agents)): ?>
            <tr>
              <td class="text-muted font-ui"><?= $no++ ?></td>
              <td class="font-ui fw-bold"><?= htmlspecialchars($agent['nama_agent']) ?></td>
              <td>
                <span class="badge badge-role"><?= htmlspecialchars($agent['role_agent']) ?></span>
              </td>
              <td>
                <div class="action-btns">
                  <!-- Tombol Edit (trigger modal) -->
                  <button class="btn btn-secondary btn-sm"
                    onclick="openEdit(<?= $agent['id_agent'] ?>, '<?= htmlspecialchars(addslashes($agent['nama_agent'])) ?>', '<?= htmlspecialchars(addslashes($agent['role_agent'])) ?>')">
                    ✏️ Edit
                  </button>
                  <!-- Tombol Hapus -->
                  <a href="agen.php?hapus=<?= $agent['id_agent'] ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Hapus agent <?= htmlspecialchars(addslashes($agent['nama_agent'])) ?>?')">
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
          <div class="empty-icon">🎭</div>
          <p>Belum ada agent. Tambahkan agent di atas.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="editModal" style="display:none;">
  <div class="modal-box">
    <div class="modal-title">✏️ Edit Agent</div>
    <form method="POST" action="">
      <input type="hidden" name="id_agent" id="editId">
      <div class="form-group">
        <label class="form-label">Nama Agent</label>
        <input type="text" name="edit_nama" id="editNama" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Role Agent</label>
        <select name="edit_role" id="editRole" class="form-control" required>
          <?php foreach ($roleOptions as $r): ?>
          <option value="<?= $r ?>"><?= $r ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeEdit()">Batal</button>
        <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEdit(id, nama, role) {
  document.getElementById('editId').value   = id;
  document.getElementById('editNama').value = nama;
  document.getElementById('editRole').value = role;
  document.getElementById('editModal').style.display = 'flex';
}
function closeEdit() {
  document.getElementById('editModal').style.display = 'none';
}
// Close on overlay click
document.getElementById('editModal').addEventListener('click', function(e) {
  if (e.target === this) closeEdit();
});
</script>
</body>
</html>
