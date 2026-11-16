<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Masuk — Parkir Aman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="view/Login/styles.css" />
  </head>
  <body class="auth-page">
    <aside class="auth-aside">
      <a class="brand brand--light" href="index.php">
        <span class="brand__icon">
          <!-- <img LOGO alt="" /> -->
        </span>
        <span></span>
      </a>
      <div>
        <h1>Tempat Aman pelanggan Senang, Mulai Dari <em>Rasa Percaya.</em></h1>
        <p>Sistem Ini Memudahkan Anda Mengelola Tempat Parkir Anda.</p>
      </div>
      <div class="trust-box">
        <span class="feature-icon">✓</span>
        <div>
          <strong>Keamanan yang dipikirkan</strong>
          <p>Setiap kendaraan punya cerita. Kami menjaganya.</p>
        </div>
      </div>
    </aside>

    <main class="auth-main">
      <div class="auth-top">
        <a class="text-link" href="index.php">← Kembali</a>
      </div>
      <div class="auth-form">
        <p class="eyebrow">SELAMAT DATANG</p>
        <h2>Masuk ke Sistem Parkir Anda.</h2>
        <p>Lihat data kendaraan yang anda butuhkan.</p>
        <form action="index.php?c=auth&a=login" method="POST">
          <label>Username
            <input type="text" name="username" autocomplete="username" placeholder="Masukkan username" required />
          </label>
          <label>Password
            <input type="password" name="password" autocomplete="current-password" placeholder="Masukkan password" required />
          </label>
          <button class="button button--full" type="submit">Masuk <span>→</span></button>
        </form>
      </div>
    </main>
  </body>
</html>