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

// Stats
$totalMatch = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM matches WHERE player_id='$id'")
);

$totalWin = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM matches WHERE player_id='$id' AND result='Win'")
);

$winRate = winRate($totalWin, $totalMatch);
$avgKdaVal = avgKda($conn, $id);

$username = isset($_SESSION['username']) ? $_SESSION['username'] : getUsername($conn, $id);

// Recent 5 matches
$recentMatches = mysqli_query($conn, "
    SELECT matches.*, agents.nama_agent, maps.nama_map
    FROM matches
    JOIN agents ON matches.agent_id = agents.id_agent
    JOIN maps ON matches.map_id = maps.id_map
    WHERE player_id = '$id'
    ORDER BY tanggal DESC, id_match DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Valorant Match Tracker</title>
  <link rel="stylesheet" href="../asset (frontend)/css/style.css">
  <link rel="stylesheet" href="../asset (frontend)/css/dashboard.css">
</head>
<body>
<div class="grid-bg"></div>
<div class="page-wrapper">

  <?php include "sidebar.php"; ?>

  <main class="main-content">
    <div class="page-header">
      <h1 class="page-title">DASHBOARD <span>PLAYER</span></h1>
      <p class="page-subtitle">Selamat datang, <?= htmlspecialchars($username) ?>! Pantau statistik pertandinganmu.</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">🎮</div>
        <div class="stat-label">Total Match</div>
        <div class="stat-value"><?= $totalMatch ?></div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🏆</div>
        <div class="stat-label">Total Win</div>
        <div class="stat-value accent"><?= $totalWin ?></div>
      </div>

      <div class="stat-card red">
        <div class="stat-icon">📉</div>
        <div class="stat-label">Total Lose</div>
        <div class="stat-value red"><?= $totalMatch - $totalWin ?></div>
      </div>

      <div class="stat-card gold">
        <div class="stat-icon">📈</div>
        <div class="stat-label">Win Rate</div>
        <div class="stat-value gold"><?= $winRate ?>%</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">⚡</div>
        <div class="stat-label">Avg KDA</div>
        <div class="stat-value"><?= $avgKdaVal ?></div>
      </div>
    </div>

    <!-- Recent Matches -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Match Terbaru</span>
        <a href="matchhistory.php" class="btn btn-secondary btn-sm">Lihat Semua</a>
      </div>

      <div class="table-wrapper">
        <?php if (mysqli_num_rows($recentMatches) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Agent</th>
              <th>Map</th>
              <th>Result</th>
              <th>K / D / A</th>
              <th>KDA</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($recentMatches)): ?>
            <?php
              $k = $row['kill_count'];
              $d = $row['death_count'];
              $a = $row['assist_count'];
              $kdaVal = kda($k, $d, $a);
              $kdaClass = $kdaVal >= 2 ? 'kda-high' : ($kdaVal >= 1 ? 'kda-mid' : 'kda-low');
            ?>
            <tr>
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
              <td class="score-cell">
                <span class="score-k"><?= $k ?></span>
                <span class="score-sep">/</span>
                <span class="score-d"><?= $d ?></span>
                <span class="score-sep">/</span>
                <span class="score-a"><?= $a ?></span>
              </td>
              <td class="kda-cell <?= $kdaClass ?>"><?= $kdaVal ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
          <div class="empty-icon">🎮</div>
          <p>Belum ada match. <a href="tambah_match.php">Tambah match pertamamu!</a></p>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </main>
</div>
</body>
</html>
