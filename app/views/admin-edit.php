<!doctype html>
<html lang="tr" class="theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FEE CARS | Arac Guncelle</title>
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

    <section class="panel">
      <div class="row-between" style="margin-bottom: 12px;">
        <h3 style="margin: 0;">Arac Bilgilerini Guncelle</h3>
        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/admin" class="btn btn-ghost">Admin Paneline Don</a>
      </div>

      <form id="adminEditCarForm" class="form-grid">
        <div class="field-hint-wrap">
          <input id="eBrand" placeholder="Marka" required>
          <span class="field-hint">MARKA</span>
        </div>
        <div class="field-hint-wrap">
          <input id="eModel" placeholder="Model" required>
          <span class="field-hint">MODEL</span>
        </div>
        <div class="field-hint-wrap">
          <input id="eYear" type="number" placeholder="Yil" required>
          <span class="field-hint">YIL</span>
        </div>
        <div class="field-hint-wrap">
          <input id="ePrice" type="number" placeholder="Fiyat" required>
          <span class="field-hint">FIYAT</span>
        </div>
        <div class="field-hint-wrap">
          <input id="eMileage" type="number" placeholder="KM" required>
          <span class="field-hint">KM</span>
        </div>
        <div class="field-hint-wrap">
          <input id="eFuel" placeholder="Yakit">
          <span class="field-hint">YAKIT</span>
        </div>
        <div class="field-hint-wrap">
          <input id="eGear" placeholder="Vites">
          <span class="field-hint">VITES</span>
        </div>
        <div class="field-hint-wrap">
          <input id="eColor" placeholder="Renk">
          <span class="field-hint">RENK</span>
        </div>
        <div class="field-hint-wrap">
          <select id="eStatus">
            <option value="AVAILABLE">AVAILABLE</option>
            <option value="SOLD">SOLD</option>
          </select>
          <span class="field-hint">DURUM</span>
        </div>
        <div class="field-hint-wrap field-hint-full">
          <textarea id="eDesc" placeholder="Aciklama" rows="3"></textarea>
          <span class="field-hint">ACIKLAMA</span>
        </div>
        <button class="btn btn-primary" type="submit">Guncelle</button>
      </form>
    </section>
  </main>

  <script>
    window.BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_UNICODE) ?>;
    window.PAGE = 'admin-edit';
    window.CAR_ID = <?= (int) $carId ?>;
  </script>
  <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/js/app.js"></script>
</body>
</html>
