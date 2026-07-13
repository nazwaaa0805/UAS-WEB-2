<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Wishlist · {{ $user->name }}</title>
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; }
      .sheet { box-shadow: none !important; margin: 0 !important; }
    }
    * { box-sizing: border-box; }
    body {
      font-family: 'Georgia', 'Times New Roman', serif;
      background: #e9e9e9;
      margin: 0;
      padding: 24px;
      color: #1a1a1a;
    }
    .no-print {
      max-width: 850px; margin: 0 auto 16px auto;
      display: flex; justify-content: space-between; align-items: center;
      font-family: Arial, sans-serif;
    }
    .no-print button {
      background: #596E79; color: #F0ECE3; border: none; padding: 10px 20px;
      border-radius: 6px; font-size: 14px; cursor: pointer; font-weight: 600;
    }
    .no-print a { font-family: Arial, sans-serif; color: #333; text-decoration: none; font-size: 14px; }
    .sheet {
      max-width: 850px; margin: 0 auto; background: #fff; padding: 48px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .doc-head {
      display: flex; justify-content: space-between; align-items: flex-start;
      border-bottom: 3px double #1a1a1a; padding-bottom: 16px; margin-bottom: 24px;
    }
    .doc-head h1 { margin: 0 0 4px 0; font-size: 26px; letter-spacing: .5px; }
    .doc-head .meta { font-size: 12px; color: #555; font-family: Arial, sans-serif; }
    .doc-head .brand { text-align: right; font-family: Arial, sans-serif; }
    .doc-head .brand .star { color: #C7B198; font-size: 18px; }
    .doc-head .brand .name { font-weight: bold; font-size: 15px; }

    table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12.5px; }
    thead th {
      text-align: left; border-bottom: 2px solid #1a1a1a; padding: 8px 6px;
      text-transform: uppercase; font-size: 10.5px; letter-spacing: .5px; color: #333;
    }
    tbody td { padding: 9px 6px; border-bottom: 1px solid #ddd; vertical-align: top; }
    tbody tr:nth-child(even) { background: #f7f5ef; }
    .col-title { font-weight: bold; }
    .col-desc { color: #555; font-size: 11.5px; }
    .col-price { text-align: right; white-space: nowrap; }
    .badge { font-size: 10px; padding: 2px 7px; border-radius: 3px; font-weight: bold; border: 1px solid #999; }
    .badge-tinggi { border-color: #a33; color: #a33; }
    .badge-sedang { border-color: #b8860b; color: #b8860b; }
    .badge-rendah { border-color: #3a8; color: #3a8; }
    .badge-status-done { background: #eef7f0; border-color: #3a8; color: #3a8; }

    .summary {
      display: flex; justify-content: flex-end; margin-top: 20px;
      font-family: Arial, sans-serif;
    }
    .summary-box { text-align: right; }
    .summary-box .row { display: flex; justify-content: space-between; gap: 40px; padding: 4px 0; font-size: 13px; }
    .summary-box .total { font-weight: bold; font-size: 16px; border-top: 2px solid #1a1a1a; padding-top: 8px; margin-top: 4px; }

    .doc-footer {
      margin-top: 40px; padding-top: 14px; border-top: 1px solid #ccc;
      font-family: Arial, sans-serif; font-size: 10.5px; color: #777; text-align: center;
    }
  </style>
</head>
<body>

  <div class="no-print">
    <a href="{{ route('dashboard') }}">&larr; Kembali ke Dashboard</a>
    <button onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
  </div>

  <div class="sheet">
    <div class="doc-head">
      <div>
        <h1>Daftar Wishlist</h1>
        <div class="meta">
          Dicetak pada {{ $printedAt->locale('id')->translatedFormat('j F Y') }}
          &middot; Filter status: {{ $statusFilter === 'semua' ? 'Semua' : ($statusFilter === 'belum' ? 'Belum Dibeli' : 'Sudah Dibeli') }}
        </div>
      </div>
      <div class="brand">
        <div class="star">✦</div>
        <div class="name">WishKeep</div>
        <div class="meta">{{ $user->name }}</div>
      </div>
    </div>

    @if($items->isEmpty())
      <p style="font-family:Arial,sans-serif; color:#666;">Tidak ada item wishlist untuk ditampilkan.</p>
    @else
      <table>
        <thead>
          <tr>
            <th style="width:26%;">Item</th>
            <th style="width:28%;">Deskripsi</th>
            <th style="width:12%;">Kategori</th>
            <th style="width:6%;" class="col-price">Jml</th>
            <th style="width:9%;">Prioritas</th>
            <th style="width:9%;">Status</th>
            <th style="width:14%;" class="col-price">Estimasi Harga</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $item)
            <tr>
              <td class="col-title">{{ $item->title }}</td>
              <td class="col-desc">{{ $item->description ?: '-' }}</td>
              <td>{{ $item->category->name ?? '-' }}</td>
              <td class="col-price">{{ $item->quantity ?? 1 }}</td>
              <td>
                <span class="badge badge-{{ $item->priority === 'high' ? 'tinggi' : ($item->priority === 'medium' ? 'sedang' : 'rendah') }}">
                  {{ $item->priority === 'high' ? 'Tinggi' : ($item->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                </span>
              </td>
              <td>
                <span class="badge {{ $item->status === 'dibeli' ? 'badge-status-done' : '' }}">
                  {{ $item->status === 'dibeli' ? 'Dibeli' : 'Belum' }}
                </span>
              </td>
              <td class="col-price">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="summary">
        <div class="summary-box">
          <div class="row"><span>Total Item</span><span>{{ $items->count() }}</span></div>
          <div class="row total"><span>Estimasi Belanja (Belum Dibeli)</span><span>Rp{{ number_format($totalEstimasi, 0, ',', '.') }}</span></div>
        </div>
      </div>
    @endif

    <div class="doc-footer">
      Dokumen ini dibuat otomatis oleh sistem WishKeep &middot; Fitur Cetak Wishlist
    </div>
  </div>

</body>
</html>
