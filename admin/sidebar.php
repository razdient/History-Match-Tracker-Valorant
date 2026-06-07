<?php
// Partial: Admin Sidebar
$currentPage = basename($_SERVER['PHP_SELF']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">⚔️</div>
    <div class="logo-title">Valorant<br>Tracker</div>
    <div class="logo-sub">Admin Panel</div>
  </div>

  <div class="sidebar-user">
    <div class="user-label">Logged in as</div>
    <div class="user-name"><?= htmlspecialchars($username) ?></div>
    <div class="user-role admin">Admin</div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Dashboard</div>

    <a href="dashboard.php"
       class="nav-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
      <span class="nav-icon">📊</span>
      Dashboard
    </a>

    <div class="nav-section-label" style="margin-top:8px;">Manajemen</div>

    <a href="agen.php"
       class="nav-item <?= $currentPage == 'agen.php' ? 'active' : '' ?>">
      <span class="nav-icon">🎭</span>
      Manage Agent
    </a>

    <a href="maps.php"
       class="nav-item <?= $currentPage == 'maps.php' ? 'active' : '' ?>">
      <span class="nav-icon">🗺️</span>
      Manage Map
    </a>
  </nav>

  <div class="sidebar-footer">
    <a href="../logout.php" class="btn-logout">
      <span>🚪</span>
      Logout
    </a>
  </div>
</aside>
