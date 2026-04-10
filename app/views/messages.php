<!doctype html>
<html lang="tr" class="theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FEE CARS | Mesajlar</title>
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
        <span id="whoami" class="muted">Kontrol ediliyor...</span>
        <button id="btnLogout" class="btn btn-ghost hidden">Cikis</button>
      </div>
    </div>
  </header>

  <main class="chat-shell">
    <div id="notice" class="notice hidden"></div>

    <section class="chat-layout">
      <aside class="chat-sidebar">
        <div class="chat-side-head">
          <h2>Mesajlar</h2>
          <p class="muted">Admin ve kullanici sohbetleri</p>
        </div>
        <div id="chatList" class="chat-list"></div>
      </aside>

      <section class="chat-main">
        <header class="chat-main-head" id="chatHead">
          <h3>Bir sohbet secin</h3>
          <p class="muted">Mesajlasma burada gorunecek.</p>
        </header>

        <div id="chatMessages" class="chat-messages"></div>

        <footer class="chat-compose">
          <textarea id="chatInput" rows="2" placeholder="Mesajinizi yazin..."></textarea>
          <button id="chatSend" class="btn btn-primary">Gonder</button>
        </footer>
      </section>
    </section>
  </main>

  <script>
    window.BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_UNICODE) ?>;
    window.PAGE = 'messages';
  </script>
  <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/js/app.js"></script>
</body>
</html>
