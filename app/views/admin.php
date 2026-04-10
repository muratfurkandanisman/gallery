<!doctype html>
<html lang="tr" class="theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FEE CARS | Admin Paneli</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
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
        <span id="whoami" class="muted">Kontrol ediliyor...</span>
        <button id="btnLogout" class="btn btn-ghost">Cikis</button>
      </div>
    </div>
  </header>

  <main class="admin-shell">
    <div id="notice" class="notice hidden"></div>
    <section class="admin-header">
      <h1>Admin Paneli</h1>
      <div class="tabs">
        <button class="chip active admin-tab" data-status="">Tum Araclar</button>
        <button class="chip admin-tab" data-status="AVAILABLE">Satilik</button>
        <button class="chip admin-tab" data-status="SOLD">Satilan</button>
      </div>
    </section>

    <section class="panel">
      <h3>Stok Listesi</h3>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th>ID</th><th>Arac</th><th>Yil</th><th>Fiyat</th><th>Durum</th><th>Islem</th></tr>
          </thead>
          <tbody id="adminCarsTable"></tbody>
        </table>
      </div>
    </section>

    <section class="panel">
      <h3>Yeni Arac Ekle</h3>
      <form id="adminCarForm" class="form-grid">
        <input id="aBrand" placeholder="Marka" required>
        <input id="aModel" placeholder="Model" required>
        <input id="aYear" type="number" placeholder="Yil" required>
        <input id="aPrice" type="number" placeholder="Fiyat" required>
        <input id="aMileage" type="number" placeholder="KM" required>
        <input id="aFuel" placeholder="Yakit">
        <input id="aGear" placeholder="Vites">
        <input id="aColor" placeholder="Renk">
        <input id="aDesc" placeholder="Aciklama">
        <button class="btn btn-primary" type="submit">Arac Ekle</button>
      </form>
    </section>

    <section class="panel">
      <h3>Iletisim Talepleri</h3>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th>ID</th><th>Kullanici</th><th>Arac</th><th>Mesaj</th><th>Durum</th><th>Guncelle</th></tr>
          </thead>
          <tbody id="adminInqTable"></tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
    window.BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_UNICODE) ?>;
    window.PAGE = 'admin';
  </script>
  <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/js/app.js"></script>
</body>
</html>
