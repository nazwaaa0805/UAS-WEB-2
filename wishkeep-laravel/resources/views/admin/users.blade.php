<x-layout :title="'Kelola Pengguna'">

  <div class="page-head">
    <div>
      <div class="eyebrow">Panel Admin</div>
      <h1>Kelola Pengguna</h1>
    </div>
  </div>

  <div class="card-panel">
    <table>
      <thead>
        <tr><th>Nama</th><th>Email</th><th>Role</th><th>Total Item</th><th>Bergabung</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->role }}</td>
            <td>{{ $u->total_items }}</td>
            <td>{{ $u->created_at->locale('id')->translatedFormat('j M Y') }}</td>
            <td>
              @if($u->role !== 'admin')
                <form action="{{ route('admin.users.delete', $u) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini beserta seluruh wishlist-nya?');">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
              @else
                <span style="color:var(--cream-dim); font-size:.78rem;">—</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</x-layout>
