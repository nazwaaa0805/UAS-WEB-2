<x-layout :title="'Daftar Akun'">
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="eyebrow">Mulai Sekarang</div>
      <h1>Buat Akun Baru</h1>
      <p class="sub">Catat semua hal yang kamu inginkan di satu tempat.</p>

      <form action="{{ route('register') }}" method="POST">
        @csrf
        <label for="name">Nama Lengkap</label>
        <input type="text" id="name" name="name" placeholder="Nama kamu" value="{{ old('name') }}" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="nama@email.com" value="{{ old('email') }}" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>

        <label for="confirm_password">Konfirmasi Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>

        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
      </form>

      <p class="sub" style="margin-top:20px; margin-bottom:0;">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
      </p>
    </div>
  </div>
</x-layout>
