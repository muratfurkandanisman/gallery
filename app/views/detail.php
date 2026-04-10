<!doctype html>
<html lang="tr" class="theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FEE CARS | Arac Detay</title>
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
      <div class="top-actions"><span id="whoami" class="muted">Misafir</span></div>
    </div>
  </header>

  <main class="ve-detail-page">
    <div id="notice" class="notice hidden"></div>

    <section class="ve-hero">
      <img id="dHeroImage" src="" alt="Arac hero gorseli">
      <div class="ve-hero-overlay"></div>

      <div class="ve-asset-badge" id="dAssetId">ASSET</div>
      <div class="ve-fav-slot" id="detailFavSlot"></div>

      <div class="ve-hero-content">
        <p class="ve-kicker" id="dTag">Vehicle Asset</p>
        <h1 id="dMainTitle">LOADING</h1>
        <p id="dDescription" class="ve-desc"></p>

        <div class="ve-stat-row">
          <div><span>Valuation</span><strong id="dPrice">-</strong></div>
          <div><span>Odometer</span><strong id="dMileage">-</strong></div>
          <div><span>Finish</span><strong id="dColor">-</strong></div>
        </div>
      </div>
    </section>

    <section class="ve-spec-table">
      <div class="ve-left-copy">
        <h2>UNRIVALED<br>SPECIFICATION</h2>
        <p>Bu aracin teknik ve ticari detaylari FEE CARS kurasyon standardinda dogrulanmistir.</p>
      </div>
      <div class="ve-right-copy">
        <p class="ve-panel-kicker">Arac Ozellikleri</p>
        <div class="ve-lines" id="dSpecLines"></div>
      </div>
    </section>

    <section class="ve-actions">
      <div class="ve-actions-right">
        <textarea id="inqMessage" rows="3" placeholder="Saticiya mesajiniz..."></textarea>
        <button id="btnInq" class="btn btn-primary">Iletisime Gec</button>
      </div>
    </section>
  </main>

  <script>
    window.BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_UNICODE) ?>;
    window.PAGE = 'detail';
    window.CAR_ID = <?= (int) $carId ?>;
  </script>
  <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/js/app.js"></script>
</body>
</html>
