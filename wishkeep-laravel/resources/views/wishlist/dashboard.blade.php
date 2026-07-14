<x-layout :title="'Dashboard Wishlist'">

  <div class="page-head">
    <div>
      <div class="eyebrow">Wishlist Saya</div>
      <h1>Halo, {{ explode(' ', auth()->user()->name)[0] }} ✦</h1>
    </div>
    <div class="top-actions">
      <a href="{{ route('folders.index') }}" class="btn btn-outline">🗂️ Kelola Folder</a>
      <a href="{{ route('wishlist.quick-add') }}" class="btn btn-outline">📝 Catatan Belanja</a>
      <a href="{{ route('wishlist.export') }}" class="btn btn-outline">📄 Export CSV</a>
      <a href="{{ route('wishlist.print') }}" target="_blank" class="btn btn-outline">🖨️ Cetak Wishlist</a>
      <a href="{{ route('wishlist.create', array_filter(['folder_id' => $filters['folder_id'] ?? null])) }}" class="btn btn-primary">+ Tambah Item</a>
    </div>
  </div>

  @if($upcomingReminders->isNotEmpty() || $upcomingItemReminders->isNotEmpty())
    <div class="flash flash-reminder">
      @foreach($upcomingReminders as $r)
        @php $daysLeft = $r->daysUntilEvent(); @endphp
        <div class="reminder-row">
          🔔 <b>{{ $daysLeft === 0 ? 'Hari ini' : 'H-' . $daysLeft }}</b> —
          {{ $r->icon ? $r->icon.' ' : '' }}{{ $r->name }} ({{ $r->event_date->locale('id')->translatedFormat('j F Y') }})
          <a href="{{ route('dashboard', ['folder_id' => $r->id]) }}">Lihat wishlist-nya →</a>
        </div>
      @endforeach
      @foreach($upcomingItemReminders as $i)
        @php $daysLeftItem = $i->daysUntilTarget(); @endphp
        <div class="reminder-row">
          🎯 <b>{{ $daysLeftItem === 0 ? 'Hari ini' : 'H-' . $daysLeftItem }}</b> —
          target beli "{{ $i->title }}" ({{ $i->target_date->locale('id')->translatedFormat('j F Y') }})
          <a href="{{ route('wishlist.edit', $i) }}">Lihat item-nya →</a>
        </div>
      @endforeach
    </div>
  @endif

  <div class="stat-grid">
    <div class="stat-card">
      <div class="num">{{ $stats['totalItems'] }}</div>
      <div class="label">Total Item</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ $stats['totalBelum'] }}</div>
      <div class="label">Belum Dibeli</div>
    </div>
    <div class="stat-card">
      <div class="num">{{ $stats['totalDibeli'] }}</div>
      <div class="label">Sudah Dibeli</div>
    </div>
    <div class="stat-card">
      <div class="num">Rp{{ number_format($stats['totalEstimasi'], 0, ',', '.') }}</div>
      <div class="label">Estimasi Belanja</div>
    </div>
  </div>

  <form class="filter-bar" method="GET" action="{{ route('dashboard') }}" id="filter-form">
    <div class="search-wrap">
      <input type="text" name="q" id="search-input" placeholder="Cari judul item..." value="{{ $filters['q'] ?? '' }}" autocomplete="off">
      <div id="search-suggestions" class="search-suggestions"></div>
    </div>

    <select name="category_id">
      <option value="">Semua Kategori</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $c->id)>{{ $c->name }}</option>
      @endforeach
    </select>

    <select name="folder_id">
      <option value="">Semua Folder</option>
      <option value="none" @selected(($filters['folder_id'] ?? '') === 'none')>Tanpa Folder</option>
      @foreach($folders as $f)
        <option value="{{ $f->id }}" @selected((string) ($filters['folder_id'] ?? '') === (string) $f->id)>{{ $f->displayName() }}</option>
      @endforeach
    </select>

    <select name="priority">
      <option value="">Semua Prioritas</option>
      <option value="high" @selected(($filters['priority'] ?? '') === 'high')>Tinggi</option>
      <option value="medium" @selected(($filters['priority'] ?? '') === 'medium')>Sedang</option>
      <option value="low" @selected(($filters['priority'] ?? '') === 'low')>Rendah</option>
    </select>

    <select name="status">
      <option value="">Semua Status</option>
      <option value="belum" @selected(($filters['status'] ?? '') === 'belum')>Belum Dibeli</option>
      <option value="dibeli" @selected(($filters['status'] ?? '') === 'dibeli')>Sudah Dibeli</option>
    </select>

    <button type="submit" class="btn btn-outline btn-sm">Terapkan</button>
    <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm">Reset</a>
  </form>

  @if($items->isEmpty())
    <div class="empty-state">
      <div class="star-big">✦</div>
      <h3>Belum ada item wishlist</h3>
      <p>Yuk mulai catat hal-hal yang kamu inginkan.</p>
      <a href="{{ route('wishlist.create') }}" class="btn btn-primary" style="margin-top:12px;">+ Tambah Item Pertama</a>
    </div>
  @else
    <div class="item-grid">
      @foreach($items as $item)
        <div class="wish-card {{ $item->status === 'dibeli' ? 'is-bought' : '' }}">
          <div class="top-row">
            <div>
              <div class="cat">{{ $item->category->name ?? 'Tanpa Kategori' }}</div>
              <h3>{{ $item->title }}</h3>
            </div>
            <span class="priority-tag priority-{{ $item->priority }}">
              {{ $item->priority === 'high' ? 'Tinggi' : ($item->priority === 'medium' ? 'Sedang' : 'Rendah') }}
            </span>
          </div>

          @if($item->folder)
            <div style="font-size:.72rem; color:var(--tan, var(--ink-dim));">
              📁 {{ $item->folder->icon ? $item->folder->icon.' ' : '' }}{{ $item->folder->name }}
            </div>
          @endif

          @if($item->target_date)
            <div style="font-size:.72rem; color:var(--tan, var(--ink-dim));">
              🎯 Target: {{ $item->target_date->locale('id')->translatedFormat('j F Y') }}
            </div>
          @endif

          @if($item->description)
            <div class="desc">{{ $item->description }}</div>
          @endif

          <div class="price">
            Rp{{ number_format($item->price, 0, ',', '.') }}
            @if(!empty($item->quantity) && trim((string) $item->quantity) !== '1')
              <span class="qty-badge">×{{ $item->quantity }}</span>
            @endif
          </div>

          @if($item->status === 'belum' && (float) $item->price > 0)
            @php $progress = $item->savingProgress(); @endphp
            <div class="saving-block">
              <div class="saving-label">
                <span>Tabungan: <b>Rp{{ number_format($item->saved_amount, 0, ',', '.') }}</b> dari Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                <span>{{ $progress }}%</span>
              </div>
              <div class="progress-track">
                <div class="progress-fill {{ $progress >= 100 ? 'is-complete' : '' }}" style="width:{{ $progress }}%;"></div>
              </div>
              @if($progress < 100)
                <form action="{{ route('wishlist.add-saving', $item) }}" method="POST" class="saving-add-form">
                  @csrf
                  <input type="number" name="amount" min="1" step="1" placeholder="Nabung Rp berapa?" required>
                  <button type="submit" class="btn btn-outline btn-sm">+ Nabung</button>
                </form>
              @endif
            </div>
          @endif

          <div>
            <span class="status-tag status-{{ $item->status }}">
              {{ $item->status === 'dibeli' ? '✓ Sudah Dibeli' : '○ Belum Dibeli' }}
            </span>
            @if($item->link)
              &nbsp; <a href="{{ $item->link }}" target="_blank" style="font-size:.78rem;">Lihat tautan ↗</a>
            @endif
          </div>

          <div class="actions">
            <form action="{{ route('wishlist.toggle', $item) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-outline btn-sm">
                {{ $item->status === 'dibeli' ? 'Tandai Belum' : 'Tandai Dibeli' }}
              </button>
            </form>
            <a href="{{ route('wishlist.edit', $item) }}" class="btn btn-outline btn-sm">Edit</a>
            <form action="{{ route('wishlist.delete', $item) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
            </form>
          </div>

          <form action="{{ route('wishlist.move-folder', $item) }}" method="POST" class="move-folder-form">
            @csrf
            <label for="move-folder-{{ $item->id }}">📁 Pindah:</label>
            <select id="move-folder-{{ $item->id }}" name="folder_id" onchange="this.form.submit()">
              <option value="" @selected(!$item->folder_id)>Tanpa Folder</option>
              @foreach($folders as $f)
                <option value="{{ $f->id }}" @selected($item->folder_id === $f->id)>{{ $f->displayName() }}</option>
              @endforeach
            </select>
          </form>
        </div>
      @endforeach
    </div>
  @endif

  <script>
    (function () {
      const input = document.getElementById('search-input');
      const box = document.getElementById('search-suggestions');
      const form = document.getElementById('filter-form');
      let debounceTimer = null;
      let currentController = null;

      function hideSuggestions() {
        box.innerHTML = '';
        box.style.display = 'none';
      }

      function renderSuggestions(items) {
        if (!items.length) {
          hideSuggestions();
          return;
        }
        box.innerHTML = items.map(item => `
          <div class="suggestion-item" data-title="${item.title.replace(/"/g, '&quot;')}">
            <span class="suggestion-title">${item.title}</span>
            <span class="suggestion-meta">${item.price} · ${item.status === 'dibeli' ? 'Sudah Dibeli' : 'Belum Dibeli'}</span>
          </div>
        `).join('');
        box.style.display = 'block';

        box.querySelectorAll('.suggestion-item').forEach(el => {
          el.addEventListener('click', () => {
            input.value = el.dataset.title;
            hideSuggestions();
            form.submit();
          });
        });
      }

      input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const q = input.value.trim();

        if (q.length < 2) {
          hideSuggestions();
          return;
        }

        debounceTimer = setTimeout(() => {
          if (currentController) currentController.abort();
          currentController = new AbortController();

          fetch(`{{ route('wishlist.suggest') }}?q=${encodeURIComponent(q)}`, {
            signal: currentController.signal,
            headers: { 'Accept': 'application/json' },
          })
            .then(res => res.json())
            .then(renderSuggestions)
            .catch(() => {});
        }, 300);
      });

      // Sembunyikan dropdown kalau klik di luar area search
      document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-wrap')) {
          hideSuggestions();
        }
      });
    })();
  </script>

</x-layout>
