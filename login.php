<?php
session_start();
include "koneksi.php";

// Jika sudah login, redirect
if (isset($_SESSION['id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: player/dashboard.php");
    }
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi.';
    } else {
        $query = mysqli_query($conn, "
            SELECT * FROM users
            WHERE username = '$username'
            AND password = '$password'
        ");

        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            $_SESSION['id']   = $data['id'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['username'] = $data['username'];

            if ($data['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: player/dashboard.php");
            }
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Valorant Match Tracker</title>
  <link rel="stylesheet" href="asset (frontend)/css/login.css">
</head>
<body>

<div class="auth-bg">
  <div class="grid-lines"></div>
  <div class="corner-decoration corner-tl"></div>
  <div class="corner-decoration corner-br"></div>
</div>

<div class="auth-wrapper">
  <div class="auth-brand">
    <div class="brand-icon">⚔️</div>
    <span class="brand-name">Valorant Tracker</span>
    <span class="brand-tagline">Match History System</span>
  </div>

  <div class="auth-card">
    <div class="auth-card-header">
      <h1>SIGN IN</h1>
      <p>Masuk ke akun Valorant Tracker kamu</p>
    </div>
    <div class="auth-card-body">
      <?php if ($error): ?>
        <div class="alert alert-error">
          ⚠️ <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Username</label>
          <div class="input-wrap">
            <span class="input-icon">👤</span>
            <input
              type="text"
              name="username"
              class="form-control"
              placeholder="Masukkan username"
              value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
              autocomplete="username"
              required
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrap">
            <span class="input-icon">🔒</span>
            <input
              type="password"
              name="password"
              class="form-control"
              placeholder="Masukkan password"
              autocomplete="current-password"
              required
            >
          </div>
        </div>

        <button type="submit" name="login" class="btn-auth">
          Masuk
        </button>
      </form>
    </div>
  </div>

  <p class="auth-footer">
    Belum punya akun? <a href="register.php">Daftar sekarang</a>
  </p>
</div>

</body>
</html>
