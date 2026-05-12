<?php require_once __DIR__ . '/_init.php'; $baseUrl = $baseUrl ?? env_base_url(); ?>
<!doctype html>
<html lang="tr" class="theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FEE CARS | Logs</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/css/style.css">
</head>
<body class="cine-body">
  <header class="topbar">
    <div class="topbar-inner">
      <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>\/" class="brand">FEE CARS</a>
      <nav>
        <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>\/">Showroom</a>
        <a id="navMessagesLink" class="hidden" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>\/messages">Mesajlar</a>
        <a id="navAdminLink" class="hidden" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>\/admin">Admin Paneli</a>
        <a id="navLogsLink" class="hidden" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>\/admin/logs">Logs</a>
      </nav>
      <div class="top-actions">
        <span id="whoami" class="muted">Misafir</span>
        <button id="btnFavorites" class="btn btn-ghost hidden">Favoriler</button>
        <a id="btnLogin" class="btn btn-ghost" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/access">Giris</a>
        <button id="btnLogout" class="btn btn-ghost hidden">Cikis</button>
      </div>
    </div>
  </header>

  <main class="admin-shell">
    <div id="notice" class="notice hidden"></div>

    <section class="panel">
      <h3>Kullanici Aktivite Loglari</h3>
      <div class="logs-controls" style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
        <button id="logsPrev" class="btn btn-ghost">◀</button>
        <div id="logsPage" class="muted">Sayfa 1</div>
        <button id="logsNext" class="btn btn-ghost">▶</button>
      </div>

      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th>ID</th><th>Kullanici ID</th><th>Action</th><th>Details</th><th>Zaman</th></tr>
          </thead>
          <tbody id="logsTableBody"></tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
    window.BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_UNICODE) ?>;
    window.PAGE = 'logs';
  </script>
  <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/public/assets/js/app.js"></script>
</body>
</html>
