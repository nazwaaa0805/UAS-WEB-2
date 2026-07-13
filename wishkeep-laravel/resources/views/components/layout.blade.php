<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'WishKeep' }} · WishKeep</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="{{ auth()->check() ? 'bg-app' : (request()->routeIs('login') || request()->routeIs('register') ? 'bg-guest' : '') }}">

  @auth
    <button type="button" id="sidebarToggle" class="sidebar-toggle" aria-label="Buka/tutup menu">
      <span class="sidebar-toggle-icon">☰</span>
    </button>

    <aside class="sidebar" id="sidebar">
      <a href="/" class="brand"><span class="star">✦</span> WishKeep</a>

      <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">🏠 Wishlist Saya</a>
        <a href="{{ route('folders.index') }}" class="{{ request()->routeIs('folders.*') ? 'is-active' : '' }}">🗂️ Folder</a>
        <a href="{{ route('stats.index') }}" class="{{ request()->routeIs('stats.*') ? 'is-active' : '' }}">📊 Statistik</a>
        @if(auth()->user()->role === 'admin')
          <div class="sidebar-divider"></div>
          <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'is-active' : '' }}">👤 Pengguna</a>
          <a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories') ? 'is-active' : '' }}">🏷️ Kategori</a>
          <a href="{{ route('admin.activity-log') }}" class="{{ request()->routeIs('admin.activity-log') ? 'is-active' : '' }}">📜 Log Aktivitas</a>
        @endif
        <div class="sidebar-divider"></div>
        <a href="{{ route('wishlist.print') }}" target="_blank">🖨️ Cetak</a>
      </nav>

      <div class="sidebar-footer">
        <span class="user-pill">{{ auth()->user()->name }}<span class="badge-role">{{ auth()->user()->role }}</span></span>
        <a href="{{ route('logout') }}"
           class="sidebar-logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">↪ Keluar</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
          @csrf
        </form>
      </div>
    </aside>

    <!-- ===== FLOATING CALCULATOR (PiP) ===== -->
    <button type="button" id="calcToggle" class="calc-toggle-btn" title="Buka Kalkulator" aria-label="Buka kalkulator">🧮</button>
    <div id="floatingCalc" class="floating-calc">
      <div class="calc-header" id="calcHeader">
        <span>🧮 Kalkulator</span>
        <div class="calc-header-actions">
          <button type="button" id="calcMinimize" title="Minimize" aria-label="Minimize kalkulator">–</button>
          <button type="button" id="calcClose" title="Tutup" aria-label="Tutup kalkulator">×</button>
        </div>
      </div>
      <div class="calc-body" id="calcBody">
        <input type="text" id="calcScreen" class="calc-screen" readonly value="0" inputmode="none">
        <div class="calc-grid">
          <button type="button" class="calc-btn calc-op" data-action="clear">C</button>
          <button type="button" class="calc-btn calc-op" data-action="backspace">⌫</button>
          <button type="button" class="calc-btn calc-op" data-value="%">%</button>
          <button type="button" class="calc-btn calc-op" data-value="/">÷</button>

          <button type="button" class="calc-btn" data-value="7">7</button>
          <button type="button" class="calc-btn" data-value="8">8</button>
          <button type="button" class="calc-btn" data-value="9">9</button>
          <button type="button" class="calc-btn calc-op" data-value="*">×</button>

          <button type="button" class="calc-btn" data-value="4">4</button>
          <button type="button" class="calc-btn" data-value="5">5</button>
          <button type="button" class="calc-btn" data-value="6">6</button>
          <button type="button" class="calc-btn calc-op" data-value="-">−</button>

          <button type="button" class="calc-btn" data-value="1">1</button>
          <button type="button" class="calc-btn" data-value="2">2</button>
          <button type="button" class="calc-btn" data-value="3">3</button>
          <button type="button" class="calc-btn calc-op" data-value="+">+</button>

          <button type="button" class="calc-btn calc-zero" data-value="0">0</button>
          <button type="button" class="calc-btn" data-value=".">.</button>
          <button type="button" class="calc-btn calc-equals" data-action="equals">=</button>
        </div>
      </div>
    </div>
  @else
    <header class="guest-topbar">
      <a href="/" class="brand"><span class="star">✦</span> WishKeep</a>
      <nav class="guest-nav">
        <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'is-active' : '' }}">Masuk</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
      </nav>
    </header>
  @endauth

  <div class="main-content" id="mainContent">
    <div class="container">
      @if (session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
      @endif
      @if ($errors->any())
        <div class="flash flash-error">{{ $errors->first() }}</div>
      @endif

      {{ $slot }}

    </div>
    <footer class="site-footer">WishKeep &middot; Aplikasi Manajemen Wishlist &middot; UAS Pemrograman Web 2 (Laravel)</footer>
  </div>

  <script>
    (function () {
      const toggle = document.getElementById('sidebarToggle');
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('mainContent');
      const STORAGE_KEY = 'wishkeep_sidebar_collapsed';

      if (!toggle || !sidebar || !main) return; // halaman tamu (login/daftar) tidak punya sidebar

      function applyState(collapsed) {
        sidebar.classList.toggle('collapsed', collapsed);
        main.classList.toggle('sidebar-collapsed', collapsed);
        document.body.classList.toggle('sidebar-collapsed', collapsed);
      }

      let collapsed = localStorage.getItem(STORAGE_KEY) === '1';
      if (window.innerWidth <= 900) collapsed = true; // default tertutup di layar kecil
      applyState(collapsed);

      toggle.addEventListener('click', function () {
        collapsed = !sidebar.classList.contains('collapsed');
        applyState(collapsed);
        localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
      });

      // Klik di luar sidebar (khusus layar kecil) akan menutup sidebar
      document.addEventListener('click', function (e) {
        if (window.innerWidth > 900) return;
        if (sidebar.classList.contains('collapsed')) return;
        if (sidebar.contains(e.target) || toggle.contains(e.target)) return;
        applyState(true);
      });
    })();
  </script>

  <script>
    // ===== FLOATING CALCULATOR (PiP) =====
    (function () {
      const toggleBtn = document.getElementById('calcToggle');
      const panel = document.getElementById('floatingCalc');
      if (!toggleBtn || !panel) return; // tidak ada di halaman tamu

      const header = document.getElementById('calcHeader');
      const closeBtn = document.getElementById('calcClose');
      const minBtn = document.getElementById('calcMinimize');
      const screen = document.getElementById('calcScreen');
      const POS_KEY = 'wishkeep_calc_pos';
      const OPEN_KEY = 'wishkeep_calc_open';
      const MIN_KEY = 'wishkeep_calc_minimized';

      let current = '0';
      let previous = null;
      let operator = null;
      let justEvaluated = false;

      function updateScreen() { screen.value = current; }

      function inputDigit(d) {
        if (justEvaluated) { current = '0'; justEvaluated = false; }
        if (d === '.' && current.includes('.')) return;
        current = (current === '0' && d !== '.') ? d : current + d;
        updateScreen();
      }
      function clearAll() {
        current = '0'; previous = null; operator = null; justEvaluated = false;
        updateScreen();
      }
      function backspace() {
        current = current.length > 1 ? current.slice(0, -1) : '0';
        updateScreen();
      }
      function chooseOperator(op) {
        if (operator && !justEvaluated) compute();
        previous = parseFloat(current);
        operator = op;
        justEvaluated = false;
        current = '0';
      }
      function compute() {
        if (operator === null || previous === null) return;
        const cur = parseFloat(current);
        let result = previous;
        switch (operator) {
          case '+': result = previous + cur; break;
          case '-': result = previous - cur; break;
          case '*': result = previous * cur; break;
          case '/': result = cur === 0 ? 0 : previous / cur; break;
          case '%': result = cur === 0 ? 0 : previous % cur; break;
        }
        current = String(Math.round(result * 1e8) / 1e8);
        operator = null;
        previous = null;
        justEvaluated = true;
        updateScreen();
      }

      panel.querySelectorAll('.calc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          const action = btn.dataset.action;
          const value = btn.dataset.value;
          if (action === 'clear') return clearAll();
          if (action === 'backspace') return backspace();
          if (action === 'equals') return compute();
          if (value === '+' || value === '-' || value === '*' || value === '/' || value === '%') {
            return chooseOperator(value);
          }
          inputDigit(value);
        });
      });

      function openCalc() {
        panel.classList.add('is-open');
        toggleBtn.style.display = 'none';
        localStorage.setItem(OPEN_KEY, '1');
      }
      function closeCalc() {
        panel.classList.remove('is-open');
        toggleBtn.style.display = 'flex';
        localStorage.setItem(OPEN_KEY, '0');
      }
      toggleBtn.addEventListener('click', openCalc);
      closeBtn.addEventListener('click', closeCalc);
      minBtn.addEventListener('click', function () {
        const minimized = panel.classList.toggle('minimized');
        localStorage.setItem(MIN_KEY, minimized ? '1' : '0');
      });
      // Klik header saat minimized akan membuka lagi
      header.addEventListener('dblclick', function () {
        panel.classList.remove('minimized');
        localStorage.setItem(MIN_KEY, '0');
      });

      // ===== DRAG (mouse + touch) =====
      let dragging = false, offsetX = 0, offsetY = 0;

      function startDrag(clientX, clientY) {
        dragging = true;
        const rect = panel.getBoundingClientRect();
        offsetX = clientX - rect.left;
        offsetY = clientY - rect.top;
        panel.style.right = 'auto';
        panel.style.bottom = 'auto';
        panel.style.left = rect.left + 'px';
        panel.style.top = rect.top + 'px';
      }
      function moveDrag(clientX, clientY) {
        if (!dragging) return;
        const maxLeft = window.innerWidth - panel.offsetWidth - 8;
        const maxTop = window.innerHeight - panel.offsetHeight - 8;
        const left = Math.min(Math.max(8, clientX - offsetX), Math.max(8, maxLeft));
        const top = Math.min(Math.max(8, clientY - offsetY), Math.max(8, maxTop));
        panel.style.left = left + 'px';
        panel.style.top = top + 'px';
      }
      function endDrag() {
        if (!dragging) return;
        dragging = false;
        localStorage.setItem(POS_KEY, JSON.stringify({ left: panel.style.left, top: panel.style.top }));
      }

      header.addEventListener('mousedown', function (e) {
        startDrag(e.clientX, e.clientY);
        e.preventDefault();
      });
      document.addEventListener('mousemove', function (e) { moveDrag(e.clientX, e.clientY); });
      document.addEventListener('mouseup', endDrag);

      header.addEventListener('touchstart', function (e) {
        const t = e.touches[0];
        startDrag(t.clientX, t.clientY);
      }, { passive: true });
      document.addEventListener('touchmove', function (e) {
        if (dragging) { const t = e.touches[0]; moveDrag(t.clientX, t.clientY); }
      }, { passive: true });
      document.addEventListener('touchend', endDrag);

      // ===== Pulihkan posisi & state terakhir =====
      try {
        const saved = JSON.parse(localStorage.getItem(POS_KEY) || 'null');
        if (saved && saved.left && saved.top) {
          panel.style.left = saved.left;
          panel.style.top = saved.top;
          panel.style.right = 'auto';
          panel.style.bottom = 'auto';
        }
      } catch (e) {}

      if (localStorage.getItem(MIN_KEY) === '1') panel.classList.add('minimized');
      if (localStorage.getItem(OPEN_KEY) === '1') openCalc();
    })();
  </script>
</body>
</html>
