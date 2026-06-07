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

// Stats
$totalPlayer = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM users WHERE role='player'")
);

$totalMatch = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM matches")
);

$totalAgent = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM agents")
);

$totalMap = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM maps")
);

// Recent players dengan total match mereka
$players = mysqli_query($conn, "
    SELECT u.id, u.username,
           COUNT(m.id_match) AS total_match,
           SUM(CASE WHEN m.result='Win' THEN 1 ELSE 0 END) AS total_win
    FROM users u
    LEFT JOIN matches m ON u.id = m.player_id
    WHERE u.role = 'player'
    GROUP BY u.id
    ORDER BY total_match DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin — Valorant Match Tracker</title>
  <link rel="stylesheet" href="../asset (frontend)/css/style.css">
  <link rel="stylesheet" href="../asset (frontend)/css/admin.css">
</head>
<body>
<div class="grid-bg"></div>
<div class="page-wrapper">

  <?php include "sidebar.php"; ?>

  <main class="main-content">
    <div class="page-header">
      <h1 class="page-title">ADMIN <span>DASHBOARD</span></h1>
      <p class="page-subtitle">Overview sistem Valorant Match Tracker</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card admin-card">
        <div class="stat-icon">👥</div>
        <div class="stat-label">Total Player</div>
        <div class="stat-value accent"><?= $totalPlayer ?></div>
      </div>

      <div class="stat-card admin-card">
        <div class="stat-icon">🎮</div>
        <div class="stat-label">Total Match</div>
        <div class="stat-value"><?= $totalMatch ?></div>
      </div>

      <div class="stat-card admin-card">
        <div class="stat-icon">🎭</div>
        <div class="stat-label">Total Agent</div>
        <div class="stat-value"><?= $totalAgent ?></div>
      </div>

      <div class="stat-card admin-card">
        <div class="stat-icon">🗺️</div>
        <div class="stat-label">Total Map</div>
        <div class="stat-value"><?= $totalMap ?></div>
      </div>
    </div>

    <!-- Player Overview -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Daftar Player</span>
      </div>
      <div class="table-wrapper">
        <?php if ($players && mysqli_num_rows($players) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Total Match</th>
              <th>Total Win</th>
              <th>Win Rate</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($p = mysqli_fetch_assoc($players)): ?>
            <tr>
              <td class="text-muted font-ui"><?= $no++ ?></td>
              <td class="font-ui fw-bold"><?= htmlspecialchars($p['username']) ?></td>
              <td class="font-ui"><?= $p['total_match'] ?></td>
              <td class="font-ui text-accent"><?= $p['total_win'] ?></td>
              <td class="font-ui">
                <?php $wr = winRate($p['total_win'], $p['total_match']); ?>
                <span class="<?= $wr >= 50 ? 'text-accent' : 'text-danger' ?>">
                  <?= $wr ?>%
                </span>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
          <div class="empty-icon">👥</div>
          <p>Belum ada player terdaftar.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </main>
</div>
</body>
</html>
