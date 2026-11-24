/**
 * Skrip Interaktif ASAS Laundry (Vanilla JS — Bersih & Mudah Dijelaskan)
 * ----------------------------------------------------------------------
 * 1. Mobile Navigation Drawer berbasis native <dialog> + scroll-snap
 * 2. Modal Konfirmasi Hapus Layanan berbasis native <dialog>
 * 3. Kalkulasi Otomatis Total Tarif Transaksi (berat x harga_per_kg) dalam Rupiah
 * 4. Toggle Lihat/Sembunyikan Password & tombol isi akun demo pada halaman Login
 * 5. Sinkronisasi ARIA untuk validasi :user-invalid setelah interaksi
 */

(function () {
  'use strict';

  /* ==========================================================================
     1. Helper Format Rupiah (sama dengan fungsi formatRupiah di PHP)
     ========================================================================== */
  function formatRupiah(nominal) {
    const nilai = Math.round(Number(nominal) || 0);
    return 'Rp\u00A0' + new Intl.NumberFormat('id-ID').format(nilai);
  }

  /* ==========================================================================
     2. Mobile Navigation Drawer (modern-web-guidance: navigation-drawer)
     ========================================================================== */
  const drawer = document.getElementById('mobile-drawer');
  const openDrawerBtn = document.getElementById('drawer-open');
  const closeDrawerBtn = document.getElementById('drawer-close');

  if (drawer && openDrawerBtn && closeDrawerBtn) {
    const scroller = drawer.querySelector('.Drawer-scroller');
    const sheet = drawer.querySelector('.Drawer-sheet');

    async function openDrawer() {
      if (drawer.open) return;
      drawer.showModal();

      if (!CSS.supports('scroll-initial-target', 'nearest')) {
        scroller.scrollTo({ left: scroller.offsetWidth, behavior: 'instant' });
        await new Promise((resolve) =>
          requestAnimationFrame(() => requestAnimationFrame(resolve))
        );
      }

      scroller.scrollTo({ left: 0, behavior: 'auto' });
    }

    function closeDrawer() {
      if (!drawer.open) return;
      scroller.scrollTo({ left: scroller.offsetWidth, behavior: 'auto' });
    }

    const visibleThreshold = 1 / Math.max(window.innerWidth, 320);
    const observer = new IntersectionObserver(
      (entries) => {
        const entry = entries.at(-1);
        if (!entry) return;
        if (entry.intersectionRatio < visibleThreshold && drawer.open) {
          drawer.close();
          openDrawerBtn.setAttribute('aria-expanded', 'false');
        }
        if (entry.intersectionRatio === 1) {
          openDrawerBtn.setAttribute('aria-expanded', 'true');
        }
      },
      { root: drawer, threshold: [visibleThreshold, 1] }
    );
    observer.observe(sheet);

    if (!CSS.supports('animation-timeline: scroll()')) {
      scroller.addEventListener('scroll', () => {
        const ratio = Math.max(0, Math.min(1, 1 - scroller.scrollLeft / Math.max(sheet.offsetWidth, 1)));
        drawer.style.setProperty('--drawer-backdrop', String(ratio));
      }, { passive: true });
    }

    openDrawerBtn.addEventListener('click', openDrawer);
    closeDrawerBtn.addEventListener('click', closeDrawer);

    drawer.addEventListener('click', (event) => {
      if (!sheet.contains(event.target)) {
        closeDrawer();
      }
    });

    drawer.addEventListener('cancel', (event) => {
      if (event.cancelable) {
        event.preventDefault();
        closeDrawer();
      }
    });
  }

  /* ==========================================================================
     3. Helper Light-Dismiss Fallback untuk Native <dialog>
     ========================================================================== */
  function attachDialogFallback(dialog) {
    dialog.addEventListener('close', () => {
      dialog.returnValue = '';
    });

    // Fallback klik di luar modal untuk browser tanpa dukungan closedby="any"
    if (!('closedBy' in HTMLDialogElement.prototype)) {
      const isOutside = (e, dlg) => {
        const r = dlg.getBoundingClientRect();
        return e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom;
      };
      let pressedOutside = false;
      dialog.addEventListener('pointerdown', (e) => {
        pressedOutside = isOutside(e, dialog);
      });
      dialog.addEventListener('pointerup', (e) => {
        if (pressedOutside && isOutside(e, dialog)) {
          dialog.close();
        }
      });
    }
  }

  /* ==========================================================================
     3a. Modal Konfirmasi Hapus Layanan (layanan/index.blade.php)
     ========================================================================== */
  const deleteDialog = document.getElementById('confirm-delete-dialog');
  if (deleteDialog) {
    const deleteNameEl = document.getElementById('delete-service-name');
    const deleteForm = document.getElementById('delete-service-form');
    const deleteTriggers = document.querySelectorAll('[data-delete-service-action]');

    deleteTriggers.forEach((button) => {
      button.addEventListener('click', () => {
        if (deleteNameEl) {
          deleteNameEl.textContent = button.dataset.deleteServiceName || 'layanan ini';
        }
        if (deleteForm && button.dataset.deleteServiceAction) {
          deleteForm.action = button.dataset.deleteServiceAction;
        }
        deleteDialog.showModal();
      });
    });

    attachDialogFallback(deleteDialog);
  }

  /* ==========================================================================
     3b. Modal Konfirmasi Hapus Rekap Transaksi (rekap/index.blade.php)
     ========================================================================== */
  const deleteTxDialog = document.getElementById('confirm-delete-tx-dialog');
  if (deleteTxDialog) {
    const deleteTxLabelEl = document.getElementById('delete-tx-label');
    const deleteTxForm = document.getElementById('delete-tx-form');
    const deleteTxTriggers = document.querySelectorAll('[data-delete-tx-action]');

    deleteTxTriggers.forEach((button) => {
      button.addEventListener('click', () => {
        if (deleteTxLabelEl) {
          deleteTxLabelEl.textContent = button.dataset.deleteTxLabel || 'pesanan ini';
        }
        if (deleteTxForm && button.dataset.deleteTxAction) {
          deleteTxForm.action = button.dataset.deleteTxAction;
        }
        deleteTxDialog.showModal();
      });
    });

    attachDialogFallback(deleteTxDialog);
  }

  /* ==========================================================================
     4. Kalkulator Tarif Otomatis pada Halaman Transaksi (berat x harga_per_kg)
     ========================================================================== */
  const serviceSelect = document.getElementById('layanan_id');
  const weightInput = document.getElementById('berat_kg');
  const calcRateEl = document.getElementById('calc-rate');
  const calcEstEl = document.getElementById('calc-estimasi');
  const calcTotalEl = document.getElementById('calc-total');

  function updateTransactionCalculation() {
    if (!serviceSelect || !weightInput || !calcTotalEl) return;

    const selected = serviceSelect.options[serviceSelect.selectedIndex];
    const harga = Number(selected?.dataset.harga || 0);
    const estimasi = selected?.dataset.estimasi || '-';
    const berat = Number(weightInput.value || 0);

    if (calcRateEl) {
      calcRateEl.textContent = harga > 0 ? formatRupiah(harga) + ' / kg' : '-';
    }
    if (calcEstEl) {
      calcEstEl.textContent = estimasi;
    }

    const total = harga > 0 && berat > 0 ? Math.round(harga * berat) : 0;
    calcTotalEl.textContent = formatRupiah(total);
  }

  if (serviceSelect && weightInput) {
    serviceSelect.addEventListener('change', updateTransactionCalculation);
    weightInput.addEventListener('input', updateTransactionCalculation);
    updateTransactionCalculation();
  }

  /* ==========================================================================
     5. Toggle Tampilkan/Sembunyikan Kata Sandi & Isi Akun Demo (Login)
     ========================================================================== */
  const togglePwdBtn = document.getElementById('toggle-password');
  const pwdInput = document.getElementById('password');

  if (togglePwdBtn && pwdInput) {
    togglePwdBtn.addEventListener('click', () => {
      const showing = pwdInput.type === 'text';
      pwdInput.type = showing ? 'password' : 'text';
      togglePwdBtn.setAttribute('aria-pressed', String(!showing));
      togglePwdBtn.textContent = showing ? 'Tampilkan' : 'Sembunyikan';
      togglePwdBtn.setAttribute(
        'aria-label',
        showing ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi'
      );
    });
  }

  document.querySelectorAll('[data-demo-user]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const userField = document.getElementById('username');
      const passField = document.getElementById('password');
      if (userField && passField) {
        userField.value = btn.dataset.demoUser || '';
        passField.value = btn.dataset.demoPass || '';
        userField.dispatchEvent(new Event('input', { bubbles: true }));
        passField.dispatchEvent(new Event('input', { bubbles: true }));
        userField.focus();
      }
    });
  });

  /* ==========================================================================
     6. Tombol Cetak Laporan (Rekap) & Cetak Struk (struk.php)
     ========================================================================== */
  const printBtn = document.getElementById('print-report-btn');
  if (printBtn) {
    printBtn.addEventListener('click', () => window.print());
  }

  const printReceiptBtn = document.getElementById('print-receipt-btn');
  if (printReceiptBtn) {
    printReceiptBtn.addEventListener('click', () => window.print());
  }

  /* ==========================================================================
     6b. Kontrol Buka Semua / Lipat Semua Grup pada Rekap Transaksi
     ========================================================================== */
  const groupContainer = document.querySelector('[data-rekap-groups]');
  const expandAllBtn = document.getElementById('expand-all-groups');
  const collapseAllBtn = document.getElementById('collapse-all-groups');

  if (groupContainer) {
    expandAllBtn?.addEventListener('click', () => {
      groupContainer.querySelectorAll('details.rekap-group').forEach((d) => {
        d.open = true;
      });
    });
    collapseAllBtn?.addEventListener('click', () => {
      groupContainer.querySelectorAll('details.rekap-group').forEach((d) => {
        d.open = false;
      });
    });
  }

  /* ==========================================================================
     7. Sinkronisasi ARIA & Validasi :user-invalid (modern-web-guidance)
     ========================================================================== */
  const supportsUserInvalid = CSS.supports('selector(:user-invalid)');
  const dirtyState = new WeakMap();
  const flagged = new WeakSet();
  const announced = new WeakSet();

  const announcer = document.createElement('div');
  announcer.setAttribute('aria-live', 'polite');
  announcer.classList.add('visually-hidden');
  document.body.append(announcer);

  let announceTimer;
  let announcedField;

  function linkError(el, shown) {
    const errorId = el.dataset.error;
    if (!errorId) return;
    const ids = (el.getAttribute('aria-describedby') || '')
      .split(/\s+/)
      .filter((id) => id && id !== errorId);
    if (shown) ids.push(errorId);
    if (ids.length) {
      el.setAttribute('aria-describedby', ids.join(' '));
    } else {
      el.removeAttribute('aria-describedby');
    }
  }

  function announceError(el) {
    const errorEl = document.getElementById(el.dataset.error || '');
    if (!errorEl) return;
    const label = el.labels?.[0] ? el.labels[0].textContent.trim() + ': ' : '';
    clearTimeout(announceTimer);
    announcer.textContent = '';
    announcedField = el;
    announceTimer = setTimeout(() => {
      announcer.textContent = label + errorEl.textContent.trim();
    }, 500);
  }

  function syncAria(el, shouldAnnounce = false) {
    if (!el.matches?.('input, select, textarea')) return;

    let isInvalid = false;
    if (supportsUserInvalid) {
      isInvalid = el.matches(':user-invalid');
    } else {
      const st = dirtyState.get(el);
      isInvalid = Boolean(st && st.hasBlurred && st.hasInteracted && !el.validity.valid);
      el.classList.toggle('user-invalid-fallback', isInvalid);
    }

    if (isInvalid) {
      if (shouldAnnounce && !announced.has(el)) {
        announceError(el);
        announced.add(el);
      }
      el.setAttribute('aria-invalid', 'true');
      linkError(el, true);
      flagged.add(el);
    } else if (flagged.has(el) || el.getAttribute('aria-invalid') !== 'true') {
      el.setAttribute('aria-invalid', 'false');
      linkError(el, false);
      flagged.delete(el);
      announced.delete(el);
      if (announcedField === el) {
        clearTimeout(announceTimer);
        announcer.textContent = '';
      }
    }
  }

  document.querySelectorAll('input, select, textarea').forEach((el) => {
    if (el.type === 'hidden') return;
    syncAria(el);
  });

  document.addEventListener('blur', (e) => {
    const el = e.target;
    if (!supportsUserInvalid && el.matches?.('input, select, textarea')) {
      const st = dirtyState.get(el) || { hasInteracted: false, hasBlurred: false };
      st.hasBlurred = true;
      dirtyState.set(el, st);
    }
    syncAria(el, true);
  }, true);

  document.addEventListener('input', (e) => {
    const el = e.target;
    if (!supportsUserInvalid && el.matches?.('input, select, textarea')) {
      const st = dirtyState.get(el) || { hasInteracted: false, hasBlurred: false };
      st.hasInteracted = true;
      dirtyState.set(el, st);
    }
    if (el.hasAttribute?.('aria-invalid')) {
      syncAria(el);
    }
  });

  document.addEventListener('change', (e) => {
    const el = e.target;
    if (!supportsUserInvalid && el.matches?.('input, select, textarea')) {
      const st = dirtyState.get(el) || { hasInteracted: false, hasBlurred: false };
      st.hasInteracted = true;
      st.hasBlurred = true;
      dirtyState.set(el, st);
    }
    syncAria(el, true);
  }, true);

  document.addEventListener('invalid', (e) => {
    const el = e.target;
    if (!supportsUserInvalid) {
      dirtyState.set(el, { hasInteracted: true, hasBlurred: true });
    }
    announced.add(el);
    syncAria(el);
  }, true);

  /* ==========================================================================
     8. Animasi Teks Ink Bleed (diadaptasi dari InkBleedComponent ke Vanilla JS)
     --------------------------------------------------------------------------
     Setiap kata meresap seperti tinta di atas kertas serat (feTurbulence +
     feDisplacementMap + feGaussianBlur + feComponentTransfer), lalu mengering
     menjadi huruf yang tajam. Hover pada kata membuatnya kembali sedikit basah.
     ========================================================================== */
  function initInkBleedText() {
    const roots = document.querySelectorAll('[data-ink-bleed]');
    if (!roots.length) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const SVG_NS = 'http://www.w3.org/2000/svg';
    const clamp = (v, min = 0, max = 1) => Math.min(max, Math.max(min, v));
    const easeSoak = (x) => 1 - Math.pow(1 - x, 3);

    const config = {
      spread: 12,
      roughness: 10,
      grain: 1.0,
      halo: 0.5,
      duration: 1.6,
      delay: 0.18,
      stagger: 0.14,
      hoverStrength: 0.55,
    };

    const createSvgEl = (tag, attrs = {}) => {
      const el = document.createElementNS(SVG_NS, tag);
      for (const [k, v] of Object.entries(attrs)) {
        el.setAttribute(k, String(v));
      }
      return el;
    };

    roots.forEach((root, rootIndex) => {
      const wordEls = Array.from(root.querySelectorAll('[data-ink-word]'));
      if (!wordEls.length) return;

      const svg = createSvgEl('svg', {
        class: 'ink-bleed__defs',
        width: '0',
        height: '0',
        'aria-hidden': 'true',
      });
      const defs = createSvgEl('defs');
      svg.append(defs);
      root.prepend(svg);

      const units = wordEls.map((el, i) => {
        const fid = `ink-bleed-${rootIndex}-${i}`;
        const filter = createSvgEl('filter', {
          id: fid,
          x: '-40%',
          y: '-60%',
          width: '180%',
          height: '220%',
          'color-interpolation-filters': 'sRGB',
        });

        const turb = createSvgEl('feTurbulence', {
          type: 'fractalNoise',
          baseFrequency: '0.08',
          numOctaves: '2',
          seed: String(i * 7 + 3),
          result: 'noise',
        });
        const disp = createSvgEl('feDisplacementMap', {
          in: 'SourceGraphic',
          in2: 'noise',
          scale: '0',
          xChannelSelector: 'R',
          yChannelSelector: 'G',
          result: 'd',
        });
        const blur = createSvgEl('feGaussianBlur', {
          in: 'd',
          stdDeviation: '0',
          result: 'b',
        });

        const haloTransfer = createSvgEl('feComponentTransfer', { in: 'b', result: 'halo' });
        const haloFunc = createSvgEl('feFuncA', { type: 'linear', slope: '0', intercept: '0' });
        haloTransfer.append(haloFunc);

        const inkTransfer = createSvgEl('feComponentTransfer', { in: 'b', result: 'ink' });
        const inkFunc = createSvgEl('feFuncA', { type: 'linear', slope: '1', intercept: '0' });
        inkTransfer.append(inkFunc);

        const merge = createSvgEl('feMerge');
        merge.append(createSvgEl('feMergeNode', { in: 'halo' }));
        merge.append(createSvgEl('feMergeNode', { in: 'ink' }));

        filter.append(turb, disp, blur, haloTransfer, inkTransfer, merge);
        defs.append(filter);

        el.setAttribute('aria-hidden', 'true');
        el.style.opacity = '0';
        el.style.filter = `url(#${fid})`;

        return {
          el,
          fid,
          done: false,
          turb,
          disp,
          blur,
          ink: inkFunc,
          halo: haloFunc,
        };
      });

      const hovers = new Map();
      const startTime = performance.now();
      let rafId = 0;

      const tick = (now) => {
        const fontSize = parseFloat(getComputedStyle(root).fontSize) || 34;
        const elapsed = (now - startTime) / 1000;
        let hasActiveWork = false;

        units.forEach((u, idx) => {
          const local = elapsed - config.delay - idx * config.stagger;
          let progress = easeSoak(clamp(local / config.duration));

          const hoverStart = hovers.get(idx);
          if (hoverStart !== undefined) {
            const h = (now - hoverStart) / 1100;
            if (h >= 1) {
              hovers.delete(idx);
            } else {
              const wet =
                config.hoverStrength *
                (h < 0.18 ? easeSoak(h / 0.18) : 1 - easeSoak((h - 0.18) / 0.82));
              progress = Math.min(progress, 1 - wet);
            }
          }

          if (progress >= 1 && local > 0) {
            if (!u.done) {
              u.done = true;
              u.el.style.filter = 'none';
              u.el.style.opacity = '1';
            }
            return;
          }

          hasActiveWork = true;
          if (u.done) {
            u.done = false;
            u.el.style.filter = `url(#${u.fid})`;
          }

          const q = 1 - progress;
          u.el.style.opacity = String(clamp(local / Math.max(0.05, config.duration * 0.12)));
          u.turb.setAttribute('baseFrequency', ((9 * config.grain) / fontSize).toFixed(4));
          u.disp.setAttribute('scale', ((q * config.roughness * fontSize) / 60).toFixed(2));
          u.blur.setAttribute('stdDeviation', ((q * config.spread * fontSize) / 100).toFixed(2));
          u.ink.setAttribute('slope', (1 + q * 16).toFixed(3));
          u.ink.setAttribute('intercept', (-q * 6).toFixed(3));
          u.halo.setAttribute('slope', (q * config.halo * 1.1).toFixed(3));
        });

        if (hasActiveWork || hovers.size > 0) {
          rafId = requestAnimationFrame(tick);
        } else {
          rafId = 0;
        }
      };

      const ensureLoop = () => {
        if (!rafId) {
          rafId = requestAnimationFrame(tick);
        }
      };

      units.forEach((u, idx) => {
        u.el.addEventListener('pointerenter', () => {
          if (u.done && !hovers.has(idx)) {
            hovers.set(idx, performance.now());
            ensureLoop();
          }
        });
      });

      ensureLoop();
    });
  }

  initInkBleedText();

  /* ==========================================================================
     9. Animasi Count-Up Statistik Dashboard (StatsSection — easeOutExpo)
     ========================================================================== */
  function initStatsShowcase() {
    const container = document.querySelector('[data-stats-showcase]');
    if (!container) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const counters = Array.from(container.querySelectorAll('[data-count-to]'));
    if (!counters.length) return;

    const easeOutExpo = (t) => (t >= 1 ? 1 : 1 - Math.pow(2, -10 * t));
    const durationMs = 1350;

    const runAnimation = () => {
      const start = performance.now();
      const tick = (now) => {
        const progress = Math.min((now - start) / durationMs, 1);
        const eased = easeOutExpo(progress);

        counters.forEach((el) => {
          const target = Number(el.dataset.countTo || 0);
          const fmt = el.dataset.countFormat || 'int';
          const current = Math.round(eased * target);
          el.textContent = fmt === 'rupiah' ? formatRupiah(current) : String(current);
        });

        if (progress < 1) {
          requestAnimationFrame(tick);
        }
      };
      requestAnimationFrame(tick);
    };

    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver(
        (entries) => {
          if (entries.some((e) => e.isIntersecting)) {
            io.disconnect();
            runAnimation();
          }
        },
        { threshold: 0.2 }
      );
      io.observe(container);
    } else {
      runAnimation();
    }
  }

  /* ==========================================================================
     10. Grafik Donut Bergradasi Interaktif (InteractiveInsightDonut)
     ========================================================================== */
  function initInsightDonut() {
    const wrap = document.querySelector('[data-insight-donut]');
    if (!wrap) return;

    let rawItems = [];
    try {
      rawItems = JSON.parse(wrap.dataset.donutItems || '[]');
    } catch (_) {
      return;
    }

    const items = rawItems.filter((item) => Number(item.value) > 0);
    const total = items.reduce((sum, item) => sum + Number(item.value), 0);
    const totalOrders = items.reduce((sum, item) => sum + Number(item.orders || 0), 0);
    if (!items.length || total <= 0) return;

    const arcsGroup = wrap.querySelector('[data-donut-arcs]');
    const labelEl = wrap.querySelector('[data-donut-label]');
    const valueEl = wrap.querySelector('[data-donut-value]');
    const shareEl = wrap.querySelector('[data-donut-share]');
    const legendBtns = Array.from(wrap.querySelectorAll('[data-donut-index]'));
    if (!arcsGroup) return;

    const SVG_NS = 'http://www.w3.org/2000/svg';
    const cx = 130;
    const cy = 130;
    const radius = 102;
    const gapDeg = items.length > 1 ? 14 : 0;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let angleCursor = -90;
    const arcEls = [];

    items.forEach((seg, idx) => {
      const share = Number(seg.value) / total;
      const sweep = Math.max(4, share * 360 - gapDeg);
      const startDeg = angleCursor + gapDeg / 2;
      const endDeg = startDeg + sweep;
      angleCursor += share * 360;

      const startRad = (startDeg * Math.PI) / 180;
      const endRad = (endDeg * Math.PI) / 180;
      const x1 = cx + radius * Math.cos(startRad);
      const y1 = cy + radius * Math.sin(startRad);
      const x2 = cx + radius * Math.cos(endRad);
      const y2 = cy + radius * Math.sin(endRad);
      const largeArc = sweep > 180 ? 1 : 0;

      const path = document.createElementNS(SVG_NS, 'path');
      path.setAttribute(
        'd',
        `M ${x1.toFixed(2)} ${y1.toFixed(2)} A ${radius} ${radius} 0 ${largeArc} 1 ${x2.toFixed(2)} ${y2.toFixed(2)}`
      );
      path.setAttribute('class', 'donut-arc');
      path.setAttribute('stroke', `url(#dash-grad-${idx % 4})`);
      path.setAttribute('tabindex', '0');
      path.setAttribute('role', 'button');
      path.setAttribute(
        'aria-label',
        `${seg.name}: ${formatRupiah(seg.value)} (${(share * 100).toFixed(1)} persen)`
      );

      arcsGroup.append(path);
      arcEls.push(path);

      if (!reduced && typeof path.getTotalLength === 'function') {
        const len = path.getTotalLength();
        path.style.strokeDasharray = `${len}`;
        path.style.strokeDashoffset = `${len}`;
        requestAnimationFrame(() => {
          setTimeout(() => {
            path.style.transition =
              'stroke-dashoffset 820ms cubic-bezier(0.16, 1, 0.3, 1), opacity 220ms cubic-bezier(0.16, 1, 0.3, 1), stroke-width 220ms cubic-bezier(0.16, 1, 0.3, 1)';
            path.style.strokeDashoffset = '0';
          }, 90 + idx * 120);
        });
      }
    });

    const setHoverState = (activeIdx) => {
      const validIdx = activeIdx !== null && items[activeIdx] ? activeIdx : null;
      arcEls.forEach((el, i) => {
        el.classList.toggle('is-active', validIdx === i);
        el.classList.toggle('is-dimmed', validIdx !== null && validIdx !== i);
      });
      legendBtns.forEach((btn) => {
        btn.classList.toggle('is-active', validIdx !== null && Number(btn.dataset.donutIndex) === validIdx);
      });

      if (validIdx !== null) {
        const seg = items[validIdx];
        const pct = ((Number(seg.value) / total) * 100).toFixed(1).replace('.', ',');
        if (labelEl) labelEl.textContent = seg.name;
        if (valueEl) valueEl.textContent = formatRupiah(seg.value);
        if (shareEl) shareEl.textContent = `${pct}% (${seg.orders} order)`;
      } else {
        if (labelEl) labelEl.textContent = 'Total pendapatan';
        if (valueEl) valueEl.textContent = formatRupiah(total);
        if (shareEl) shareEl.textContent = `${totalOrders} order`;
      }
    };

    arcEls.forEach((el, idx) => {
      el.addEventListener('pointerenter', () => setHoverState(idx));
      el.addEventListener('pointerleave', () => setHoverState(null));
      el.addEventListener('focus', () => setHoverState(idx));
      el.addEventListener('blur', () => setHoverState(null));
    });

    legendBtns.forEach((btn) => {
      const idx = Number(btn.dataset.donutIndex);
      btn.addEventListener('pointerenter', () => setHoverState(idx));
      btn.addEventListener('pointerleave', () => setHoverState(null));
      btn.addEventListener('focus', () => setHoverState(idx));
      btn.addEventListener('blur', () => setHoverState(null));
    });
  }

  initStatsShowcase();
  initInsightDonut();
})();
