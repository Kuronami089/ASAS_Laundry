/**
 * ASAS Laundry — Demo Interaktif Statis untuk GitHub Pages
 * --------------------------------------------------------
 * Meniru penuh tampilan & alur aplikasi Laravel 13 + MySQL di repository ini
 * menggunakan penyimpanan lokal browser (localStorage) agar dapat didemokan
 * langsung dari GitHub Pages (https://kuronami089.github.io/ASAS_Laundry/).
 */

(function () {
  'use strict';

  const STORAGE_KEY = 'asas_laundry_ghpages_v1';

  const USERS = [
    { id: 1, nama_lengkap: 'Faqih Rachman', username: 'admin', password: 'admin123', role: 'admin' },
    { id: 2, nama_lengkap: 'Nadia Putri',   username: 'kasir', password: 'kasir123', role: 'kasir' },
  ];

  function createInitialState() {
    const now = Date.now();
    const h = (n) => new Date(now - n * 3600 * 1000).toISOString();
    const d = (n) => new Date(now - n * 86400 * 1000).toISOString();

    return {
      nextLayananId: 4,
      nextTransaksiId: 15,
      layanan: [
        { id: 1, nama_layanan: 'Reguler',    harga_per_kg: 7000,  estimasi_selesai: '3-4 hari' },
        { id: 2, nama_layanan: 'Ekspres',    harga_per_kg: 12000, estimasi_selesai: '1-2 hari' },
        { id: 3, nama_layanan: 'Superkilat', harga_per_kg: 25000, estimasi_selesai: '5 jam' },
      ],
      transaksi: [
        { id: 1,  kode: 'LDR-0001', nama: 'Budi Santoso',    hp: '081234567890', layanan_id: 1, petugas: 'Nadia Putri',   berat: 4.0, harga: 7000,  total: 28000, status: 'Selesai', masuk: d(7),  selesai: d(4) },
        { id: 2,  kode: 'LDR-0002', nama: 'Siti Aminah',     hp: '085611223344', layanan_id: 2, petugas: 'Nadia Putri',   berat: 3.5, harga: 12000, total: 42000, status: 'Selesai', masuk: d(6),  selesai: d(5) },
        { id: 3,  kode: 'LDR-0003', nama: 'Andi Wijaya',     hp: '082198765432', layanan_id: 3, petugas: 'Faqih Rachman', berat: 2.0, harga: 25000, total: 50000, status: 'Selesai', masuk: d(5),  selesai: d(5) },
        { id: 4,  kode: 'LDR-0004', nama: 'Dewi Lestari',    hp: '087855667788', layanan_id: 1, petugas: 'Nadia Putri',   berat: 5.0, harga: 7000,  total: 35000, status: 'Selesai', masuk: d(5),  selesai: d(2) },
        { id: 5,  kode: 'LDR-0005', nama: 'Hendra Gunawan',  hp: '081377889900', layanan_id: 2, petugas: 'Faqih Rachman', berat: 4.5, harga: 12000, total: 54000, status: 'Selesai', masuk: d(4),  selesai: d(3) },
        { id: 6,  kode: 'LDR-0006', nama: 'Rina Kusuma',     hp: '085722334455', layanan_id: 1, petugas: 'Nadia Putri',   berat: 6.0, harga: 7000,  total: 42000, status: 'Selesai', masuk: d(4),  selesai: d(1) },
        { id: 7,  kode: 'LDR-0007', nama: 'Dimas Pratama',   hp: '082244556677', layanan_id: 3, petugas: 'Faqih Rachman', berat: 1.5, harga: 25000, total: 37500, status: 'Selesai', masuk: d(3),  selesai: d(3) },
        { id: 8,  kode: 'LDR-0008', nama: 'Kartika Sari',    hp: '081566778899', layanan_id: 2, petugas: 'Nadia Putri',   berat: 2.5, harga: 12000, total: 30000, status: 'Selesai', masuk: d(3),  selesai: d(2) },
        { id: 9,  kode: 'LDR-0009', nama: 'Fajar Ramadhan',  hp: '087711229988', layanan_id: 1, petugas: 'Nadia Putri',   berat: 7.5, harga: 7000,  total: 52500, status: 'Selesai', masuk: d(2),  selesai: h(8) },
        { id: 10, kode: 'LDR-0010', nama: 'Maya Anggraini',  hp: '081299001122', layanan_id: 3, petugas: 'Faqih Rachman', berat: 3.0, harga: 25000, total: 75000, status: 'Selesai', masuk: d(1),  selesai: h(19) },
        { id: 11, kode: 'LDR-0011', nama: 'Reza Mahendra',   hp: '085233445566', layanan_id: 2, petugas: 'Nadia Putri',   berat: 3.0, harga: 12000, total: 36000, status: 'Proses',  masuk: h(9),  selesai: null },
        { id: 12, kode: 'LDR-0012', nama: 'Putri Wulandari', hp: '081844552233', layanan_id: 1, petugas: 'Nadia Putri',   berat: 4.5, harga: 7000,  total: 31500, status: 'Proses',  masuk: h(6),  selesai: null },
        { id: 13, kode: 'LDR-0013', nama: 'Bagus Setiawan',  hp: '082166554433', layanan_id: 3, petugas: 'Faqih Rachman', berat: 2.5, harga: 25000, total: 62500, status: 'Proses',  masuk: h(3),  selesai: null },
        { id: 14, kode: 'LDR-0014', nama: 'Aulia Rahmawati', hp: '085877665544', layanan_id: 2, petugas: 'Nadia Putri',   berat: 5.0, harga: 12000, total: 60000, status: 'Proses',  masuk: h(1),  selesai: null },
      ],
    };
  }

  function loadState() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      if (raw) return JSON.parse(raw);
    } catch (_) {}
    const fresh = createInitialState();
    saveState(fresh);
    return fresh;
  }

  function saveState(s) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(s));
    } catch (_) {}
  }

  let db = loadState();
  let currentUser = null;
  let currentView = 'login';
  let editLayananId = null;
  let strukId = null;
  let flash = null;
  let filters = { q: '', status: 'semua', layanan_id: '' };
  let rekapGroup = 'status';
  let rekapPage = 1;

  const root = document.getElementById('app-root');
  const confirmDialog = document.getElementById('demo-confirm-dialog');
  const confirmTitle = document.getElementById('demo-dialog-title');
  const confirmBody = document.getElementById('demo-dialog-body');
  const confirmCancel = document.getElementById('demo-dialog-cancel');
  const confirmOk = document.getElementById('demo-dialog-confirm');
  let pendingConfirmAction = null;

  confirmCancel.addEventListener('click', () => confirmDialog.close());
  confirmOk.addEventListener('click', () => {
    const fn = pendingConfirmAction;
    pendingConfirmAction = null;
    confirmDialog.close();
    if (fn) fn();
  });

  function openConfirm(title, htmlBody, okLabel, onConfirm) {
    confirmTitle.textContent = title;
    confirmBody.innerHTML = htmlBody;
    confirmOk.textContent = okLabel;
    pendingConfirmAction = onConfirm;
    confirmDialog.showModal();
  }

  function esc(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function formatRupiah(val) {
    return 'Rp\u00A0' + new Intl.NumberFormat('id-ID').format(Math.round(Number(val) || 0));
  }

  function formatBerat(val) {
    const n = Number(val) || 0;
    const teks = Number.isInteger(n)
      ? n.toLocaleString('id-ID')
      : n.toLocaleString('id-ID', { maximumFractionDigits: 2 });
    return teks + '\u00A0kg';
  }

  function formatTanggal(iso) {
    if (!iso) return '-';
    const dt = new Date(iso);
    const bln = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const dd = String(dt.getDate()).padStart(2, '0');
    const hh = String(dt.getHours()).padStart(2, '0');
    const mm = String(dt.getMinutes()).padStart(2, '0');
    return `${dd} ${bln[dt.getMonth()]} ${dt.getFullYear()}, ${hh}:${mm}`;
  }

  function getLayanan(id) {
    return db.layanan.find((l) => l.id === Number(id)) || { nama_layanan: '-', estimasi_selesai: '-', harga_per_kg: 0 };
  }

  function setFlash(type, message) {
    flash = { type, message };
  }

  function navigate(view) {
    if (!currentUser && view !== 'login') {
      setFlash('error', 'Silakan login terlebih dahulu.');
      currentView = 'login';
      render();
      return;
    }
    if (view === 'layanan' && currentUser.role !== 'admin') {
      setFlash('error', 'Akses ditolak: pengelolaan Data Layanan hanya dapat dilakukan oleh Admin.');
      currentView = 'dashboard';
      render();
      return;
    }
    if (view !== 'layanan') editLayananId = null;
    currentView = view;
    render();
    window.scrollTo({ top: 0, behavior: 'instant' });
  }

  function initInkBleed() {
    const el = document.querySelector('[data-ink-bleed]');
    if (!el || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const SVG_NS = 'http://www.w3.org/2000/svg';
    const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
    const easeSoak = (x) => 1 - Math.pow(1 - x, 3);
    const makeSvg = (tag, attrs = {}) => {
      const node = document.createElementNS(SVG_NS, tag);
      for (const [k, v] of Object.entries(attrs)) node.setAttribute(k, String(v));
      return node;
    };

    const words = Array.from(el.querySelectorAll('[data-ink-word]'));
    const svg = makeSvg('svg', { class: 'ink-bleed__defs', width: 0, height: 0, 'aria-hidden': 'true' });
    const defs = makeSvg('defs');
    svg.append(defs);
    el.prepend(svg);

    const units = words.map((w, i) => {
      const fid = `gh-ink-${i}`;
      const f = makeSvg('filter', { id: fid, x: '-40%', y: '-60%', width: '180%', height: '220%', 'color-interpolation-filters': 'sRGB' });
      const turb = makeSvg('feTurbulence', { type: 'fractalNoise', baseFrequency: '0.08', numOctaves: '2', seed: String(i * 7 + 3), result: 'noise' });
      const disp = makeSvg('feDisplacementMap', { in: 'SourceGraphic', in2: 'noise', scale: '0', xChannelSelector: 'R', yChannelSelector: 'G', result: 'd' });
      const blur = makeSvg('feGaussianBlur', { in: 'd', stdDeviation: '0', result: 'b' });
      const haloT = makeSvg('feComponentTransfer', { in: 'b', result: 'halo' });
      const halo = makeSvg('feFuncA', { type: 'linear', slope: '0', intercept: '0' });
      haloT.append(halo);
      const inkT = makeSvg('feComponentTransfer', { in: 'b', result: 'ink' });
      const ink = makeSvg('feFuncA', { type: 'linear', slope: '1', intercept: '0' });
      inkT.append(ink);
      const merge = makeSvg('feMerge');
      merge.append(makeSvg('feMergeNode', { in: 'halo' }), makeSvg('feMergeNode', { in: 'ink' }));
      f.append(turb, disp, blur, haloT, inkT, merge);
      defs.append(f);
      w.style.opacity = '0';
      w.style.filter = `url(#${fid})`;
      return { el: w, fid, done: false, turb, disp, blur, ink, halo };
    });

    const hovers = new Map();
    const start = performance.now();
    let rafId = 0;

    const tick = (now) => {
      if (!document.body.contains(el)) return;
      const fsz = parseFloat(getComputedStyle(el).fontSize) || 34;
      const elapsed = (now - start) / 1000;
      let active = false;

      units.forEach((u, idx) => {
        const local = elapsed - 0.18 - idx * 0.14;
        let prog = easeSoak(clamp(local / 1.6));
        const hs = hovers.get(idx);
        if (hs !== undefined) {
          const h = (now - hs) / 1100;
          if (h >= 1) hovers.delete(idx);
          else {
            const wet = 0.55 * (h < 0.18 ? easeSoak(h / 0.18) : 1 - easeSoak((h - 0.18) / 0.82));
            prog = Math.min(prog, 1 - wet);
          }
        }
        if (prog >= 1 && local > 0) {
          if (!u.done) { u.done = true; u.el.style.filter = 'none'; u.el.style.opacity = '1'; }
          return;
        }
        active = true;
        if (u.done) { u.done = false; u.el.style.filter = `url(#${u.fid})`; }
        const q = 1 - prog;
        u.el.style.opacity = String(clamp(local / 0.19));
        u.turb.setAttribute('baseFrequency', (9 / fsz).toFixed(4));
        u.disp.setAttribute('scale', ((q * 10 * fsz) / 60).toFixed(2));
        u.blur.setAttribute('stdDeviation', ((q * 12 * fsz) / 100).toFixed(2));
        u.ink.setAttribute('slope', (1 + q * 16).toFixed(3));
        u.ink.setAttribute('intercept', (-q * 6).toFixed(3));
        u.halo.setAttribute('slope', (q * 0.55).toFixed(3));
      });

      rafId = active || hovers.size > 0 ? requestAnimationFrame(tick) : 0;
    };

    units.forEach((u, idx) => {
      u.el.addEventListener('pointerenter', () => {
        if (u.done && !hovers.has(idx)) {
          hovers.set(idx, performance.now());
          if (!rafId) rafId = requestAnimationFrame(tick);
        }
      });
    });

    rafId = requestAnimationFrame(tick);
  }

  function renderLogin() {
    const f = flash;
    flash = null;
    root.innerHTML = `
      <main class="auth-page">
        <div class="auth-bg" aria-hidden="true">
          <img class="auth-bg__photo" src="assets/img/login-laundry.jpg" width="896" height="1200" alt="" fetchpriority="high">
          <div class="auth-bg__scrim"></div>
        </div>

        <section class="auth-showcase" aria-label="Gambaran layanan ASAS Laundry">
          <div class="auth-showcase__content">
            <div class="ink-bleed-block">
              <p class="auth-showcase__headline ink-bleed" data-ink-bleed aria-label="Cucian rapi, setiap helai terjaga.">
                <span class="ink-bleed__line">
                  <span class="ink-bleed__word" data-ink-word>Cucian</span>
                  <span class="ink-bleed__word" data-ink-word>rapi,</span>
                </span>
                <span class="ink-bleed__line">
                  <span class="ink-bleed__word" data-ink-word>setiap</span>
                  <span class="ink-bleed__word" data-ink-word>helai</span>
                  <span class="ink-bleed__word" data-ink-word>terjaga.</span>
                </span>
              </p>
              <svg class="scribble-underline" viewBox="0 0 240 16" fill="none" aria-hidden="true">
                <path class="scribble-underline__path" d="M4 11 C58 4, 118 14, 182 7 C206 5, 224 8, 236 6"/>
              </svg>
            </div>
            <p class="auth-showcase__sub">
              Kelola pesanan harian, status pengerjaan, dan laporan keuangan laundry dalam satu ruang kerja yang tenang.
            </p>
          </div>
        </section>

        <section class="auth-panel" aria-labelledby="login-heading">
          <div class="auth-panel__inner">
            <div class="auth-brand">
              <img class="auth-brand__logo" src="assets/img/logo-asas-laundry.svg" width="360" height="272" alt="Logo ASAS Laundry">
              <div class="auth-brand__copy">
                <h1 id="login-heading">Masuk ke aplikasi</h1>
                <p class="text-muted">Demo interaktif GitHub Pages (kode penuh Laravel 13 + MySQL ada di repository).</p>
              </div>
            </div>

            ${f ? `<div class="alert ${f.type === 'success' ? 'alert--success' : 'alert--error'}" role="alert"><span>${esc(f.message)}</span></div>` : ''}

            <form class="form-stack" id="login-form">
              <div class="field">
                <label class="field__label" for="username">Username <span class="field__req">(wajib)</span></label>
                <div class="input-icon-wrap">
                  <input class="input" type="text" id="username" name="username" placeholder="Masukkan username" autocomplete="username" autocorrect="off" autocapitalize="none" spellcheck="false" required>
                  <svg class="input-icon-wrap__glyph" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                  </svg>
                </div>
              </div>
              <div class="field">
                <label class="field__label" for="password">Kata sandi <span class="field__req">(wajib)</span></label>
                <div class="password-wrap">
                  <input class="input" type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                  <button type="button" class="password-toggle" id="toggle-password" aria-pressed="false" aria-label="Tampilkan kata sandi">Tampilkan</button>
                </div>
              </div>
              <button type="submit" class="btn btn--primary btn--lg btn--full">Masuk</button>
            </form>

            <div class="demo-accounts">
              <p class="demo-accounts__title">Akun demo interaktif (klik untuk mengisi form)</p>
              <div class="demo-accounts__grid">
                <button type="button" class="demo-btn" data-fill-user="admin" data-fill-pass="admin123">
                  <strong>Admin (Faqih Rachman)</strong>
                  <span class="mono">admin / admin123</span>
                </button>
                <button type="button" class="demo-btn" data-fill-user="kasir" data-fill-pass="kasir123">
                  <strong>Kasir (Nadia Putri)</strong>
                  <span class="mono">kasir / kasir123</span>
                </button>
              </div>
            </div>
          </div>
        </section>
      </main>
    `;

    initInkBleed();

    const pwd = document.getElementById('password');
    const toggle = document.getElementById('toggle-password');
    toggle.addEventListener('click', () => {
      const showing = pwd.type === 'text';
      pwd.type = showing ? 'password' : 'text';
      toggle.setAttribute('aria-pressed', String(!showing));
      toggle.textContent = showing ? 'Tampilkan' : 'Sembunyikan';
    });

    document.querySelectorAll('[data-fill-user]').forEach((btn) => {
      btn.addEventListener('click', () => {
        document.getElementById('username').value = btn.dataset.fillUser;
        document.getElementById('password').value = btn.dataset.fillPass;
        document.getElementById('username').focus();
      });
    });

    document.getElementById('login-form').addEventListener('submit', (e) => {
      e.preventDefault();
      const u = document.getElementById('username').value.trim();
      const p = document.getElementById('password').value;
      const found = USERS.find((acc) => acc.username === u && acc.password === p);
      if (!found) {
        setFlash('error', 'Username atau kata sandi salah. Gunakan admin/admin123 atau kasir/kasir123.');
        render();
        return;
      }
      currentUser = found;
      setFlash('success', `Selamat datang, ${found.nama_lengkap}.`);
      navigate('dashboard');
    });
  }

  function renderStruk() {
    const trx = db.transaksi.find((t) => t.id === strukId);
    if (!trx) {
      navigate('rekap');
      return;
    }
    const lay = getLayanan(trx.layanan_id);

    root.innerHTML = `
      <main class="receipt-page">
        <div class="receipt-shell">
          <div class="receipt-toolbar">
            <button type="button" class="btn btn--quiet" id="back-rekap-btn">Kembali ke rekap</button>
            <button type="button" class="btn btn--primary" id="print-receipt-btn">Cetak struk</button>
          </div>

          <article class="receipt-card" aria-labelledby="receipt-title">
            <header class="receipt-head">
              <div class="brand-mark">
                <span class="brand-mark__seal" aria-hidden="true">
                  <img class="brand-mark__logo" src="assets/img/logo-asas-mark.svg" width="284" height="202" alt="">
                </span>
                <span class="brand-mark__text">
                  <h1 class="brand-mark__name" id="receipt-title">ASAS Laundry</h1>
                  <span class="brand-mark__sub">Struk Resmi Transaksi Cucian</span>
                </span>
              </div>
              <span class="badge ${trx.status === 'Selesai' ? 'badge--selesai' : 'badge--proses'}">${esc(trx.status)}</span>
            </header>

            <hr class="receipt-divider">
            <dl class="receipt-list">
              <div class="receipt-row"><dt>Kode transaksi</dt><dd class="mono">${esc(trx.kode)}</dd></div>
              <div class="receipt-row"><dt>Tanggal masuk</dt><dd class="mono">${esc(formatTanggal(trx.masuk))}</dd></div>
              ${trx.selesai ? `<div class="receipt-row"><dt>Tanggal selesai</dt><dd class="mono">${esc(formatTanggal(trx.selesai))}</dd></div>` : ''}
              <div class="receipt-row"><dt>Petugas</dt><dd>${esc(trx.petugas)}</dd></div>
            </dl>
            <hr class="receipt-divider">
            <dl class="receipt-list">
              <div class="receipt-row"><dt>Nama pelanggan</dt><dd>${esc(trx.nama)}</dd></div>
              <div class="receipt-row"><dt>Nomor HP</dt><dd class="mono">${esc(trx.hp)}</dd></div>
            </dl>
            <hr class="receipt-divider">
            <dl class="receipt-list">
              <div class="receipt-row"><dt>Jenis layanan</dt><dd>${esc(lay.nama_layanan)} (${esc(lay.estimasi_selesai)})</dd></div>
              <div class="receipt-row"><dt>Berat cucian</dt><dd class="mono">${esc(formatBerat(trx.berat))}</dd></div>
              <div class="receipt-row"><dt>Tarif per kg</dt><dd class="mono">${esc(formatRupiah(trx.harga))}</dd></div>
            </dl>
            <hr class="receipt-divider">
            <div class="receipt-total">
              <span>Total biaya</span>
              <span class="receipt-total__amount">${esc(formatRupiah(trx.total))}</span>
            </div>
            <p class="receipt-note">Simpan struk ini sebagai bukti pengambilan cucian di ASAS Laundry. Terima kasih atas kepercayaan Anda.</p>
          </article>
        </div>
      </main>
    `;

    document.getElementById('back-rekap-btn').addEventListener('click', () => navigate('rekap'));
    document.getElementById('print-receipt-btn').addEventListener('click', () => window.print());
  }

  function renderShell(innerHtml) {
    const f = flash;
    flash = null;
    const isAdmin = currentUser.role === 'admin';
    const roleLabel = isAdmin ? 'Admin' : 'Kasir';

    const menus = [
      { id: 'dashboard', label: 'Dashboard', admin: false },
      { id: 'layanan',   label: 'Data Layanan', admin: true },
      { id: 'transaksi', label: 'Transaksi Laundry', admin: false },
      { id: 'rekap',     label: 'Rekap Transaksi Laundry', admin: false },
    ].filter((m) => !m.admin || isAdmin);

    const navMarkup = `<ul class="nav-list">${menus.map((m) => `
      <li>
        <a class="nav-link" href="#${m.id}" data-nav="${m.id}" ${currentView === m.id ? 'aria-current="page"' : ''}>
          <span>${esc(m.label)}</span>
        </a>
      </li>`).join('')}</ul>`;

    root.innerHTML = `
      <div class="app-shell">
        <aside class="sidebar" aria-label="Navigasi utama">
          <a class="brand-mark" href="#dashboard" data-nav="dashboard">
            <span class="brand-mark__seal" aria-hidden="true">
              <img class="brand-mark__logo" src="assets/img/logo-asas-mark.svg" width="284" height="202" alt="">
            </span>
            <span class="brand-mark__text">
              <span class="brand-mark__name">ASAS Laundry</span>
              <span class="brand-mark__sub">Mode Demo GitHub Pages</span>
            </span>
          </a>
          <nav aria-label="Menu aplikasi">${navMarkup}</nav>
          <div class="sidebar__footer">
            <div class="user-meta">
              <span class="user-meta__name">${esc(currentUser.nama_lengkap)}</span>
              <span class="user-meta__role">Role ${roleLabel}</span>
            </div>
            <button type="button" class="btn btn--quiet btn--full" id="reset-demo-btn">Reset data demo</button>
            <button type="button" class="btn btn--quiet btn--full" id="logout-btn">Keluar</button>
          </div>
        </aside>

        <div class="app-main">
          <header class="mobile-topbar">
            <a class="brand-mark" href="#dashboard" data-nav="dashboard"><span class="brand-mark__name">ASAS Laundry</span></a>
            <div class="row-actions">
              <span class="user-meta__role">${roleLabel}</span>
              <button type="button" class="btn btn--quiet" id="mobile-logout-btn">Keluar</button>
            </div>
          </header>

          <main class="page-content" id="main-content">
            ${f ? `<div class="alert ${f.type === 'success' ? 'alert--success' : 'alert--error'}" role="status" aria-live="polite"><span>${esc(f.message)}</span></div>` : ''}
            ${innerHtml}
          </main>
        </div>
      </div>
    `;

    root.querySelectorAll('[data-nav]').forEach((link) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        navigate(link.dataset.nav);
      });
    });

    const doLogout = () => {
      currentUser = null;
      setFlash('success', 'Anda berhasil keluar dari aplikasi.');
      navigate('login');
    };
    document.getElementById('logout-btn')?.addEventListener('click', doLogout);
    document.getElementById('mobile-logout-btn')?.addEventListener('click', doLogout);

    document.getElementById('reset-demo-btn')?.addEventListener('click', () => {
      db = createInitialState();
      saveState(db);
      setFlash('success', 'Data demo dikembalikan ke 14 transaksi awal (Rp 636.000).');
      render();
    });
  }

  function renderDashboard() {
    const total = db.transaksi.length;
    const proses = db.transaksi.filter((t) => t.status === 'Proses');
    const selesaiCount = total - proses.length;
    const pendapatan = db.transaksi.reduce((acc, t) => acc + t.total, 0);

    const html = `
      <section class="page-header">
        <div class="page-header__title">
          <h1>Dashboard operasional</h1>
          <p class="page-header__desc">Pantau cucian yang sedang diproses, selesaikan order yang siap diambil, dan cek pendapatan secara langsung.</p>
        </div>
        <div class="page-header__actions">
          <a class="btn btn--primary" href="#transaksi" data-nav="transaksi">Catat transaksi baru</a>
          <a class="btn btn--quiet" href="#rekap" data-nav="rekap">Lihat rekap laporan</a>
        </div>
      </section>

      <section class="stat-strip" aria-label="Ringkasan transaksi">
        <div class="stat-item"><span class="stat-item__label">Total transaksi</span><span class="stat-item__value">${total}</span><span class="stat-item__note">Seluruh order tercatat</span></div>
        <div class="stat-item"><span class="stat-item__label">Sedang diproses</span><span class="stat-item__value">${proses.length}</span><span class="stat-item__note">Menunggu penyelesaian</span></div>
        <div class="stat-item"><span class="stat-item__label">Transaksi selesai</span><span class="stat-item__value">${selesaiCount}</span><span class="stat-item__note">Siap atau sudah diambil</span></div>
        <div class="stat-item"><span class="stat-item__label">Total pendapatan</span><span class="stat-item__value stat-item__value--accent">${esc(formatRupiah(pendapatan))}</span><span class="stat-item__note">Akumulasi nominal transaksi</span></div>
      </section>

      <div class="workspace-grid">
        <section class="panel">
          <div class="panel__head">
            <h2 class="panel__title">Tarif layanan aktif</h2>
            ${currentUser.role === 'admin' ? '<a class="btn btn--quiet" href="#layanan" data-nav="layanan">Kelola</a>' : ''}
          </div>
          <ul class="tariff-list">
            ${db.layanan.map((l) => `
              <li class="tariff-item">
                <div class="tariff-item__info">
                  <span class="tariff-item__name">${esc(l.nama_layanan)}</span>
                  <span class="tariff-item__est">Estimasi ${esc(l.estimasi_selesai)}</span>
                </div>
                <span class="tariff-item__price">${esc(formatRupiah(l.harga_per_kg))}/kg</span>
              </li>`).join('')}
          </ul>
        </section>

        <section class="panel">
          <div class="panel__head">
            <div>
              <h2 class="panel__title">Antrean cucian dalam proses</h2>
              <p class="panel__meta">Klik Tandai selesai saat cucian telah rampung dikerjakan.</p>
            </div>
            <span class="badge badge--proses">${proses.length} antrean</span>
          </div>
          ${proses.length === 0 ? '<div class="empty-state"><p class="empty-state__title">Semua cucian sudah selesai</p></div>' : `
            <div class="table-wrapper" tabindex="0">
              <table class="data-table">
                <thead><tr><th>Kode &amp; pelanggan</th><th>Layanan</th><th>Berat</th><th>Total</th><th>Status</th><th class="col-actions">Aksi</th></tr></thead>
                <tbody>
                  ${proses.slice().reverse().map((t) => {
                    const l = getLayanan(t.layanan_id);
                    return `<tr>
                      <th><span class="cell-primary">${esc(t.nama)}</span><span class="cell-sub mono">${esc(t.kode)} | ${esc(t.hp)}</span></th>
                      <td data-label="Layanan"><span class="cell-primary">${esc(l.nama_layanan)}</span><span class="cell-sub">${esc(l.estimasi_selesai)}</span></td>
                      <td data-label="Berat" class="col-num">${esc(formatBerat(t.berat))}</td>
                      <td data-label="Total" class="col-num">${esc(formatRupiah(t.total))}</td>
                      <td data-label="Status"><span class="badge badge--proses">Proses</span></td>
                      <td data-label="Aksi" class="col-actions"><button type="button" class="btn btn--quiet" data-mark-selesai="${t.id}">Tandai selesai</button></td>
                    </tr>`;
                  }).join('')}
                </tbody>
              </table>
            </div>`}
        </section>
      </div>
    `;

    renderShell(html);
    root.querySelectorAll('[data-mark-selesai]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const tx = db.transaksi.find((t) => t.id === Number(btn.dataset.markSelesai));
        if (tx) {
          tx.status = 'Selesai';
          tx.selesai = new Date().toISOString();
          saveState(db);
          setFlash('success', `Status transaksi ${tx.kode} berhasil diperbarui menjadi Selesai.`);
          render();
        }
      });
    });
  }

  function renderLayanan() {
    const editing = editLayananId ? db.layanan.find((l) => l.id === editLayananId) : null;

    const html = `
      <section class="page-header">
        <div class="page-header__title">
          <h1>Data layanan cuci</h1>
          <p class="page-header__desc">Tambah, ubah, atau hapus paket layanan. Layanan yang masih terhubung dengan transaksi dilindungi agar laporan tetap konsisten.</p>
        </div>
      </section>

      <div class="workspace-grid">
        <section class="panel">
          <div class="panel__head">
            <h2 class="panel__title">${editing ? 'Ubah data layanan' : 'Tambah layanan baru'}</h2>
            ${editing ? '<button type="button" class="btn btn--quiet" id="cancel-edit-btn">Batal ubah</button>' : ''}
          </div>
          <form class="form-stack" id="layanan-form">
            <div class="field">
              <label class="field__label" for="nama_layanan">Nama layanan</label>
              <input class="input" id="nama_layanan" required value="${esc(editing?.nama_layanan || '')}" placeholder="Contoh: Cuci Selimut">
            </div>
            <div class="field">
              <label class="field__label" for="harga_per_kg">Harga per kg (Rp)</label>
              <input class="input mono" id="harga_per_kg" type="number" min="1" step="500" required value="${esc(editing?.harga_per_kg || '')}" placeholder="15000">
            </div>
            <div class="field">
              <label class="field__label" for="estimasi_selesai">Estimasi selesai</label>
              <input class="input" id="estimasi_selesai" required value="${esc(editing?.estimasi_selesai || '')}" placeholder="2 hari">
            </div>
            <button type="submit" class="btn btn--primary">${editing ? 'Simpan perubahan' : 'Simpan layanan'}</button>
          </form>
        </section>

        <section class="panel">
          <div class="panel__head"><h2 class="panel__title">Daftar paket layanan</h2></div>
          <div class="table-wrapper" tabindex="0">
            <table class="data-table">
              <thead><tr><th>Nama layanan</th><th>Tarif per kg</th><th>Estimasi</th><th>Relasi transaksi</th><th class="col-actions">Aksi</th></tr></thead>
              <tbody>
                ${db.layanan.map((l) => {
                  const count = db.transaksi.filter((t) => t.layanan_id === l.id).length;
                  return `<tr>
                    <th><span class="cell-primary">${esc(l.nama_layanan)}</span></th>
                    <td data-label="Tarif per kg" class="col-num">${esc(formatRupiah(l.harga_per_kg))}</td>
                    <td data-label="Estimasi">${esc(l.estimasi_selesai)}</td>
                    <td data-label="Relasi"><span class="badge badge--neutral"><span class="mono">${count}</span> transaksi</span></td>
                    <td data-label="Aksi" class="col-actions">
                      <div class="row-actions">
                        <button type="button" class="btn btn--quiet" data-edit-layanan="${l.id}">Ubah</button>
                        <button type="button" class="btn btn--danger" data-del-layanan="${l.id}">Hapus</button>
                      </div>
                    </td>
                  </tr>`;
                }).join('')}
              </tbody>
            </table>
          </div>
        </section>
      </div>
    `;

    renderShell(html);

    document.getElementById('cancel-edit-btn')?.addEventListener('click', () => {
      editLayananId = null;
      render();
    });

    document.getElementById('layanan-form').addEventListener('submit', (e) => {
      e.preventDefault();
      const nama = document.getElementById('nama_layanan').value.trim();
      const harga = parseInt(document.getElementById('harga_per_kg').value, 10);
      const est = document.getElementById('estimasi_selesai').value.trim();
      if (!nama || !est || !Number.isFinite(harga) || harga <= 0) {
        setFlash('error', 'Harga per kg harus lebih besar dari 0 dan seluruh kolom wajib diisi.');
        render();
        return;
      }
      if (editing) {
        editing.nama_layanan = nama;
        editing.harga_per_kg = harga;
        editing.estimasi_selesai = est;
        editLayananId = null;
        setFlash('success', `Perubahan pada layanan "${nama}" berhasil disimpan.`);
      } else {
        db.layanan.push({ id: db.nextLayananId++, nama_layanan: nama, harga_per_kg: harga, estimasi_selesai: est });
        setFlash('success', `Layanan baru "${nama}" berhasil ditambahkan.`);
      }
      saveState(db);
      render();
    });

    root.querySelectorAll('[data-edit-layanan]').forEach((btn) => {
      btn.addEventListener('click', () => {
        editLayananId = Number(btn.dataset.editLayanan);
        render();
      });
    });

    root.querySelectorAll('[data-del-layanan]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const lay = db.layanan.find((l) => l.id === Number(btn.dataset.delLayanan));
        if (!lay) return;
        openConfirm(
          'Hapus data layanan?',
          `Anda akan menghapus layanan <strong>${esc(lay.nama_layanan)}</strong>. Jika masih terpakai di transaksi, penghapusan akan ditolak otomatis.`,
          'Ya, hapus layanan',
          () => {
            const used = db.transaksi.filter((t) => t.layanan_id === lay.id).length;
            if (used > 0) {
              setFlash('error', `Layanan "${lay.nama_layanan}" tidak dapat dihapus karena masih digunakan pada ${used} transaksi. Data laporan tetap terlindungi.`);
            } else {
              db.layanan = db.layanan.filter((l) => l.id !== lay.id);
              saveState(db);
              setFlash('success', `Layanan "${lay.nama_layanan}" berhasil dihapus.`);
            }
            render();
          }
        );
      });
    });
  }

  function renderTransaksi() {
    const proses = db.transaksi.filter((t) => t.status === 'Proses').slice().reverse();

    const html = `
      <section class="page-header">
        <div class="page-header__title">
          <h1>Transaksi laundry</h1>
          <p class="page-header__desc">Catat cucian masuk dengan perhitungan tarif otomatis berdasarkan jenis layanan dan berat.</p>
        </div>
        <div class="page-header__actions"><a class="btn btn--quiet" href="#rekap" data-nav="rekap">Buka rekap transaksi</a></div>
      </section>

      <div class="workspace-grid">
        <section class="panel">
          <div class="panel__head"><h2 class="panel__title">Catat transaksi baru</h2><span class="badge badge--proses">Status awal: Proses</span></div>
          <form class="form-stack" id="tx-form">
            <div class="field"><label class="field__label" for="tx-nama">Nama pelanggan</label><input class="input" id="tx-nama" required placeholder="Contoh: Budi Santoso"></div>
            <div class="field"><label class="field__label" for="tx-hp">Nomor HP pelanggan</label><input class="input mono" id="tx-hp" type="tel" required placeholder="081234567890"></div>
            <div class="field">
              <label class="field__label" for="tx-layanan">Jenis layanan</label>
              <select class="select" id="tx-layanan" required>
                <option value="">Pilih jenis layanan cuci</option>
                ${db.layanan.map((l) => `<option value="${l.id}" data-harga="${l.harga_per_kg}" data-est="${esc(l.estimasi_selesai)}">${esc(l.nama_layanan)} (${esc(formatRupiah(l.harga_per_kg))}/kg, ${esc(l.estimasi_selesai)})</option>`).join('')}
              </select>
            </div>
            <div class="field"><label class="field__label" for="tx-berat">Berat cucian (kg)</label><input class="input mono" id="tx-berat" type="number" min="0.1" max="500" step="0.1" required placeholder="3.5"></div>
            <div class="calc-box">
              <div class="calc-box__row"><span>Tarif layanan</span><span class="mono" id="calc-rate">-</span></div>
              <div class="calc-box__row"><span>Estimasi selesai</span><span id="calc-est">-</span></div>
              <div class="calc-box__total"><span>Total biaya (berat x tarif)</span><span class="calc-box__amount" id="calc-total">Rp&nbsp;0</span></div>
            </div>
            <button type="submit" class="btn btn--primary btn--lg btn--full">Simpan transaksi</button>
          </form>
        </section>

        <section class="panel">
          <div class="panel__head"><h2 class="panel__title">Cucian sedang diproses</h2></div>
          ${proses.length === 0 ? '<div class="empty-state"><p class="empty-state__title">Tidak ada cucian dalam antrean</p></div>' : `
            <div class="table-wrapper" tabindex="0">
              <table class="data-table">
                <thead><tr><th>Kode &amp; pelanggan</th><th>Layanan</th><th>Berat</th><th>Total</th><th>Status</th><th class="col-actions">Aksi</th></tr></thead>
                <tbody>
                  ${proses.map((t) => {
                    const l = getLayanan(t.layanan_id);
                    return `<tr>
                      <th><span class="cell-primary">${esc(t.nama)}</span><span class="cell-sub mono">${esc(t.kode)} | ${esc(t.hp)}</span></th>
                      <td data-label="Layanan"><span class="cell-primary">${esc(l.nama_layanan)}</span><span class="cell-sub mono">${esc(formatRupiah(t.harga))}/kg</span></td>
                      <td data-label="Berat" class="col-num">${esc(formatBerat(t.berat))}</td>
                      <td data-label="Total" class="col-num">${esc(formatRupiah(t.total))}</td>
                      <td data-label="Status"><span class="badge badge--proses">Proses</span></td>
                      <td data-label="Aksi" class="col-actions"><button type="button" class="btn btn--quiet" data-mark-selesai="${t.id}">Tandai selesai</button></td>
                    </tr>`;
                  }).join('')}
                </tbody>
              </table>
            </div>`}
        </section>
      </div>
    `;

    renderShell(html);

    const sel = document.getElementById('tx-layanan');
    const brt = document.getElementById('tx-berat');
    const updateCalc = () => {
      const opt = sel.options[sel.selectedIndex];
      const harga = Number(opt?.dataset.harga || 0);
      const berat = Number(brt.value || 0);
      document.getElementById('calc-rate').textContent = harga > 0 ? formatRupiah(harga) + ' / kg' : '-';
      document.getElementById('calc-est').textContent = opt?.dataset.est || '-';
      document.getElementById('calc-total').textContent = formatRupiah(harga > 0 && berat > 0 ? Math.round(harga * berat) : 0);
    };
    sel.addEventListener('change', updateCalc);
    brt.addEventListener('input', updateCalc);

    document.getElementById('tx-form').addEventListener('submit', (e) => {
      e.preventDefault();
      const nama = document.getElementById('tx-nama').value.trim();
      const hp = document.getElementById('tx-hp').value.trim();
      const layananId = Number(sel.value);
      const berat = Number(brt.value);
      const lay = db.layanan.find((l) => l.id === layananId);
      if (!nama || !hp || !lay || !(berat > 0)) return;
      const id = db.nextTransaksiId++;
      const kode = 'LDR-' + String(id).padStart(4, '0');
      const total = Math.round(berat * lay.harga_per_kg);
      db.transaksi.push({
        id, kode, nama, hp, layanan_id: lay.id, petugas: currentUser.nama_lengkap,
        berat, harga: lay.harga_per_kg, total, status: 'Proses', masuk: new Date().toISOString(), selesai: null,
      });
      saveState(db);
      setFlash('success', `Transaksi ${kode} untuk ${nama} berhasil dicatat dengan total ${formatRupiah(total)} (status: Proses).`);
      render();
    });

    root.querySelectorAll('[data-mark-selesai]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const tx = db.transaksi.find((t) => t.id === Number(btn.dataset.markSelesai));
        if (tx) {
          tx.status = 'Selesai';
          tx.selesai = new Date().toISOString();
          saveState(db);
          setFlash('success', `Status transaksi ${tx.kode} berhasil diperbarui menjadi Selesai.`);
          render();
        }
      });
    });
  }

  function renderRekapTable(rows) {
    return `
      <div class="table-wrapper" tabindex="0">
        <table class="data-table">
          <thead><tr><th>Kode &amp; pelanggan</th><th>Layanan &amp; tarif</th><th>Berat</th><th>Total biaya</th><th>Status</th><th>Waktu masuk</th><th class="col-actions">Aksi</th></tr></thead>
          <tbody>
            ${rows.map((t) => {
              const l = getLayanan(t.layanan_id);
              return `<tr>
                <th><span class="cell-primary">${esc(t.nama)}</span><span class="cell-sub mono">${esc(t.kode)} | ${esc(t.hp)}</span></th>
                <td data-label="Layanan &amp; tarif"><span class="cell-primary">${esc(l.nama_layanan)}</span><span class="cell-sub mono">${esc(formatRupiah(t.harga))}/kg</span></td>
                <td data-label="Berat" class="col-num">${esc(formatBerat(t.berat))}</td>
                <td data-label="Total biaya" class="col-num">${esc(formatRupiah(t.total))}</td>
                <td data-label="Status"><span class="badge ${t.status === 'Selesai' ? 'badge--selesai' : 'badge--proses'}">${esc(t.status)}</span></td>
                <td data-label="Waktu masuk"><span class="cell-primary mono">${esc(formatTanggal(t.masuk))}</span><span class="cell-sub">Petugas: ${esc(t.petugas)}</span></td>
                <td data-label="Aksi" class="col-actions">
                  <div class="row-actions">
                    ${t.status === 'Proses' ? `<button type="button" class="btn btn--quiet" data-mark-selesai="${t.id}">Selesai</button>` : ''}
                    <button type="button" class="btn btn--quiet btn--icon" data-open-struk="${t.id}" aria-label="Cetak struk ${esc(t.kode)}" title="Cetak struk">
                      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M8 7h8"/><path d="M8 11h8"/><path d="M8 15h5"/></svg>
                    </button>
                    <button type="button" class="btn btn--danger btn--icon" data-del-tx="${t.id}" aria-label="Hapus transaksi ${esc(t.kode)}" title="Hapus pesanan">
                      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                  </div>
                </td>
              </tr>`;
            }).join('')}
          </tbody>
        </table>
      </div>
    `;
  }

  function buildDemoGroups(list, mode, openAll) {
    if (list.length === 0) return [];
    if (mode === 'status') {
      const out = [];
      const proses = list.filter((t) => t.status === 'Proses');
      const selesai = list.filter((t) => t.status === 'Selesai');
      if (proses.length) {
        out.push({ title: 'Sedang diproses', badge: 'proses', items: proses, open: true });
      }
      if (selesai.length) {
        out.push({ title: 'Transaksi selesai', badge: 'selesai', items: selesai, open: openAll || out.length === 0 });
      }
      return out;
    }
    if (mode === 'layanan') {
      const map = new Map();
      list.forEach((t) => {
        const name = getLayanan(t.layanan_id).nama_layanan;
        if (!map.has(name)) map.set(name, []);
        map.get(name).push(t);
      });
      return Array.from(map.entries()).map(([name, items], idx) => ({
        title: `Layanan ${name}`,
        badge: 'neutral',
        items,
        open: openAll || idx === 0,
      }));
    }
    const map = new Map();
    list.forEach((t) => {
      const label = formatTanggal(t.masuk).split(',')[0];
      if (!map.has(label)) map.set(label, []);
      map.get(label).push(t);
    });
    return Array.from(map.entries()).map(([label, items], idx) => ({
      title: label,
      badge: 'neutral',
      items,
      open: openAll || idx === 0,
    }));
  }

  function renderRekap() {
    const q = filters.q.trim().toLowerCase();
    const list = db.transaksi.slice().reverse().filter((t) => {
      if (filters.status !== 'semua' && t.status !== filters.status) return false;
      if (filters.layanan_id && t.layanan_id !== Number(filters.layanan_id)) return false;
      if (q && !(t.nama.toLowerCase().includes(q) || t.kode.toLowerCase().includes(q) || t.hp.toLowerCase().includes(q))) return false;
      return true;
    });

    const hasActiveFilter = Boolean(q || filters.status !== 'semua' || filters.layanan_id);
    const totalPendapatan = list.reduce((s, t) => s + t.total, 0);
    const totalProses = list.filter((t) => t.status === 'Proses').length;
    const totalSelesai = list.length - totalProses;

    const groupOpts = [
      ['status', 'Per status'],
      ['layanan', 'Per layanan'],
      ['tanggal', 'Per tanggal'],
      ['halaman', 'Per halaman (5)'],
    ];

    let contentMarkup = '';
    if (list.length === 0) {
      contentMarkup = `<section class="panel"><div class="empty-state"><p class="empty-state__title">Tidak ada transaksi yang cocok</p><p class="empty-state__desc">Ubah kata kunci pencarian atau klik Reset untuk melihat seluruh transaksi.</p></div></section>`;
    } else if (rekapGroup === 'halaman') {
      const perPage = 5;
      const totalPages = Math.max(1, Math.ceil(list.length / perPage));
      rekapPage = Math.min(Math.max(1, rekapPage), totalPages);
      const start = (rekapPage - 1) * perPage;
      const slice = list.slice(start, start + perPage);

      contentMarkup = `
        <section class="panel">
          <div class="panel__head">
            <div>
              <h2 class="panel__title">Daftar transaksi (halaman ${rekapPage} dari ${totalPages})</h2>
              <p class="panel__meta">Menampilkan ${start + 1}-${start + slice.length} dari ${list.length} transaksi.</p>
            </div>
          </div>
          ${renderRekapTable(slice)}
          ${totalPages > 1 ? `
            <nav class="pager" aria-label="Navigasi halaman rekap">
              <button type="button" class="btn btn--quiet" data-page="${rekapPage - 1}" ${rekapPage <= 1 ? 'disabled' : ''}>Sebelumnya</button>
              <div class="pager__pages">
                ${Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => `<button type="button" class="pager__num" data-page="${p}" ${p === rekapPage ? 'aria-current="page"' : ''}>${p}</button>`).join('')}
              </div>
              <button type="button" class="btn btn--quiet" data-page="${rekapPage + 1}" ${rekapPage >= totalPages ? 'disabled' : ''}>Berikutnya</button>
            </nav>` : ''}
        </section>
      `;
    } else {
      const groups = buildDemoGroups(list, rekapGroup, hasActiveFilter);
      contentMarkup = `
        <div class="rekap-groups" id="demo-rekap-groups">
          ${groups.map((g) => {
            const sub = g.items.reduce((s, t) => s + t.total, 0);
            return `
              <details class="rekap-group" ${g.open ? 'open' : ''}>
                <summary class="rekap-group__summary">
                  <div class="rekap-group__left">
                    <svg class="rekap-group__chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                    <h2 class="rekap-group__title">${esc(g.title)}</h2>
                    <span class="badge badge--${g.badge}"><span class="mono">${g.items.length}</span> <span>transaksi</span></span>
                  </div>
                  <div class="rekap-group__right">
                    <span class="rekap-group__subtotal-label">Subtotal</span>
                    <span class="rekap-group__subtotal">${esc(formatRupiah(sub))}</span>
                  </div>
                </summary>
                <div class="rekap-group__body">${renderRekapTable(g.items)}</div>
              </details>`;
          }).join('')}
        </div>
      `;
    }

    const html = `
      <section class="page-header">
        <div class="page-header__title">
          <h1>Rekap transaksi laundry</h1>
          <p class="page-header__desc">Laporan keuangan dan riwayat order yang dikelompokkan rapi agar mudah dipindai tanpa scroll panjang ke bawah.</p>
        </div>
        <div class="page-header__actions">
          <a class="btn btn--primary" href="#transaksi" data-nav="transaksi">Catat transaksi baru</a>
          <button type="button" class="btn btn--quiet" id="print-rekap-btn">Cetak laporan</button>
        </div>
      </section>

      <section class="stat-strip">
        <div class="stat-item"><span class="stat-item__label">Jumlah transaksi</span><span class="stat-item__value">${list.length}</span><span class="stat-item__note">Data ditampilkan</span></div>
        <div class="stat-item"><span class="stat-item__label">Status Proses</span><span class="stat-item__value">${totalProses}</span><span class="stat-item__note">Dalam pengerjaan</span></div>
        <div class="stat-item"><span class="stat-item__label">Status Selesai</span><span class="stat-item__value">${totalSelesai}</span><span class="stat-item__note">Cucian telah rampung</span></div>
        <div class="stat-item"><span class="stat-item__label">Total pendapatan</span><span class="stat-item__value stat-item__value--accent">${esc(formatRupiah(totalPendapatan))}</span><span class="stat-item__note">Akumulasi tabel rekap</span></div>
      </section>

      <section class="panel panel--well filter-panel">
        <search>
          <form class="filter-form" id="rekap-filter-form">
            <div class="field"><label class="field__label" for="f-q">Cari pelanggan atau kode</label><input class="input" type="search" id="f-q" value="${esc(filters.q)}" placeholder="Nama pelanggan, no HP, atau LDR-0001"></div>
            <div class="field">
              <label class="field__label" for="f-status">Status pengerjaan</label>
              <select class="select" id="f-status">
                <option value="semua" ${filters.status === 'semua' ? 'selected' : ''}>Semua status</option>
                <option value="Proses" ${filters.status === 'Proses' ? 'selected' : ''}>Proses</option>
                <option value="Selesai" ${filters.status === 'Selesai' ? 'selected' : ''}>Selesai</option>
              </select>
            </div>
            <div class="field">
              <label class="field__label" for="f-layanan">Jenis layanan</label>
              <select class="select" id="f-layanan">
                <option value="">Semua layanan</option>
                ${db.layanan.map((l) => `<option value="${l.id}" ${String(filters.layanan_id) === String(l.id) ? 'selected' : ''}>${esc(l.nama_layanan)}</option>`).join('')}
              </select>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn">Terapkan filter</button>
              <button type="button" class="btn btn--quiet" id="f-reset">Reset</button>
            </div>
          </form>
        </search>
      </section>

      <div class="rekap-toolbar">
        <div class="group-tabs" role="group" aria-label="Kelompokkan transaksi">
          ${groupOpts.map(([k, lbl]) => `<button type="button" class="group-tab" data-group-mode="${k}" aria-pressed="${rekapGroup === k ? 'true' : 'false'}">${lbl}</button>`).join('')}
        </div>
        ${rekapGroup !== 'halaman' && list.length > 0 ? `
          <div class="group-controls">
            <button type="button" class="btn btn--quiet" id="demo-expand-all">Buka semua</button>
            <button type="button" class="btn btn--quiet" id="demo-collapse-all">Lipat semua</button>
          </div>` : ''}
      </div>

      ${contentMarkup}
    `;

    renderShell(html);

    document.getElementById('print-rekap-btn')?.addEventListener('click', () => window.print());
    document.getElementById('rekap-filter-form')?.addEventListener('submit', (e) => {
      e.preventDefault();
      filters = {
        q: document.getElementById('f-q').value,
        status: document.getElementById('f-status').value,
        layanan_id: document.getElementById('f-layanan').value,
      };
      rekapPage = 1;
      render();
    });
    document.getElementById('f-reset')?.addEventListener('click', () => {
      filters = { q: '', status: 'semua', layanan_id: '' };
      rekapPage = 1;
      render();
    });

    root.querySelectorAll('[data-group-mode]').forEach((btn) => {
      btn.addEventListener('click', () => {
        rekapGroup = btn.dataset.groupMode;
        rekapPage = 1;
        render();
      });
    });

    root.querySelectorAll('[data-page]').forEach((btn) => {
      btn.addEventListener('click', () => {
        rekapPage = Number(btn.dataset.page);
        render();
      });
    });

    document.getElementById('demo-expand-all')?.addEventListener('click', () => {
      document.querySelectorAll('#demo-rekap-groups details.rekap-group').forEach((d) => { d.open = true; });
    });
    document.getElementById('demo-collapse-all')?.addEventListener('click', () => {
      document.querySelectorAll('#demo-rekap-groups details.rekap-group').forEach((d) => { d.open = false; });
    });

    root.querySelectorAll('[data-mark-selesai]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const tx = db.transaksi.find((t) => t.id === Number(btn.dataset.markSelesai));
        if (tx) {
          tx.status = 'Selesai';
          tx.selesai = new Date().toISOString();
          saveState(db);
          setFlash('success', `Status transaksi ${tx.kode} berhasil diperbarui menjadi Selesai.`);
          render();
        }
      });
    });

    root.querySelectorAll('[data-open-struk]').forEach((btn) => {
      btn.addEventListener('click', () => {
        strukId = Number(btn.dataset.openStruk);
        currentView = 'struk';
        render();
      });
    });

    root.querySelectorAll('[data-del-tx]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const tx = db.transaksi.find((t) => t.id === Number(btn.dataset.delTx));
        if (!tx) return;
        openConfirm(
          'Hapus rekap pesanan?',
          `Anda akan menghapus transaksi <strong>${esc(tx.kode)} (${esc(tx.nama)})</strong> secara permanen dari rekap pesanan.`,
          'Ya, hapus pesanan',
          () => {
            db.transaksi = db.transaksi.filter((t) => t.id !== tx.id);
            saveState(db);
            setFlash('success', `Transaksi ${tx.kode} (${tx.nama}) berhasil dihapus dari rekap pesanan.`);
            render();
          }
        );
      });
    });
  }

  function render() {
    if (!currentUser || currentView === 'login') renderLogin();
    else if (currentView === 'struk') renderStruk();
    else if (currentView === 'layanan') renderLayanan();
    else if (currentView === 'transaksi') renderTransaksi();
    else if (currentView === 'rekap') renderRekap();
    else renderDashboard();
  }

  render();
})();
