<?php
session_start();
include "../koneksi.php";
include "../function.php";

// Auth check
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'player') {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['id'];
$error = '';

// Validasi ID match dari GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: matchhistory.php");
    exit;
}

$matchId = intval($_GET['id']);

// Pastikan match milik player ini
$matchQuery = mysqli_query($conn, "
    SELECT * FROM matches WHERE id_match = '$matchId' AND player_id = '$id'
");

if (!$matchQuery || mysqli_num_rows($matchQuery) == 0) {
    header("Location: matchhistory.php");
    exit;
}

$match = mysqli_fetch_assoc($matchQuery);

// Handle update
if (isset($_POST['update'])) {
    $agent_id   = intval($_POST['agent_id']);
    $map_id     = intval($_POST['map_id']);
    $result     = $_POST['result'];
    $kill       = intval($_POST['kill']);
    $death      = intval($_POST['death']);
    $assist     = intval($_POST['assist']);
    $tanggal    = $_POST['tanggal'];

    if ($agent_id <= 0 || $map_id <= 0 || empty($result) || empty($tanggal)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!in_array($result, ['Win', 'Lose'])) {
        $error = 'Result tidak valid.';
    } elseif ($kill < 0 || $death < 0 || $assist < 0) {
        $error = 'Statistik tidak boleh negatif.';
    } else {
        mysqli_query($conn, "
            UPDATE matches SET
              agent_id     = '$agent_id',
              map_id       = '$map_id',
              result       = '$result',
              kill_count   = '$kill',
              death_count  = '$death',
              assist_count = '$assist',
              tanggal      = '$tanggal'
            WHERE id_match = '$matchId'
            AND player_id  = '$id'
        ");
        header("Location: matchhistory.php?success=Match+berhasil+diupdate");
        exit;
    }
}

// Data dropdown
$agents = mysqli_query($conn, "SELECT * FROM agents ORDER BY nama_agent ASC");
$maps   = mysqli_query($conn, "SELECT * FROM maps ORDER BY nama_map ASC");

$username = isset($_SESSION['username']) ? $_SESSION['username'] : getUsername($conn, $id);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Match — Valorant Match Tracker</title>
  <link rel="stylesheet" href="../asset (frontend)/css/style.css">
  <link rel="stylesheet" href="../asset (frontend)/css/dashboard.css">
</head>
<body>
<div class="grid-bg"></div>
<div class="page-wrapper">

  <?php include "sidebar.php"; ?>

  <main class="main-content">
    <div class="page-header">
      <div class="d-flex align-center gap-3">
        <div>
          <h1 class="page-title">EDIT <span>MATCH</span></h1>
          <p class="page-subtitle">Perbarui data pertandingan</p>
        </div>
        <div class="ms-auto">
          <a href="matchhistory.php" class="btn btn-secondary">← Kembali</a>
        </div>
      </div>
    </div>

    <div class="card form-page">
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="form-section-label">Informasi Match</div>

          <div class="form-row mb-3">
            <div class="form-group">
              <label class="form-label">Agent</label>
              <select name="agent_id" class="form-control" required>
                <option value="">-- Pilih Agent --</option>
                <?php while ($agent = mysqli_fetch_assoc($agents)): ?>
                <option value="<?= $agent['id_agent'] ?>"
                  <?= $match['agent_id'] == $agent['id_agent'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($agent['nama_agent']) ?>
                  (<?= htmlspecialchars($agent['role_agent']) ?>)
                </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Map</label>
              <select name="map_id" class="form-control" required>
                <option value="">-- Pilih Map --</option>
                <?php while ($map = mysqli_fetch_assoc($maps)): ?>
                <option value="<?= $map['id_map'] ?>"
                  <?= $match['map_id'] == $map['id_map'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($map['nama_map']) ?>
                </option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>

          <div class="form-row mb-3">
            <div class="form-group">
              <label class="form-label">Tanggal</label>
              <input
                type="date"
                name="tanggal"
                class="form-control"
                value="<?= htmlspecialchars($match['tanggal']) ?>"
                max="<?= date('Y-m-d') ?>"
                required
              >
            </div>

            <div class="form-group">
              <label class="form-label">Result</label>
              <div class="result-toggle">
                <div class="result-option">
                  <input type="radio" name="result" id="win" value="Win"
                    <?= $match['result'] == 'Win' ? 'checked' : '' ?>>
                  <label for="win" class="win-label">🏆 Win</label>
                </div>
                <div class="result-option">
                  <input type="radio" name="result" id="lose" value="Lose"
                    <?= $match['result'] == 'Lose' ? 'checked' : '' ?>>
                  <label for="lose" class="lose-label">💀 Lose</label>
                </div>
              </div>
            </div>
          </div>

          <div class="form-section-label" style="margin-top:20px;">Statistik</div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Kill</label>
              <input
                type="number"
                name="kill"
                class="form-control"
                min="0" max="100"
                value="<?= $match['kill_count'] ?>"
                required
              >
            </div>

            <div class="form-group">
              <label class="form-label">Death</label>
              <input
                type="number"
                name="death"
                class="form-control"
                min="0" max="100"
                value="<?= $match['death_count'] ?>"
                required
              >
            </div>

            <div class="form-group">
              <label class="form-label">Assist</label>
              <input
                type="number"
                name="assist"
                class="form-control"
                min="0" max="100"
                value="<?= $match['assist_count'] ?>"
                required
              >
            </div>
          </div>

          <div style="display:flex; gap:12px; margin-top:8px;">
            <button type="submit" name="update" class="btn btn-primary">💾 Simpan Perubahan</button>
            <a href="matchhistory.php" class="btn btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>

  </main>
</div>
</body>
</html>
