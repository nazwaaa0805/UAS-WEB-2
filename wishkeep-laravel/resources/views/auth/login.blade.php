<x-layout :title="'Login'">
  <div class="auth-wrap">
    <div class="auth-card">
      <div class="eyebrow">Selamat Datang</div>
      <h1>Masuk ke WishKeep</h1>
      <p class="sub">Kelola daftar keinginanmu, satu bintang pada satu waktu.</p>

      <form action="{{ route('login') }}" method="POST">
        @csrf
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="nama@email.com" value="{{ old('email') }}" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
      </form>

      <p class="sub" style="margin-top:20px; margin-bottom:0;">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
      </p>
    </div>
  </div>
</x-layout>
