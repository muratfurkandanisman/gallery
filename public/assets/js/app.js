const state = {
  user: null,
  cars: [],
  favorites: [],
  chats: [],
  activeConversationId: null,
  chatPollHandle: null,
  adminCars: [],
  inquiries: [],
};

const $ = (s) => document.querySelector(s);
const $$ = (s) => document.querySelectorAll(s);

function money(v) {
  const n = Number(v || 0);
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
    maximumFractionDigits: 0,
  }).format(n);
}

async function api(path, options = {}) {
  const base = window.BASE_URL || '';
  const candidates = [
    `${base}`,
    `${base}/index.php`,
  ].filter((v, i, a) => a.indexOf(v) === i);

  const cfg = {
    headers: { 'Content-Type': 'application/json' },
    credentials: 'same-origin',
    ...options,
  };

  if (cfg.body && typeof cfg.body !== 'string') {
    cfg.body = JSON.stringify(cfg.body);
  }

  let lastError = null;

  for (let i = 0; i < candidates.length; i++) {
    const res = await fetch(`${candidates[i]}${path}`, cfg);
    const data = await res.json().catch(() => ({}));

    // If pretty URL cannot handle the method, retry with index.php prefix.
    if ((res.status === 404 || res.status === 405 || res.status === 501) && i < candidates.length - 1) {
      continue;
    }

    if (!res.ok || data.success === false) {
      lastError = data.message || 'Islem basarisiz.';
      break;
    }

    return data;
  }

  throw new Error(lastError || 'Islem basarisiz.');
}

function toast(msg) {
  const el = $('#notice');
  if (!el) return;
  el.textContent = msg;
  el.classList.remove('hidden');
  setTimeout(() => el.classList.add('hidden'), 2500);
}

function esc(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function parseImageUrls(value) {
  return String(value || '')
    .split(/[\r\n,]+/)
    .map((item) => item.trim())
    .filter(Boolean);
}

function favoriteHeartButton(carId, active, extraClass = '') {
  const stateClass = active ? 'is-favorite' : 'is-empty';
  const label = active ? 'Favoriden cikar' : 'Favoriye ekle';
  return `
    <button class="fav-heart ${stateClass} ${extraClass}" data-fav="${carId}" aria-label="${label}" title="${label}">
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12 21s-6.7-4.16-9.33-8.12C.47 9.23 1.5 5.55 4.61 4.27c2.1-.86 4.45-.28 5.99 1.33L12 7.02l1.4-1.42c1.54-1.61 3.89-2.19 5.99-1.33 3.11 1.28 4.14 4.96 1.94 8.61C18.7 16.84 12 21 12 21z" />
      </svg>
    </button>
  `;
}

async function loadMe() {
  const data = await api('/api/auth/me');
  state.user = data.user;
  return state.user;
}

async function loadFavorites() {
  if (!state.user || !state.user.permissions?.can_use_favorites) {
    state.favorites = [];
    return;
  }
  const data = await api('/api/favorites');
  state.favorites = data.data || [];
}

async function logoutAndGo() {
  await api('/api/auth/logout', { method: 'POST' });
  location.href = appUrl('/');
}

function goAccess() {
  location.href = appUrl('/access');
}

function appUrl(path) {
  const base = window.BASE_URL || '';
  const normalizedPath = path.startsWith('/') ? path : `/${path}`;
  if ((window.location.pathname || '').includes('/index.php')) {
    return `${base}/index.php${normalizedPath}`;
  }
  return `${base}${normalizedPath}`;
}

function attachHeaderActions() {
  const whoami = $('#whoami');
  if (whoami) {
    whoami.textContent = state.user ? `${state.user.full_name} (${state.user.role})` : 'Misafir';
  }

  const btnLogin = $('#btnLogin');
  if (btnLogin) btnLogin.classList.toggle('hidden', !!state.user);

  const btnLogout = $('#btnLogout');
  if (btnLogout) {
    btnLogout.classList.toggle('hidden', !state.user);
    btnLogout.onclick = logoutAndGo;
  }

  const btnFav = $('#btnFavorites');
  if (btnFav) {
    btnFav.classList.toggle('hidden', !(state.user && state.user.permissions?.can_use_favorites));
  }

  const navAdminLink = $('#navAdminLink');
  if (navAdminLink) {
    navAdminLink.classList.toggle('hidden', !(state.user && state.user.permissions?.can_view_admin_panel));
  }

  const navLogsLink = $('#navLogsLink');
  if (navLogsLink) {
    const canViewLogs = !!(state.user && (state.user.permissions?.can_view_admin_panel || state.user.role === 'ADMIN'));
    navLogsLink.classList.toggle('hidden', !canViewLogs);
  }

  const navMessagesLink = $('#navMessagesLink');
  if (navMessagesLink) {
    navMessagesLink.classList.toggle('hidden', !state.user);
  }
}

function renderShowroomCars() {
  const grid = $('#carsGrid');
  if (!grid) return;

  if (!state.cars.length) {
    grid.innerHTML = '<div class="panel">Arac bulunamadi.</div>';
    return;
  }

  const canUseFavorites = !!(state.user && state.user.permissions?.can_use_favorites);
  const showFavoriteAction = !state.user || canUseFavorites;

  // Debug: Log image paths
  console.log('Showroom Cars:', state.cars.map(car => ({
    id: car.CAR_ID,
    brand: car.BRAND,
    imagePath: car.IMAGE_PATH,
    allImages: car.IMAGES
  })));

  grid.innerHTML = state.cars.map((car) => {
    const fav = state.favorites.some((f) => Number(f.CAR_ID) === Number(car.CAR_ID));
    const base = window.BASE_URL || '';
    let imgSrc = car.IMAGE_PATH ? car.IMAGE_PATH : null;
    if (imgSrc && !imgSrc.startsWith('http')) {
      imgSrc = base + imgSrc;
    }
    if (!imgSrc) {
      imgSrc = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=900&q=80';
    }
    return `
      <article class="car-card">
        <img src="${imgSrc}" alt="${esc(car.BRAND)} ${esc(car.MODEL)}">
        <div class="car-body">
          <div class="row-between">
            <strong>${esc(car.BRAND)} ${esc(car.MODEL)}</strong>
            <span class="muted">${esc(car.YEAR)}</span>
          </div>
          <p class="price">${money(car.PRICE)}</p>
          <p class="muted">${Number(car.MILEAGE || 0).toLocaleString('tr-TR')} km</p>
          <div class="row-between">
            <a class="btn btn-ghost" href="${appUrl(`/vehicle/${car.CAR_ID}`)}">Detay</a>
            ${showFavoriteAction ? favoriteHeartButton(car.CAR_ID, fav, 'fav-heart-card') : ''}
          </div>
        </div>
      </article>
    `;
  }).join('');
}

async function loadCarsForShowroom() {
  const q = new URLSearchParams();
  const brand = $('#fBrand')?.value?.trim() || '';
  const model = $('#fModel')?.value?.trim() || '';
  const minPrice = $('#fMin')?.value?.trim() || '';
  const maxPrice = $('#fMax')?.value?.trim() || '';

  if (brand) q.set('brand', brand);
  if (model) q.set('model', model);
  if (minPrice) q.set('minPrice', minPrice);
  if (maxPrice) q.set('maxPrice', maxPrice);

  const data = await api(`/api/cars?${q.toString()}`);
  state.cars = data.data || [];
  renderShowroomCars();
}

async function toggleFavorite(carId) {
  if (!state.user || !state.user.permissions?.can_use_favorites) {
    goAccess();
    return;
  }

  await api(`/api/favorites/${carId}`, { method: 'POST' });
  await loadFavorites();
  renderShowroomCars();
}

function renderFavoriteModal() {
  const list = $('#favList');
  if (!list) return;
  if (!state.favorites.length) {
    list.innerHTML = '<p class="muted">Favori listen bos.</p>';
    return;
  }

  list.innerHTML = state.favorites.map((f) => `
    <div class="panel" style="margin-bottom:8px;">
      <div class="row-between">
        <strong>${esc(f.BRAND)} ${esc(f.MODEL)} (${esc(f.YEAR)})</strong>
        <span class="tag ${f.STATUS === 'SOLD' ? 'sold' : 'available'}">${esc(f.STATUS)}</span>
      </div>
      <div class="row-between" style="margin-top:8px;">
        <span class="muted">${money(f.PRICE)}</span>
        <a class="btn btn-ghost" href="${appUrl(`/vehicle/${f.CAR_ID}`)}">Detay</a>
      </div>
    </div>
  `).join('');
}

async function initShowroom() {
  await loadFavorites();
  attachHeaderActions();
  await loadCarsForShowroom();

  $('#applyFilter')?.addEventListener('click', loadCarsForShowroom);

  $('#carsGrid')?.addEventListener('click', async (e) => {
    const favBtn = e.target.closest('[data-fav]');
    if (!favBtn) return;
    try {
      await toggleFavorite(Number(favBtn.dataset.fav));
    } catch (err) {
      toast(err.message);
    }
  });

  const favModal = $('#favoritesModal');
  $('#btnFavorites')?.addEventListener('click', async () => {
    await loadFavorites();
    renderFavoriteModal();
    favModal?.classList.add('open');
  });
  $('#closeFav')?.addEventListener('click', () => favModal?.classList.remove('open'));
}

function switchAuthMode(mode) {
  const form = $('#accessForm');
  const nameField = $('#nameField');
  const title = $('#accessTitle');
  const submit = $('#submitBtn');
  const toLogin = $('#toLogin');
  const toRegister = $('#toRegister');

  form.dataset.mode = mode;
  if (mode === 'register') {
    nameField.classList.remove('hidden');
    title.textContent = 'Kayit Ol';
    submit.textContent = 'Hesap Olustur';
    toRegister.classList.add('active');
    toLogin.classList.remove('active');
  } else {
    nameField.classList.add('hidden');
    title.textContent = 'Giris Yap';
    submit.textContent = 'Giris Yap';
    toLogin.classList.add('active');
    toRegister.classList.remove('active');
  }
}

function validateAccessForm(mode) {
  const email = $('#email')?.value?.trim() || '';
  const password = $('#password')?.value || '';

  if (!email || !email.includes('@')) {
    return 'Gecerli bir e-posta giriniz.';
  }
  if (password.length < 6) {
    return 'Sifre en az 6 karakter olmalidir.';
  }

  if (mode === 'register') {
    const fullName = $('#fullName')?.value?.trim() || '';
    if (fullName.length < 2) {
      return 'Ad soyad en az 2 karakter olmalidir.';
    }
  }

  return null;
}

async function initAccess() {
  if (state.user) {
    location.href = appUrl('/');
    return;
  }

  $('#toLogin')?.addEventListener('click', () => switchAuthMode('login'));
  $('#toRegister')?.addEventListener('click', () => switchAuthMode('register'));

  $('#accessForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const mode = e.currentTarget.dataset.mode;

    const validationError = validateAccessForm(mode);
    if (validationError) {
      toast(validationError);
      return;
    }

    try {
      if (mode === 'register') {
        await api('/api/auth/register', {
          method: 'POST',
          body: {
            full_name: $('#fullName').value.trim(),
            email: $('#email').value.trim(),
            password: $('#password').value,
          },
        });
        toast('Kayit basarili. Simdi giris yapabilirsiniz.');
        switchAuthMode('login');
        return;
      }

      await api('/api/auth/login', {
        method: 'POST',
        body: {
          email: $('#email').value.trim(),
          password: $('#password').value,
        },
      });

      location.href = appUrl('/');
    } catch (err) {
      toast(err.message);
    }
  });
}

async function initDetail() {

  attachHeaderActions();
  await loadFavorites();

  const data = await api(`/api/cars/${window.CAR_ID}`);
  const c = data.data;

  // Debug: Log image data
  console.log('Car Details - IMAGES:', c.IMAGES);
  console.log('Car Details - Raw car object:', c);

  const images = (c.IMAGES || []).map((x) => x.IMAGE_PATH).filter(Boolean);
  const base = window.BASE_URL || '';
  let hero = images[0] ? images[0] : null;
  if (hero && !hero.startsWith('http')) {
    hero = base + hero;
  }
  if (!hero) {
    hero = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1600&q=80';
  }

  console.log('Final hero image src:', hero);

  const upperTitle = `${c.BRAND || ''} ${c.MODEL || ''}`.trim().toUpperCase();
  const isFavorite = state.favorites.some((f) => Number(f.CAR_ID) === Number(c.CAR_ID));

  $('#dHeroImage').src = hero;

  $('#dAssetId').textContent = `${c.BRAND || ''} ${c.MODEL || ''} (VE-${String(c.CAR_ID).padStart(3, '0')})`;
  $('#dTag').textContent = c.STATUS === 'SOLD' ? 'Asset Archived' : 'Asset Ready';
  $('#dMainTitle').textContent = upperTitle || 'VELOCITY ASSET';
  $('#dDescription').textContent = c.DESCRIPTION || 'Curated for elite delivery and certified technical provenance.';

  $('#dPrice').textContent = money(c.PRICE);
  $('#dMileage').textContent = `${Number(c.MILEAGE || 0).toLocaleString('tr-TR')} km`;
  $('#dColor').textContent = c.COLOR || '-';
  $('#detailFavSlot').innerHTML = state.user && state.user.permissions?.can_use_favorites ? favoriteHeartButton(c.CAR_ID, isFavorite, 'fav-heart-detail') : '';

  $('#dSpecLines').innerHTML = `
    <div class="ve-line"><span>Exterior Finish</span><strong>${esc(c.COLOR || '-')}</strong></div>
    <div class="ve-line"><span>Fuel Type</span><strong>${esc(c.FUEL_TYPE || '-')}</strong></div>
    <div class="ve-line"><span>Transmission</span><strong>${esc(c.GEAR_TYPE || '-')}</strong></div>
    <div class="ve-line"><span>Mileage</span><strong>${Number(c.MILEAGE || 0).toLocaleString('tr-TR')} km</strong></div>
    <div class="ve-line"><span>Model Year</span><strong>${esc(c.YEAR || '-')}</strong></div>
    <div class="ve-line"><span>Valuation</span><strong>${money(c.PRICE)}</strong></div>
  `;

  const canSendInquiry = !!(state.user && state.user.permissions?.can_send_inquiry);
  if (state.user && !canSendInquiry) {
    $('#btnInq')?.classList.add('hidden');
    $('#inqMessage')?.classList.add('hidden');
  }

  $('#detailFavSlot')?.addEventListener('click', async (e) => {
    const favBtn = e.target.closest('[data-fav]');
    if (!favBtn) return;

    try {
      if (!state.user || !state.user.permissions?.can_use_favorites) {
        goAccess();
        return;
      }
      await api(`/api/favorites/${window.CAR_ID}`, { method: 'POST' });
      await loadFavorites();
      const refreshed = state.favorites.some((f) => Number(f.CAR_ID) === Number(window.CAR_ID));
      $('#detailFavSlot').innerHTML = favoriteHeartButton(window.CAR_ID, refreshed, 'fav-heart-detail');
      renderShowroomCars();
      toast('Favori durumu guncellendi.');
    } catch (err) {
      toast(err.message);
    }
  });

  $('#btnInq')?.addEventListener('click', async () => {
    try {
      if (!state.user || !state.user.permissions?.can_send_inquiry) {
        goAccess();
        return;
      }
      const draft = $('#inqMessage').value.trim();
      const message = draft || 'Merhaba, bu arac hakkinda detayli bilgi almak istiyorum.';
      const data = await api('/api/chats/start', {
        method: 'POST',
        body: { car_id: window.CAR_ID, message },
      });
      $('#inqMessage').value = '';
      location.href = appUrl(`/messages?conversation_id=${data.conversation_id}`);
    } catch (err) {
      toast(err.message);
    }
  });
}

function chatLabel(item) {
  const car = `${item.BRAND} ${item.MODEL}`;
  if (state.user?.permissions?.can_view_admin_panel) {
    return `${item.FULL_NAME} - ${car}`;
  }
  return car;
}

function getConversationFromQuery() {
  const q = new URLSearchParams(window.location.search);
  const id = Number(q.get('conversation_id') || '0');
  return Number.isFinite(id) && id > 0 ? id : null;
}

function renderChatList() {
  const list = $('#chatList');
  if (!list) return;

  if (!state.chats.length) {
    list.innerHTML = '<p class="muted">Henuz sohbet yok.</p>';
    return;
  }

  list.innerHTML = state.chats.map((item) => {
    const active = Number(item.CONVERSATION_ID) === Number(state.activeConversationId);
    const unread = Number(item.UNREAD_COUNT || 0);

    return `
      <button class="chat-item ${active ? 'active' : ''}" data-chat="${item.CONVERSATION_ID}">
        <div class="chat-item-top">
          <strong>${esc(chatLabel(item))}</strong>
          <span class="muted">${esc((item.LAST_MESSAGE_AT || '').slice(0, 16).replace('T', ' '))}</span>
        </div>
        <div class="chat-item-sub">
          <span>${esc(item.LAST_MESSAGE || 'Yeni sohbet')}</span>
          ${unread > 0 ? `<span class="chat-badge">${unread}</span>` : ''}
        </div>
      </button>
    `;
  }).join('');
}

function renderChatMessages(messages) {
  const wrap = $('#chatMessages');
  if (!wrap) return;

  if (!messages.length) {
    wrap.innerHTML = '<p class="muted">Mesaj bekleniyor...</p>';
    return;
  }

  const isAdmin = !!state.user?.permissions?.can_view_admin_panel;
  wrap.innerHTML = messages.map((m) => {
    const mine = (isAdmin && m.SENDER_ROLE === 'ADMIN') || (!isAdmin && m.SENDER_ROLE === 'USER');
    return `
      <div class="chat-row ${mine ? 'mine' : 'theirs'}">
        <div class="chat-bubble">
          <p>${esc(m.MESSAGE_TEXT)}</p>
          <time>${esc((m.CREATED_AT || '').slice(0, 16).replace('T', ' '))}</time>
        </div>
      </div>
    `;
  }).join('');

  wrap.scrollTop = wrap.scrollHeight;
}

async function loadChatMessages(conversationId) {
  const res = await api(`/api/chats/${conversationId}/messages`);
  renderChatMessages(res.data || []);
}

async function loadChats() {
  const res = await api('/api/chats');
  state.chats = res.data || [];
  renderChatList();
}

async function setActiveConversation(conversationId) {
  state.activeConversationId = Number(conversationId);
  renderChatList();

  const current = state.chats.find((x) => Number(x.CONVERSATION_ID) === Number(conversationId));
  if (current) {
    $('#chatHead').innerHTML = `
      <h3>${esc(chatLabel(current))}</h3>
      <p class="muted">${esc(current.BRAND)} ${esc(current.MODEL)} ${esc(current.YEAR)}</p>
    `;
  }

  await loadChatMessages(conversationId);
}

async function initMessages() {
  attachHeaderActions();
  if (!state.user) {
    goAccess();
    return;
  }

  await loadChats();

  const fromUrl = getConversationFromQuery();
  if (fromUrl) {
    await setActiveConversation(fromUrl);
  } else if (state.chats.length) {
    await setActiveConversation(state.chats[0].CONVERSATION_ID);
  }

  $('#chatList')?.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-chat]');
    if (!btn) return;
    await setActiveConversation(Number(btn.dataset.chat));
  });

  $('#chatSend')?.addEventListener('click', async () => {
    if (!state.activeConversationId) return;
    const input = $('#chatInput');
    const message = (input?.value || '').trim();
    if (!message) return;

    try {
      await api(`/api/chats/${state.activeConversationId}/messages`, {
        method: 'POST',
        body: { message },
      });
      input.value = '';
      await loadChats();
      await loadChatMessages(state.activeConversationId);
    } catch (err) {
      toast(err.message);
    }
  });

  state.chatPollHandle = window.setInterval(async () => {
    try {
      await loadChats();
      if (state.activeConversationId) {
        await loadChatMessages(state.activeConversationId);
      }
    } catch {
      // Poll failures are transient; keep UI usable.
    }
  }, 5000);
}

async function loadAdminCars(status = '') {
  const q = status ? `?status=${encodeURIComponent(status)}` : '';
  const data = await api(`/api/admin/cars${q}`);
  state.adminCars = data.data || [];

  $('#adminCarsTable').innerHTML = state.adminCars.map((c) => `
    <tr>
      <td>${c.CAR_ID}</td>
      <td>${esc(c.BRAND)} ${esc(c.MODEL)}</td>
      <td>${esc(c.YEAR)}</td>
      <td>${money(c.PRICE)}</td>
      <td><span class="tag ${c.STATUS === 'SOLD' ? 'sold' : 'available'}">${esc(c.STATUS)}</span></td>
      <td class="row-between">
        ${c.STATUS === 'AVAILABLE' ? `<button class="btn btn-warn" data-sold="${c.CAR_ID}">Satildi Yap</button>` : '<span>-</span>'}
        <div class="action-icons">
          <button class="btn-edit-icon" data-edit="${c.CAR_ID}" aria-label="Araci guncelle" title="Araci guncelle">
            <span class="material-symbols-outlined" aria-hidden="true">edit</span>
          </button>
          <button class="btn-trash-icon" data-delete="${c.CAR_ID}" aria-label="Araci sil" title="Araci sil">
            <span class="material-symbols-outlined" aria-hidden="true">delete</span>
          </button>
        </div>
      </td>
    </tr>
  `).join('');
}

async function loadAdminInquiries() {
  const data = await api('/api/admin/inquiries');
  state.inquiries = data.data || [];

  $('#adminInqTable').innerHTML = state.inquiries.map((i) => `
    <tr>
      <td>${i.INQUIRY_ID}</td>
      <td>${esc(i.FULL_NAME)}</td>
      <td>${esc(i.BRAND)} ${esc(i.MODEL)}</td>
      <td>${esc(i.MESSAGE)}</td>
      <td>${esc(i.STATUS)}</td>
      <td>
        <select data-inq="${i.INQUIRY_ID}">
          <option ${i.STATUS === 'NEW' ? 'selected' : ''}>NEW</option>
          <option ${i.STATUS === 'IN_PROGRESS' ? 'selected' : ''}>IN_PROGRESS</option>
          <option ${i.STATUS === 'CLOSED' ? 'selected' : ''}>CLOSED</option>
        </select>
      </td>
    </tr>
  `).join('');
}

async function initLogs() {
  attachHeaderActions();
  // Allow admin role OR permissions flag
  if (!state.user || !(state.user.permissions?.can_view_admin_panel || state.user.role === 'ADMIN')) {
    location.href = appUrl('/access');
    return;
  }

  let page = 1;

  async function loadPage(p) {
    try {
      const data = await api(`/api/admin/logs?page=${p}`);
      const logs = data.data || [];
      page = data.page || p;
      $('#logsPage').textContent = `Sayfa ${page}`;

      $('#logsTableBody').innerHTML = logs.map((l) => {
        let details = '';
        if (l.details && typeof l.details === 'object') {
          details = Object.entries(l.details).map(([k,v]) => `${k}: ${esc(v)}`).join('<br>');
        } else if (l.details) {
          details = esc(JSON.stringify(l.details));
        }
        return `
          <tr>
            <td>${esc(l.log_id)}</td>
            <td>${esc(l.user_id)}</td>
            <td>${esc(l.action)}</td>
            <td>${details}</td>
            <td>${esc(l.created_at)}</td>
          </tr>
        `;
      }).join('');
    } catch (err) {
      toast(err.message);
    }
  }

  $('#logsPrev')?.addEventListener('click', async () => {
    if (page <= 1) return;
    await loadPage(page - 1);
  });

  $('#logsNext')?.addEventListener('click', async () => {
    await loadPage(page + 1);
  });

  await loadPage(1);
}

async function initAdmin() {
  attachHeaderActions();
  if (!state.user || !state.user.permissions?.can_view_admin_panel) {
    location.href = appUrl('/access');
    return;
  }

  await loadAdminCars();
  
  // Debug: Log loaded cars to check image paths
  console.log('Admin Cars:', state.adminCars.map(car => ({
    id: car.CAR_ID,
    brand: car.BRAND,
    images: car.IMAGES
  })));
  await loadAdminInquiries();

  $$('.admin-tab').forEach((btn) => {
    btn.addEventListener('click', async () => {
      $$('.admin-tab').forEach((x) => x.classList.remove('active'));
      btn.classList.add('active');
      try {
        await loadAdminCars(btn.dataset.status || '');
      } catch (err) {
        toast(err.message);
      }
    });
  });

  $('#adminCarsTable')?.addEventListener('click', async (e) => {
    const edit = e.target.closest('[data-edit]');
    if (edit) {
      location.href = appUrl(`/admin/cars/${edit.dataset.edit}/edit`);
      return;
    }

    const sold = e.target.closest('[data-sold]');
    if (sold) {
      try {
        await api(`/api/admin/cars/${sold.dataset.sold}/mark-sold`, { method: 'POST' });
        await loadAdminCars();
        toast('Arac satildi olarak guncellendi.');
      } catch (err) {
        toast(err.message);
      }
      return;
    }

    const del = e.target.closest('[data-delete]');
    if (del) {
      const ok = window.confirm('Bu araci veritabanindan kalici olarak silmek istediginize emin misiniz?');
      if (!ok) return;

      try {
        await api(`/api/admin/cars/${del.dataset.delete}`, { method: 'DELETE' });
        await loadAdminCars();
        toast('Arac kalici olarak silindi.');
      } catch (err) {
        toast(err.message);
      }
    }
  });

  $('#adminInqTable')?.addEventListener('change', async (e) => {
    const sel = e.target.closest('select[data-inq]');
    if (!sel) return;

    try {
      await api(`/api/admin/inquiries/${sel.dataset.inq}`, {
        method: 'PUT',
        body: { status: sel.value },
      });
      toast('Talep durumu guncellendi.');
    } catch (err) {
      toast(err.message);
    }
  });

  // File upload handler for admin form
  $('#adminImageUploadBtn')?.addEventListener('click', () => {
    $('#adminImageFileInput')?.click();
  });

  $('#adminImageFileInput')?.addEventListener('change', async (e) => {
    const files = Array.from(e.target.files || []);
    if (files.length === 0) return;

    for (const file of files) {
      try {
        console.log('Uploading file:', file.name, 'size:', file.size, 'type:', file.type);
        
        const formData = new FormData();
        formData.append('file', file);

        const base = window.BASE_URL || '';
        const uploadUrl = `${base}/api/admin/upload`;
        console.log('Upload URL:', uploadUrl);
        
        const res = await fetch(uploadUrl, {
          method: 'POST',
          body: formData,
          credentials: 'same-origin',
        });

        console.log('Upload response status:', res.status);
        
        if (!res.ok) {
          const error = await res.json().catch(() => ({}));
          console.error('Upload error response:', error);
          throw new Error(error.message || 'Yükleme başarısız');
        }

        const data = await res.json();
        console.log('Upload success response:', data);
        
        if (data.success) {
          const currentValue = $('#aImages').value.trim();
          $('#aImages').value = currentValue ? currentValue + '\n' + data.path : data.path;
          console.log('Added to textarea:', data.path);
        } else {
          throw new Error(data.message || 'Yükleme başarısız');
        }
      } catch (err) {
        console.error('Upload error:', err);
        toast(`Hata: ${err.message}`);
      }
    }
    
    // Reset file input
    e.target.value = '';
    toast('Resimler yüklendi.');
  });

  $('#adminCarForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
      brand: $('#aBrand').value.trim(),
      model: $('#aModel').value.trim(),
      year: Number($('#aYear').value),
      price: Number($('#aPrice').value),
      mileage: Number($('#aMileage').value),
      fuel_type: $('#aFuel').value.trim(),
      gear_type: $('#aGear').value.trim(),
      color: $('#aColor').value.trim(),
      images: parseImageUrls($('#aImages').value),
      description: $('#aDesc').value.trim(),
    };

    const required = [payload.brand, payload.model, payload.year, payload.price, payload.mileage];
    if (required.some((v) => v === '' || Number.isNaN(Number(v)) || Number(v) <= 0)) {
      toast('Marka, model, yil, fiyat ve km alanlarini gecerli doldurun.');
      return;
    }

    try {
      await api('/api/admin/cars', { method: 'POST', body: payload });
      e.target.reset();
      await loadAdminCars();
      toast('Arac eklendi.');
    } catch (err) {
      toast(err.message);
    }
  });
}

async function initAdminEdit() {
  attachHeaderActions();
  if (!state.user || !state.user.permissions?.can_view_admin_panel) {
    location.href = appUrl('/access');
    return;
  }

  const carId = Number(window.CAR_ID || 0);
  if (!carId) {
    toast('Gecersiz arac id.');
    return;
  }

  try {
    const data = await api(`/api/cars/${carId}`);
    const car = data.data || {};

    $('#eBrand').value = car.BRAND || '';
    $('#eModel').value = car.MODEL || '';
    $('#eYear').value = car.YEAR || '';
    $('#ePrice').value = car.PRICE || '';
    $('#eMileage').value = car.MILEAGE || '';
    $('#eFuel').value = car.FUEL_TYPE || '';
    $('#eGear').value = car.GEAR_TYPE || '';
    $('#eColor').value = car.COLOR || '';
    $('#eImages').value = (car.IMAGES || []).map((img) => img.IMAGE_PATH).filter(Boolean).join('\n');
    $('#eStatus').value = car.STATUS || 'AVAILABLE';
    $('#eDesc').value = car.DESCRIPTION || '';
  } catch (err) {
    toast(err.message);
    return;
  }

  // File upload handler
  $('#imageUploadBtn')?.addEventListener('click', () => {
    $('#imageFileInput')?.click();
  });

  $('#imageFileInput')?.addEventListener('change', async (e) => {
    const files = Array.from(e.target.files || []);
    if (files.length === 0) return;

    for (const file of files) {
      try {
        console.log('Uploading file (edit):', file.name, 'size:', file.size, 'type:', file.type);
        
        const formData = new FormData();
        formData.append('file', file);

        const base = window.BASE_URL || '';
        const uploadUrl = `${base}/api/admin/upload`;
        console.log('Upload URL:', uploadUrl);
        
        const res = await fetch(uploadUrl, {
          method: 'POST',
          body: formData,
          credentials: 'same-origin',
        });

        console.log('Upload response status:', res.status);
        
        if (!res.ok) {
          const error = await res.json().catch(() => ({}));
          console.error('Upload error response:', error);
          throw new Error(error.message || 'Yükleme başarısız');
        }

        const data = await res.json();
        console.log('Upload success response:', data);
        
        if (data.success) {
          const currentValue = $('#eImages').value.trim();
          $('#eImages').value = currentValue ? currentValue + '\n' + data.path : data.path;
          console.log('Added to textarea:', data.path);
        } else {
          throw new Error(data.message || 'Yükleme başarısız');
        }
      } catch (err) {
        console.error('Upload error:', err);
        toast(`Hata: ${err.message}`);
      }
    }
    
    // Reset file input
    e.target.value = '';
    toast('Resimler yüklendi.');
  });

  $('#adminEditCarForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const payload = {
      brand: $('#eBrand').value.trim(),
      model: $('#eModel').value.trim(),
      year: $('#eYear').value.trim(),
      price: $('#ePrice').value.trim(),
      mileage: $('#eMileage').value.trim(),
      fuel_type: $('#eFuel').value.trim(),
      gear_type: $('#eGear').value.trim(),
      color: $('#eColor').value.trim(),
      images: parseImageUrls($('#eImages').value),
      status: $('#eStatus').value,
      description: $('#eDesc').value.trim(),
    };

    const required = [payload.brand, payload.model, payload.year, payload.price, payload.mileage];
    if (required.some((v) => v === '')) {
      toast('Lutfen zorunlu alanlari bos birakmayin.');
      return;
    }

    try {
      await api(`/api/admin/cars/${carId}`, {
        method: 'PUT',
        body: {
          ...payload,
          year: Number(payload.year),
          price: Number(payload.price),
          mileage: Number(payload.mileage),
        },
      });
      toast('Arac bilgileri guncellendi.');
    } catch (err) {
      toast(err.message);
    }
  });
}

async function boot() {
  try {
    await loadMe();
  } catch {
    state.user = null;
  }

  const page = window.PAGE || 'showroom';
  if (page === 'showroom') return initShowroom();
  if (page === 'access') return initAccess();
  if (page === 'detail') return initDetail();
  if (page === 'admin') return initAdmin();
  if (page === 'admin-edit') return initAdminEdit();
  if (page === 'messages') return initMessages();
  if (page === 'logs') return initLogs();
}

window.addEventListener('DOMContentLoaded', () => {
  boot().catch((err) => toast(err.message || 'Beklenmeyen hata.'));
});
