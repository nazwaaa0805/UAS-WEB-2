<x-layout :title="'Folder Wishlist'">

  <div class="page-head">
    <div>
      <div class="eyebrow">Kelompokkan Wishlist-mu</div>
      <h1>Folder Wishlist</h1>
    </div>
    <div class="top-actions">
      <a href="{{ route('folders.create') }}" class="btn btn-primary">+ Buat Folder</a>
    </div>
  </div>

  @if($folders->isEmpty())
    <div class="empty-state">
      <div class="star-big">🗂️</div>
      <h3>Belum ada folder</h3>
      <p>Buat folder untuk mengelompokkan wishlist berdasarkan momen — misalnya Ulang Tahun, Lebaran, atau Kado Nikah.</p>
      <a href="{{ route('folders.create') }}" class="btn btn-primary" style="margin-top:12px;">+ Buat Folder Pertama</a>
    </div>
  @else
    <div class="item-grid">
      @foreach($folders as $folder)
        @php $daysLeft = $folder->daysUntilEvent(); @endphp
        <div class="wish-card">
          @if($folder->photo_path)
            <img src="{{ $folder->photoUrl() }}" alt="Tema folder {{ $folder->name }}" class="card-photo">
          @endif
          <div class="top-row">
            <div>
              <div class="cat">{{ $folder->items_count }} item</div>
              <h3>
                {{ $folder->icon ? $folder->icon.' ' : '' }}{{ $folder->name }}
                @if($folder->isCatatanBelanja())
                  <span class="folder-type-badge" title="Folder Catatan Belanja">C</span>
                @endif
              </h3>
            </div>
            @if($daysLeft !== null)
              <span class="priority-tag priority-{{ $daysLeft <= 7 ? 'high' : ($daysLeft <= 30 ? 'medium' : 'low') }}">
                H-{{ $daysLeft }}
              </span>
            @endif
          </div>

          @if($folder->event_date)
            <div class="desc">📅 {{ $folder->event_date->locale('id')->translatedFormat('j F Y') }}</div>
          @endif

          <div class="price">Rp{{ number_format($folder->total_estimasi, 0, ',', '.') }}</div>
          <div style="font-size:.78rem; color:var(--ink-dim);">
            {{ $folder->total_dibeli }} dari {{ $folder->items_count }} item sudah dibeli
          </div>

          <div class="actions">
            <a href="{{ route('dashboard', ['folder_id' => $folder->id]) }}" class="btn btn-outline btn-sm">Lihat Item</a>
            <a href="{{ route('dashboard', ['folder_id' => $folder->id, 'status' => 'dibeli']) }}" class="btn btn-outline btn-sm">✅ Sudah Dibeli</a>
            <a href="{{ route('wishlist.quick-add', ['folder_id' => $folder->id]) }}" class="btn btn-outline btn-sm">📝 Catatan</a>
            <a href="{{ route('folders.edit', $folder) }}" class="btn btn-outline btn-sm">Edit</a>
            <form action="{{ route('folders.delete', $folder) }}" method="POST" onsubmit="return confirm('Hapus folder ini? Item di dalamnya tidak akan terhapus, hanya dipindah ke Tanpa Folder.');">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    @if($itemsWithoutFolder > 0)
      <div class="card-panel" style="margin-top:20px;">
        <p style="margin:0; font-size:.88rem; color:var(--ink-dim);">
          Ada <b>{{ $itemsWithoutFolder }}</b> item yang belum masuk folder mana pun.
          <a href="{{ route('dashboard', ['folder_id' => 'none']) }}">Lihat item tanpa folder →</a>
        </p>
      </div>
    @endif
  @endif

</x-layout>
