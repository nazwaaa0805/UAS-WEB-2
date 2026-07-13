<x-layout :title="'Riwayat Aktivitas'">

  <div class="page-head">
    <div>
      <div class="eyebrow">Panel Admin</div>
      <h1>Riwayat Aktivitas</h1>
    </div>
  </div>

  <div class="card-panel">
    @if($logs->isEmpty())
      <p style="color:var(--ink-dim); margin:0;">Belum ada aktivitas tercatat.</p>
    @else
      <table>
        <thead>
          <tr><th style="width:120px;">Aksi</th><th>Deskripsi</th><th style="width:180px;">Waktu</th></tr>
        </thead>
        <tbody>
          @foreach($logs as $log)
            <tr>
              <td>
                <span class="priority-tag priority-{{ match($log->action) {
                  'create' => 'low',
                  'delete' => 'high',
                  default => 'medium',
                } }}">
                  {{ match($log->action) {
                    'create' => 'Tambah',
                    'update' => 'Edit',
                    'delete' => 'Hapus',
                    'toggle' => 'Status',
                    'saving' => 'Nabung',
                    'folder' => 'Folder',
                    default => $log->action,
                  } }}
                </span>
              </td>
              <td>{{ $log->description }}</td>
              <td style="font-size:.78rem; color:var(--ink-dim);">
                {{ $log->created_at->locale('id')->translatedFormat('j M Y, H:i') }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div style="margin-top:18px;">
        {{ $logs->links() }}
      </div>
    @endif
  </div>

</x-layout>
