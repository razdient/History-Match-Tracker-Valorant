<?php
// Partial: Player Sidebar
// Harus dipanggil setelah session_start() dan koneksi
$currentPage = basename($_SERVER['PHP_SELF']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : getUsername($conn, $_SESSION['id']);
?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">⚔️</div>
    <div class="logo-title">Valorant<br>Tracker</div>
    <div class="logo-sub">Match History System</div>
  </div>

  <div class="sidebar-user">
    <div class="user-label">Logged in as</div>
    <div class="user-name"><?= htmlspecialchars($username) ?></div>
    <div class="user-role">Player</div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Menu</div>

    <a href="dashboard.php"
       class="nav-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
      <span class="nav-icon">📊</span>
      Dashboard
    </a>

    <a href="matchhistory.php"
       class="nav-item <?= $currentPage == 'matchhistory.php' ? 'active' : '' ?>">
      <span class="nav-icon">📋</span>
      Match History
    </a>

    <a href="tambah_match.php"
       class="nav-item <?= $currentPage == 'tambah_match.php' ? 'active' : '' ?>">
      <span class="nav-icon">➕</span>
      Tambah Match
    </a>
  </nav>

  <div class="sidebar-footer">
    <a href="../logout.php" class="btn-logout">
      <span>🚪</span>
      Logout
    </a>
  </div>
</aside>
