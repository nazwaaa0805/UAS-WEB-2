<x-layout :title="'Catatan Belanja'">

<div class="page-head">
  <div>
    <div class="eyebrow">Perintilan &amp; Belanja Kecil</div>
    <h1>📝 Catatan Belanja</h1>
    <p class="sub" style="margin:6px 0 0 0;">
      Buat catatan singkat untuk barang-barang kecil (bahan makanan, alat tulis, dll) —
      isi nama &amp; jumlahnya, harga boleh dikosongkan. Semua baris yang terisi
      langsung jadi item wishlist.
    </p>
  </div>
</div>

<div class="card-panel">
  <form action="{{ route('wishlist.quick-add.store') }}" method="POST" id="quick-add-form">
    @csrf

    <div style="display:flex; gap:16px; flex-wrap:wrap;">
      <div style="flex:1; min-width:220px;">
        <label for="folder_id">Masukkan ke Folder (opsional)</label>
        <select id="folder_id" name="folder_id">
          <option value="">Tanpa Folder</option>
          @foreach($folders as $f)
            <option value="{{ $f->id }}" @selected((string) ($selectedFolderId ?? '') === (string) $f->id)>{{ $f->icon ? $f->icon.' ' : '' }}{{ $f->name }}</option>
          @endforeach
        </select>
      </div>
      <div style="flex:1; min-width:220px;">
        <label for="category_id">Kategori (opsional, berlaku untuk semua baris)</label>
        <select id="category_id" name="category_id">
          <option value="">Tanpa Kategori</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="quick-add-table-wrap">
      <table class="quick-add-table" id="quick-add-table">
        <thead>
          <tr>
            <th style="width:44%;">Nama Barang</th>
            <th style="width:16%;">Jumlah</th>
            <th style="width:18%;">Harga (opsional)</th>
            <th style="width:16%;">Total</th>
            <th style="width:6%;"></th>
          </tr>
        </thead>
        <tbody id="quick-add-rows">
          @for ($i = 0; $i < 5; $i++)
            <tr class="quick-add-row">
              <td><input type="text" name="items[{{ $i }}][title]" placeholder="Contoh: Beras 5kg" autocomplete="off"></td>
              <td><input type="text" name="items[{{ $i }}][quantity]" value="1" placeholder="cth: 2 lusin" class="qa-qty" autocomplete="off"></td>
              <td><input type="number" name="items[{{ $i }}][price]" placeholder="0" min="0" step="500" class="qa-price"></td>
              <td><span class="qa-total">Rp0</span></td>
              <td><button type="button" class="qa-remove-btn" title="Hapus baris">×</button></td>
            </tr>
          @endfor
        </tbody>
      </table>
    </div>

    <p style="font-size:.8rem; color:var(--ink-dim); margin:8px 0 0 0;">
      Kolom Jumlah boleh diisi angka biasa (mis. <b>3</b>) atau kalimat singkat (mis. <b>2 lusin</b>, <b>12 buah</b>, <b>1 pack</b>).
      Untuk perhitungan Total, angka di depan tulisan itu yang dipakai — sisanya cuma buat kamu.
    </p>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; flex-wrap:wrap; gap:10px;">
      <button type="button" id="qa-add-row" class="btn btn-outline btn-sm">+ Tambah Baris</button>
      <div style="font-size:.92rem; color:var(--ink-dim);">
        Estimasi Total: <b id="qa-grand-total" style="color:var(--brown); font-family:'Fraunces',serif; font-size:1.1rem;">Rp0</b>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan ke Wishlist</button>
      <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>

<script>
  (function () {
    const rowsBody = document.getElementById('quick-add-rows');
    const addRowBtn = document.getElementById('qa-add-row');
    const grandTotalEl = document.getElementById('qa-grand-total');
    let rowIndex = rowsBody.querySelectorAll('.quick-add-row').length;

    function formatRupiah(num) {
      return 'Rp' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function makeRow(index) {
      const tr = document.createElement('tr');
      tr.className = 'quick-add-row';
      tr.innerHTML = `
        <td><input type="text" name="items[${index}][title]" placeholder="Nama barang" autocomplete="off"></td>
        <td><input type="text" name="items[${index}][quantity]" value="1" placeholder="cth: 2 lusin" class="qa-qty" autocomplete="off"></td>
        <td><input type="number" name="items[${index}][price]" placeholder="0" min="0" step="500" class="qa-price"></td>
        <td><span class="qa-total">Rp0</span></td>
        <td><button type="button" class="qa-remove-btn" title="Hapus baris">×</button></td>
      `;
      return tr;
    }

    function parseQty(raw) {
      const match = String(raw || '').trim().match(/^(\d+([.,]\d+)?)/);
      if (!match) return 1;
      const num = parseFloat(match[1].replace(',', '.'));
      return isNaN(num) || num <= 0 ? 1 : num;
    }

    function recalcRow(row) {
      const qty = parseQty(row.querySelector('.qa-qty').value);
      const price = parseFloat(row.querySelector('.qa-price').value) || 0;
      const total = qty * price;
      row.querySelector('.qa-total').textContent = formatRupiah(total);
      return total;
    }

    function recalcAll() {
      let grand = 0;
      rowsBody.querySelectorAll('.quick-add-row').forEach(row => {
        grand += recalcRow(row);
      });
      grandTotalEl.textContent = formatRupiah(grand);
    }

    addRowBtn.addEventListener('click', () => {
      rowsBody.appendChild(makeRow(rowIndex));
      rowIndex++;
    });

    rowsBody.addEventListener('click', (e) => {
      if (e.target.classList.contains('qa-remove-btn')) {
        const rows = rowsBody.querySelectorAll('.quick-add-row');
        if (rows.length > 1) {
          e.target.closest('.quick-add-row').remove();
          recalcAll();
        } else {
          // baris terakhir: kosongkan saja, jangan dihapus
          const row = e.target.closest('.quick-add-row');
          row.querySelectorAll('input').forEach(inp => {
            if (inp.classList.contains('qa-qty')) inp.value = 1;
            else inp.value = '';
          });
          recalcAll();
        }
      }
    });

    rowsBody.addEventListener('input', (e) => {
      if (e.target.closest('.quick-add-row')) {
        recalcAll();
      }
    });

    recalcAll();
  })();
</script>

</x-layout>
