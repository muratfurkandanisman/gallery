<!doctype html>
<html lang="tr" class="theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FEE CARS | Giris</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/css/style.css">
</head>
<body class="cine-body">
  <main class="access-wrap">
    <section class="access-visual">
      <img src="https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1400&q=80" alt="Luxury car">
      <div class="overlay"></div>
      <div class="visual-copy">
        <p class="kicker">Heritage & Precision</p>
        <h1>FEE CARS</h1>
        <p>Premium galeri deneyimine hos geldiniz.</p>
      </div>
    </section>

    <section class="access-form-zone">
      <div class="access-card">
        <a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/">Showrooma Don</a>
        <h2 id="accessTitle">Giris Yap</h2>
        <p class="sub">Hesabina girerek favori ve iletisim ozelliklerini kullan.</p>
        <div id="notice" class="notice hidden"></div>

        <form id="accessForm" data-mode="login" class="stack-14">
          <div id="nameField" class="hidden">
            <label for="fullName">Ad Soyad</label>
            <input id="fullName" type="text" placeholder="Adiniz Soyadiniz">
          </div>
          <div>
            <label for="email">E-posta</label>
            <input id="email" type="email" placeholder="ornek@mail.com" required>
          </div>
          <div>
            <label for="password">Sifre</label>
            <input id="password" type="password" placeholder="******" required>
          </div>
          <button class="btn btn-primary" type="submit" id="submitBtn">Giris Yap</button>
        </form>

        <div class="auth-switch">
          <button id="toLogin" class="chip active">Giris</button>
          <button id="toRegister" class="chip">Kayit Ol</button>
        </div>
      </div>
    </section>
  </main>

  <script>
    window.BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_UNICODE) ?>;
    window.PAGE = 'access';
  </script>
  <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/js/app.js"></script>
</body>
</html>
