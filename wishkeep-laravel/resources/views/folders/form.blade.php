<x-layout :title="$folder ? 'Edit Folder' : 'Buat Folder'">

<div class="page-centered" style="max-width:560px;">

  <div class="page-head">
    <div>
      <div class="eyebrow">{{ $folder ? 'Edit Folder' : 'Folder Baru' }}</div>
      <h1>{{ $folder ? 'Edit Folder Wishlist' : 'Buat Folder Wishlist' }}</h1>
    </div>
  </div>

  <div class="card-panel">
    <form action="{{ $folder ? route('folders.update', $folder) : route('folders.store') }}" method="POST">
      @csrf

      <label for="name">Nama Folder</label>
      <input type="text" id="name" name="name" placeholder="Contoh: Ulang Tahun ke-20"
             value="{{ old('name', $folder->name ?? '') }}" required>

      <label for="icon">Emoji / Ikon (opsional)</label>
      <input type="text" id="icon" name="icon" placeholder="Klik salah satu di bawah, atau ketik sendiri"
             value="{{ old('icon', $folder->icon ?? '') }}" maxlength="10">
      <div class="emoji-picker">
        @foreach(['🎂','🎉','💍','🎄','🎓','🏠','❤️','🎁','🌟','🎊','👶','✈️','🏆','🕌','🎌','📚'] as $emoji)
          <button type="button" class="emoji-option" onclick="document.getElementById('icon').value='{{ $emoji }}'">{{ $emoji }}</button>
        @endforeach
        <button type="button" class="emoji-option emoji-clear" onclick="document.getElementById('icon').value=''">✕</button>
      </div>

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
