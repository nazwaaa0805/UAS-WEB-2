<x-layout :title="$folder ? 'Edit Folder' : 'Buat Folder'">

<div class="page-centered" style="max-width:560px;">

  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $folder ? 'Edit Folder' : 'Folder Baru' }}</div>
      <h1>{{ $folder ? 'Edit Folder Wishlist' : 'Buat Folder Wishlist' }}</h1>
    </div>
  </div>

  <div class="card-panel">
    <form action="{{ $folder ? route('folders.update', $folder) : route('folders.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <label for="name">Nama Folder</label>
      <input type="text" id="name" name="name" placeholder="Contoh: Ulang Tahun ke-20"
             value="{{ old('name', $folder->name ?? '') }}" required>

      <label>Jenis Folder</label>
      <div style="display:flex; gap:16px; margin-top:4px;">
        <label style="display:flex; align-items:center; gap:6px; font-weight:400; font-size:.88rem;">
          <input type="radio" name="type" value="biasa" style="width:auto;"
                 @checked(old('type', $folder->type ?? 'biasa') === 'biasa')> Folder Biasa
        </label>
        <label style="display:flex; align-items:center; gap:6px; font-weight:400; font-size:.88rem;">
          <input type="radio" name="type" value="catatan_belanja" style="width:auto;"
                 @checked(old('type', $folder->type ?? 'biasa') === 'catatan_belanja')> 📝 Catatan Belanja
        </label>
      </div>
      <p style="font-size:.76rem; color:var(--ink-dim); margin-top:4px; margin-bottom:0;">
        Folder Catatan Belanja ditandai "(C)" di daftar folder, biar gampang dibedain dari folder wishlist biasa.
      </p>

      <label for="icon">Emoji / Ikon (opsional)</label>
      <input type="text" id="icon" name="icon" placeholder="Klik salah satu di bawah, atau ketik sendiri"
             value="{{ old('icon', $folder->icon ?? '') }}" maxlength="10">
      <div class="emoji-picker">
        @foreach(['🎂','🎉','💍','🎄','🎓','🏠','❤️','🎁','🌟','🎊','👶','✈️','🏆','🕌','🎌','📚'] as $emoji)
          <button type="button" class="emoji-option" onclick="document.getElementById('icon').value='{{ $emoji }}'">{{ $emoji }}</button>
        @endforeach
        <button type="button" class="emoji-option emoji-clear" onclick="document.getElementById('icon').value=''">✕</button>
      </div>

      <label for="photo">Foto Tema Folder (opsional)</label>
      <input type="file" id="photo" name="photo" accept="image/*">
      <p style="font-size:.76rem; color:var(--ink-dim); margin-top:4px; margin-bottom:0;">
        Format gambar (jpg/png/dll), maksimal 4MB.
      </p>

      <div id="photo-preview-wrap" style="margin-top:10px; @if(!($folder && $folder->photo_path)) display:none; @endif">
        <img id="photo-preview" src="{{ $folder && $folder->photo_path ? $folder->photoUrl() : '' }}" alt="Preview foto tema folder" class="photo-preview-img">
        @if($folder && $folder->photo_path)
          <label style="display:flex; align-items:center; gap:6px; font-weight:400; font-size:.82rem; margin-top:8px;">
            <input type="checkbox" name="remove_photo" value="1" style="width:auto;"> Hapus foto ini
          </label>
        @endif
      </div>

      <script>
        document.getElementById('photo').addEventListener('change', function (e) {
          const wrap = document.getElementById('photo-preview-wrap');
          const img = document.getElementById('photo-preview');
          const file = e.target.files[0];
          if (file) {
            img.src = URL.createObjectURL(file);
            wrap.style.display = 'block';
          }
        });
      </script>

      <label for="event_date">Tanggal Acara (opsional)</label>
      <input type="date" id="event_date" name="event_date"
             value="{{ old('event_date', $folder?->event_date?->format('Y-m-d') ?? '') }}">

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">{{ $folder ? 'Simpan Perubahan' : 'Buat Folder' }}</button>
        <a href="{{ route('folders.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>

</div>

</x-layout>
