<x-layout :title="'Kelola Kategori'">

  <div class="page-head">
    <div>
      <div class="eyebrow">Panel Admin</div>
      <h1>Kelola Kategori</h1>
    </div>
  </div>

  <div class="card-panel" style="max-width:480px;">
    <form action="{{ route('admin.categories.store') }}" method="POST" style="display:flex; gap:10px; align-items:flex-end;">
      @csrf
      <div style="flex-grow:1;">
        <label for="name">Kategori Baru</label>
        <input type="text" id="name" name="name" placeholder="Contoh: Olahraga" required>
      </div>
      <button type="submit" class="btn btn-primary">Tambah</button>
    </form>
  </div>

  @if($pendingCategories->isNotEmpty())
    <div class="card-panel" style="border-color:rgba(226,104,95,0.4);">
      <h3 style="font-size:1rem; margin-bottom:4px;">⏳ Menunggu Persetujuan ({{ $pendingCategories->count() }})</h3>
      <p style="font-size:.8rem; color:var(--ink-dim); margin-top:0; margin-bottom:14px;">
        Kategori ini diajukan oleh user dan belum bisa dipakai sampai kamu setujui.
      </p>
      <table>
        <thead><tr><th>Nama Kategori</th><th>Diajukan Oleh</th><th>Aksi</th></tr></thead>
        <tbody>
          @foreach($pendingCategories as $c)
            <tr>
              <td>{{ $c->name }}</td>
              <td>{{ $c->requestedBy->name ?? '-' }}</td>
              <td style="display:flex; gap:6px;">
                <form action="{{ route('admin.categories.approve', $c) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm">Setujui</button>
                </form>
                <form action="{{ route('admin.categories.delete', $c) }}" method="POST" onsubmit="return confirm('Tolak & hapus pengajuan ini?');">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <div class="card-panel">
    <table>
      <thead><tr><th>Nama Kategori</th><th>Jumlah Item</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($categories as $c)
          <tr>
            <td>{{ $c->name }}</td>
            <td>{{ $c->total_items }}</td>
            <td>
              <form action="{{ route('admin.categories.delete', $c) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</x-layout>
