<!doctype html>
<html lang="tr" class="theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FEE CARS | Showroom</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/css/style.css">
</head>
<body class="cine-body">
  <header class="topbar">
    <div class="topbar-inner">
      <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/" class="brand">FEE CARS</a>
      <nav>
        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/">Showroom</a>
        <a id="navMessagesLink" class="hidden" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/messages">Mesajlar</a>
        <a id="navAdminLink" class="hidden" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/admin">Admin Paneli</a>
      </nav>
      <div class="top-actions">
        <span id="whoami" class="muted">Misafir</span>
        <button id="btnFavorites" class="btn btn-ghost hidden">Favoriler</button>
        <a id="btnLogin" class="btn btn-ghost" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/access">Giris</a>
        <button id="btnLogout" class="btn btn-ghost hidden">Cikis</button>
      </div>
    </div>
  </header>

  <main>
    <section class="hero-block">
      <img src="https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1800&q=80" alt="Hero car">
      <div class="overlay"></div>
      <div class="hero-content">
        <p class="kicker">Exhibition 2026</p>
        <h1>The Art of Velocity</h1>
        <p>Hesapsiz gez, uye olunca favori ve galerici ile iletisim acilsin.</p>
      </div>
    </section>

    <section class="content-shell">
      <div id="notice" class="notice hidden"></div>

      <div class="filters-cine">
        <input id="fBrand" placeholder="Marka">
        <input id="fModel" placeholder="Model">
        <input id="fMin" type="number" placeholder="Min fiyat">
        <input id="fMax" type="number" placeholder="Max fiyat">
        <button class="btn btn-primary" id="applyFilter">Filtrele</button>
      </div>

      <section id="carsGrid" class="car-grid"></section>
    </section>
  </main>

  <div id="favoritesModal" class="modal">
    <div class="modal-card">
      <div class="row-end">
        <h3>Favorilerim</h3>
        <button id="closeFav" class="btn btn-ghost">Kapat</button>
      </div>
      <div id="favList"></div>
    </div>
  </div>

  <script>
    window.BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_UNICODE) ?>;
    window.PAGE = 'showroom';
  </script>
  <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/js/app.js"></script>
</body>
</html>
