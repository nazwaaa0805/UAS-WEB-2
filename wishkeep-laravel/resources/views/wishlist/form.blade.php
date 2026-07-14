<x-layout :title="$item ? 'Edit Item Wishlist' : 'Tambah Item Wishlist'">

<div class="page-centered">

  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $item ? 'Edit Item' : 'Item Baru' }}</div>
      <h1>{{ $item ? 'Edit Wishlist' : 'Tambah ke Wishlist' }}</h1>
    </div>
  </div>

  <div class="card-panel">
    <form action="{{ $item ? route('wishlist.update', $item) : route('wishlist.store') }}" method="POST">
      @csrf

      <label for="title">Judul Item</label>
      <input type="text" id="title" name="title" placeholder="Contoh: Sepatu Lari Baru"
             value="{{ old('title', $item->title ?? '') }}" required>

      <label for="description">Deskripsi</label>
      <textarea id="description" name="description" placeholder="Catatan tambahan (opsional)">{{ old('description', $item->description ?? '') }}</textarea>

      <label for="price">Estimasi Harga (Rp)</label>
      <input type="number" id="price" name="price" min="0" step="1000" value="{{ old('price', $item->price ?? 0) }}">

      <label for="quantity">Jumlah</label>
      <input type="text" id="quantity" name="quantity" placeholder="cth: 1, 2 lusin, 12 buah" maxlength="50" value="{{ old('quantity', $item->quantity ?? 1) }}">

      <label for="saved_amount">Sudah Menabung (Rp) — opsional</label>
      <input type="number" id="saved_amount" name="saved_amount" min="0" step="1000" value="{{ old('saved_amount', $item->saved_amount ?? 0) }}">

      <label for="target_date">Target Tanggal Beli (opsional)</label>
      <input type="date" id="target_date" name="target_date" value="{{ old('target_date', $item?->target_date?->format('Y-m-d') ?? '') }}">
      <p style="font-size:.76rem; color:var(--ink-dim); margin-top:4px; margin-bottom:0;">
        Jika diisi, akan muncul reminder di dashboard saat tanggalnya sudah dekat (H-7).
      </p>

      <label for="link">Tautan Produk (opsional)</label>
      <input type="url" id="link" name="link" placeholder="https://..." value="{{ old('link', $item->link ?? '') }}">

      <label for="category_id">Kategori</label>
      <select id="category_id" name="category_id">
        <option value="">Tanpa Kategori</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}" @selected($item && (string) $item->category_id === (string) $c->id)>{{ $c->name }}</option>
        @endforeach
      </select>

      <label for="folder_id">Folder (opsional)</label>
      <select id="folder_id" name="folder_id">
        <option value="">Tanpa Folder</option>
        @foreach($folders as $f)
          <option value="{{ $f->id }}" @selected((string) ($selectedFolderId ?? '') === (string) $f->id)>{{ $f->displayName() }}</option>
        @endforeach
      </select>

      <label for="priority">Prioritas</label>
      <select id="priority" name="priority">
        <option value="low" @selected($item && $item->priority === 'low')>Rendah</option>
        <option value="medium" @selected(!$item || $item->priority === 'medium')>Sedang</option>
        <option value="high" @selected($item && $item->priority === 'high')>Tinggi</option>
      </select>

      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="belum" @selected(!$item || $item->status === 'belum')>Belum Dibeli</option>
        <option value="dibeli" @selected($item && $item->status === 'dibeli')>Sudah Dibeli</option>
      </select>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">{{ $item ? 'Simpan Perubahan' : 'Tambah Item' }}</button>
        <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>

  <div class="card-panel">
    <h3 style="font-size:1rem; margin-bottom:10px;">Tambah Kategori Baru</h3>
    <p style="font-size:.82rem; color:var(--ink-dim); margin-top:0; margin-bottom:12px;">
      Gak nemu kategori yang cocok? Tambah sendiri di sini — langsung bisa dipakai.
    </p>
    <form action="{{ route('categories.request') }}" method="POST" style="display:flex; gap:8px;">
      @csrf
      <input type="text" name="name" placeholder="Nama kategori baru" style="flex-grow:1;" required>
      <button type="submit" class="btn btn-outline">Tambah</button>
    </form>
  </div>

</div>

</x-layout>
