<<<<<<< HEAD
<?php
session_start();
include "koneksi.php";

// Jika sudah login, redirect
if (isset($_SESSION['id'])) {
    header("Location: player/dashboard.php");
    exit;
}

$error   = '';
$success = '';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi.';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (strlen($password) < 3) {
        $error = 'Password minimal 3 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $cek = mysqli_query($conn, "
            SELECT * FROM users WHERE username = '$username'
        ");

        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah dipakai, coba yang lain.';
        } else {
            mysqli_query($conn, "
                INSERT INTO users (username, password, role)
                VALUES ('$username', '$password', 'player')
            ");
            $success = 'Akun berhasil dibuat! Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — Valorant Match Tracker</title>
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
      <h1>REGISTER</h1>
      <p>Buat akun baru untuk mulai tracking</p>
    </div>
    <div class="auth-card-body">
      <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success">✔ <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <?php if (!$success): ?>
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Username</label>
          <div class="input-wrap">
            <span class="input-icon">👤</span>
            <input
              type="text"
              name="username"
              class="form-control"
              placeholder="Pilih username"
              value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
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
              placeholder="Buat password"
              required
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Konfirmasi Password</label>
          <div class="input-wrap">
            <span class="input-icon">🔑</span>
            <input
              type="password"
              name="confirm_password"
              class="form-control"
              placeholder="Ulangi password"
              required
            >
          </div>
        </div>

        <button type="submit" name="register" class="btn-auth">
          Buat Akun
        </button>
      </form>
      <?php else: ?>
        <a href="login.php" class="btn-auth" style="display:block; text-align:center; text-decoration:none;">
          Pergi ke Halaman Login
        </a>
      <?php endif; ?>
    </div>
  </div>

  <p class="auth-footer">
    Sudah punya akun? <a href="login.php">Login sekarang</a>
  </p>
</div>

</body>
</html>
=======
  <?php
  session_start();
  include "koneksi.php";

  // Jika sudah login, redirect
  if (isset($_SESSION['id'])) {
      header("Location: player/dashboard.php");
      exit;
  }

  $error   = '';
  $success = '';

  if (isset($_POST['register'])) {
      $username = mysqli_real_escape_string($conn, trim($_POST['username']));
      $password = trim($_POST['password']);
      $confirm  = trim($_POST['confirm_password']);

      if (empty($username) || empty($password)) {
          $error = 'Username dan password harus diisi.';
      } elseif (strlen($username) < 3) {
          $error = 'Username minimal 3 karakter.';
      } elseif (strlen($password) < 3) {
          $error = 'Password minimal 3 karakter.';
      } elseif ($password !== $confirm) {
          $error = 'Konfirmasi password tidak cocok.';
      } else {
          $cek = mysqli_query($conn, "
              SELECT * FROM users WHERE username = '$username'
          ");

          if (mysqli_num_rows($cek) > 0) {
              $error = 'Username sudah dipakai, coba yang lain.';
          } else {
              mysqli_query($conn, "
                  INSERT INTO users (username, password, role)
                  VALUES ('$username', '$password', 'player')
              ");
              $success = 'Akun berhasil dibuat! Silakan login.';
          }
      }
  }
  ?>
  <!DOCTYPE html>
  <html lang="id">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Valorant Match Tracker</title>
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
        <h1>REGISTER</h1>
        <p>Buat akun baru untuk mulai tracking</p>
      </div>
      <div class="auth-card-body">
        <?php if ($error): ?>
          <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success">✔ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="">
          <div class="form-group">
            <label class="form-label">Username</label>
            <div class="input-wrap">
              <span class="input-icon">👤</span>
              <input
                type="text"
                name="username"
                class="form-control"
                placeholder="Pilih username"
                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
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
                placeholder="Buat password"
                required
              >
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-wrap">
              <span class="input-icon">🔑</span>
              <input
                type="password"
                name="confirm_password"
                class="form-control"
                placeholder="Ulangi password"
                required
              >
            </div>
          </div>

          <button type="submit" name="register" class="btn-auth">
            Buat Akun
          </button>
        </form>
        <?php else: ?>
          <a href="login.php" class="btn-auth" style="display:block; text-align:center; text-decoration:none;">
            Pergi ke Halaman Login
          </a>
        <?php endif; ?>
      </div>
    </div>

    <p class="auth-footer">
      Sudah punya akun? <a href="login.php">Login sekarang</a>
    </p>
  </div>

  </body>
  </html>
>>>>>>> b12d2d86b54d16831f403fe8e44fffdc9f9aae80
