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

// Ambil semua match player dengan JOIN
$data = mysqli_query($conn, "
    SELECT matches.*, agents.nama_agent, maps.nama_map
    FROM matches
    JOIN agents ON matches.agent_id = agents.id_agent
    JOIN maps ON matches.map_id = maps.id_map
    WHERE player_id = '$id'
    ORDER BY tanggal DESC, id_match DESC
");

$username = isset($_SESSION['username']) ? $_SESSION['username'] : getUsername($conn, $id);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Match History — Valorant Match Tracker</title>
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
          <h1 class="page-title">MATCH <span>HISTORY</span></h1>
          <p class="page-subtitle">Semua riwayat pertandinganmu</p>
        </div>
        <div class="ms-auto">
          <a href="tambah_match.php" class="btn btn-primary">➕ Tambah Match</a>
        </div>
      </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">✔ <?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="table-wrapper">
        <?php if ($data && mysqli_num_rows($data) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Tanggal</th>
              <th>Agent</th>
              <th>Map</th>
              <th>Result</th>
              <th>Kill</th>
              <th>Death</th>
              <th>Assist</th>
              <th>KDA</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($data)):
              $k = $row['kill_count'];
              $d = $row['death_count'];
              $a = $row['assist_count'];
              $kdaVal = kda($k, $d, $a);
              $kdaClass = $kdaVal >= 2 ? 'kda-high' : ($kdaVal >= 1 ? 'kda-mid' : 'kda-low');
            ?>
            <tr>
              <td class="text-muted font-ui"><?= $no++ ?></td>
              <td class="font-ui"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
              <td>
                <div class="agent-cell">
                  <span class="agent-dot"></span>
                  <?= htmlspecialchars($row['nama_agent']) ?>
                </div>
              </td>
              <td class="text-secondary"><?= htmlspecialchars($row['nama_map']) ?></td>
              <td>
                <span class="badge <?= $row['result'] == 'Win' ? 'badge-win' : 'badge-lose' ?>">
                  <?= $row['result'] ?>
                </span>
              </td>
              <td class="font-ui text-accent fw-bold"><?= $k ?></td>
              <td class="font-ui text-danger fw-bold"><?= $d ?></td>
              <td class="font-ui text-gold fw-bold"><?= $a ?></td>
              <td class="kda-cell <?= $kdaClass ?>"><?= $kdaVal ?></td>
              <td>
                <div class="action-btns">
                  <a href="edit_match.php?id=<?= $row['id_match'] ?>" class="btn btn-secondary btn-sm">✏️ Edit</a>
                  <a href="hapus_match.php?id_match=<?= $row['id_match'] ?>"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Hapus match ini?')">🗑️ Hapus</a>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
          <div class="empty-icon">📋</div>
          <p>Belum ada data match. <a href="tambah_match.php">Tambahkan match pertamamu!</a></p>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </main>
</div>
</body>
</html>
