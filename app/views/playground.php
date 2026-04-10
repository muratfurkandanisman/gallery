<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>API Playground</title>
  <style>
    :root { --bg:#0f1115; --card:#171b23; --line:#2a3140; --ink:#e9eefc; --muted:#9aa7c4; --ok:#2dd4bf; }
    * { box-sizing: border-box; }
    body { margin:0; font-family: Segoe UI, Arial, sans-serif; background:var(--bg); color:var(--ink); }
    .wrap { width:min(1200px,95%); margin:20px auto 60px; }
    h1 { margin:0 0 8px; }
    p { color:var(--muted); }
    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(330px,1fr)); gap:12px; }
    .card { background:var(--card); border:1px solid var(--line); border-radius:12px; padding:12px; }
    .row { display:flex; gap:8px; margin-bottom:8px; }
    input, select, textarea { width:100%; border:1px solid var(--line); background:#11151c; color:var(--ink); border-radius:8px; padding:9px; }
    textarea { min-height:100px; font-family: ui-monospace, Consolas, monospace; }
    button { border:1px solid #35507a; background:#1f3557; color:#fff; border-radius:8px; padding:9px 12px; cursor:pointer; }
    button:hover { filter:brightness(1.08); }
    .result { white-space:pre-wrap; background:#0b0f14; border:1px solid var(--line); border-radius:8px; padding:10px; margin-top:10px; min-height:120px; font-family: ui-monospace, Consolas, monospace; }
    .hint { font-size:12px; color:var(--muted); }
    .ok { color:var(--ok); }
    .path { font-family: ui-monospace, Consolas, monospace; color:#9cc3ff; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>API Playground</h1>
    <p>Bu sayfa Swagger gibi endpoint test etmen icin eklendi. Session/cookie kullanir, once login et.</p>
    <p class="hint">Base URL: <span class="path" id="base"></span></p>

    <div class="grid">
      <section class="card">
        <h3>Auth - Register</h3>
        <div class="hint path">POST /api/auth/register</div>
        <input id="reg_name" placeholder="Ad Soyad">
        <div class="row">
          <input id="reg_email" placeholder="email">
          <input id="reg_pass" placeholder="sifre" type="password">
        </div>
        <button data-endpoint="register">Gonder</button>
        <div id="out_register" class="result"></div>
      </section>

      <section class="card">
        <h3>Auth - Login / Me / Logout</h3>
        <div class="hint path">POST /api/auth/login, GET /api/auth/me, POST /api/auth/logout</div>
        <div class="row">
          <input id="log_email" placeholder="email">
          <input id="log_pass" placeholder="sifre" type="password">
        </div>
        <div class="row">
          <button data-endpoint="login">Login</button>
          <button data-endpoint="me">Me</button>
          <button data-endpoint="logout">Logout</button>
        </div>
        <div id="out_auth" class="result"></div>
      </section>

      <section class="card">
        <h3>Cars - List / Detail</h3>
        <div class="hint path">GET /api/cars, GET /api/cars/{id}</div>
        <div class="row">
          <input id="cars_brand" placeholder="brand">
          <input id="cars_model" placeholder="model">
        </div>
        <div class="row">
          <button data-endpoint="cars">Listele</button>
          <input id="car_id" placeholder="car id">
          <button data-endpoint="car_detail">Detay</button>
        </div>
        <div id="out_cars" class="result"></div>
      </section>

      <section class="card">
        <h3>Favorites</h3>
        <div class="hint path">GET /api/favorites, POST /api/favorites/{carId}, DELETE /api/favorites/{carId}</div>
        <div class="row">
          <button data-endpoint="fav_list">Liste</button>
          <input id="fav_car_id" placeholder="car id">
        </div>
        <div class="row">
          <button data-endpoint="fav_toggle">Toggle</button>
          <button data-endpoint="fav_delete">Sil</button>
        </div>
        <div id="out_fav" class="result"></div>
      </section>

      <section class="card">
        <h3>Inquiry</h3>
        <div class="hint path">POST /api/inquiries</div>
        <div class="row">
          <input id="inq_car_id" placeholder="car id">
        </div>
        <textarea id="inq_msg" placeholder="Mesaj..."></textarea>
        <button data-endpoint="inq_create">Gonder</button>
        <div id="out_inq" class="result"></div>
      </section>

      <section class="card">
        <h3>Admin - Cars</h3>
        <div class="hint path">GET /api/admin/cars, POST /api/admin/cars, POST /api/admin/cars/{id}/mark-sold</div>
        <div class="row">
          <select id="admin_status"><option value="">Tum</option><option value="AVAILABLE">AVAILABLE</option><option value="SOLD">SOLD</option></select>
          <button data-endpoint="admin_cars">Liste</button>
        </div>
        <textarea id="admin_car_payload">{"brand":"BMW","model":"M3","year":2022,"price":2200000,"mileage":35000,"fuel_type":"Benzin","gear_type":"Otomatik","color":"Siyah","description":"Playground arac"}</textarea>
        <div class="row">
          <button data-endpoint="admin_car_create">Arac Ekle</button>
          <input id="sold_car_id" placeholder="car id">
          <button data-endpoint="admin_mark_sold">Satildi Yap</button>
        </div>
        <div id="out_admin_cars" class="result"></div>
      </section>

      <section class="card">
        <h3>Admin - Inquiries</h3>
        <div class="hint path">GET /api/admin/inquiries, PUT /api/admin/inquiries/{id}</div>
        <div class="row">
          <button data-endpoint="admin_inq_list">Liste</button>
          <input id="admin_inq_id" placeholder="inquiry id">
          <select id="admin_inq_status"><option>NEW</option><option>IN_PROGRESS</option><option>CLOSED</option></select>
          <button data-endpoint="admin_inq_update">Guncelle</button>
        </div>
        <div id="out_admin_inq" class="result"></div>
      </section>
    </div>
  </div>

  <script>
    const BASE = <?= json_encode($baseUrl, JSON_UNESCAPED_UNICODE) ?>;
    document.getElementById('base').textContent = BASE || '/';

    async function callApi(path, method = 'GET', body = null) {
      const res = await fetch(`${BASE}${path}`, {
        method,
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: body ? JSON.stringify(body) : undefined,
      });
      const data = await res.json().catch(() => ({ raw: 'JSON degil' }));
      return { status: res.status, data };
    }

    function write(outId, payload) {
      document.getElementById(outId).textContent = JSON.stringify(payload, null, 2);
    }

    async function run(endpoint) {
      try {
        if (endpoint === 'register') {
          return write('out_register', await callApi('/api/auth/register', 'POST', {
            full_name: document.getElementById('reg_name').value,
            email: document.getElementById('reg_email').value,
            password: document.getElementById('reg_pass').value,
          }));
        }
        if (endpoint === 'login') {
          return write('out_auth', await callApi('/api/auth/login', 'POST', {
            email: document.getElementById('log_email').value,
            password: document.getElementById('log_pass').value,
          }));
        }
        if (endpoint === 'me') return write('out_auth', await callApi('/api/auth/me'));
        if (endpoint === 'logout') return write('out_auth', await callApi('/api/auth/logout', 'POST'));

        if (endpoint === 'cars') {
          const q = new URLSearchParams();
          const b = document.getElementById('cars_brand').value.trim();
          const m = document.getElementById('cars_model').value.trim();
          if (b) q.set('brand', b);
          if (m) q.set('model', m);
          return write('out_cars', await callApi(`/api/cars?${q.toString()}`));
        }
        if (endpoint === 'car_detail') {
          const id = document.getElementById('car_id').value.trim();
          return write('out_cars', await callApi(`/api/cars/${id}`));
        }

        if (endpoint === 'fav_list') return write('out_fav', await callApi('/api/favorites'));
        if (endpoint === 'fav_toggle') {
          const id = document.getElementById('fav_car_id').value.trim();
          return write('out_fav', await callApi(`/api/favorites/${id}`, 'POST'));
        }
        if (endpoint === 'fav_delete') {
          const id = document.getElementById('fav_car_id').value.trim();
          return write('out_fav', await callApi(`/api/favorites/${id}`, 'DELETE'));
        }

        if (endpoint === 'inq_create') {
          return write('out_inq', await callApi('/api/inquiries', 'POST', {
            car_id: Number(document.getElementById('inq_car_id').value),
            message: document.getElementById('inq_msg').value,
          }));
        }

        if (endpoint === 'admin_cars') {
          const s = document.getElementById('admin_status').value;
          return write('out_admin_cars', await callApi(`/api/admin/cars${s ? `?status=${encodeURIComponent(s)}` : ''}`));
        }
        if (endpoint === 'admin_car_create') {
          const payload = JSON.parse(document.getElementById('admin_car_payload').value || '{}');
          return write('out_admin_cars', await callApi('/api/admin/cars', 'POST', payload));
        }
        if (endpoint === 'admin_mark_sold') {
          const id = document.getElementById('sold_car_id').value.trim();
          return write('out_admin_cars', await callApi(`/api/admin/cars/${id}/mark-sold`, 'POST'));
        }

        if (endpoint === 'admin_inq_list') return write('out_admin_inq', await callApi('/api/admin/inquiries'));
        if (endpoint === 'admin_inq_update') {
          const id = document.getElementById('admin_inq_id').value.trim();
          const status = document.getElementById('admin_inq_status').value;
          return write('out_admin_inq', await callApi(`/api/admin/inquiries/${id}`, 'PUT', { status }));
        }
      } catch (err) {
        alert(err.message);
      }
    }

    document.querySelectorAll('button[data-endpoint]').forEach((btn) => {
      btn.addEventListener('click', () => run(btn.dataset.endpoint));
    });
  </script>
</body>
</html>
