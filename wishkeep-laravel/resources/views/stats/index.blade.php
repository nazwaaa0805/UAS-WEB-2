<x-layout :title="'Statistik'">

  <div class="page-head">
    <div>
      <div class="eyebrow">Insight Wishlist</div>
      <h1>Statistik</h1>
    </div>
  </div>

  @if($totalItems === 0)
    <div class="empty-state">
      <div class="star-big">📊</div>
      <h3>Belum ada data untuk ditampilkan</h3>
      <p>Tambah beberapa item wishlist dulu, statistik akan muncul otomatis di sini.</p>
      <a href="{{ route('wishlist.create') }}" class="btn btn-primary" style="margin-top:12px;">+ Tambah Item</a>
    </div>
  @else
    <div class="item-grid" style="grid-template-columns:repeat(auto-fit,minmax(340px,1fr));">
      <div class="card-panel">
        <h3 style="margin-bottom:16px;">Estimasi Belanja per Kategori</h3>
        <canvas id="chartCategory" height="260"></canvas>
      </div>

      <div class="card-panel">
        <h3 style="margin-bottom:16px;">Belum vs Sudah Dibeli</h3>
        <canvas id="chartStatus" height="260"></canvas>
      </div>

      <div class="card-panel">
        <h3 style="margin-bottom:16px;">Item per Prioritas</h3>
        <canvas id="chartPriority" height="260"></canvas>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
      const inkColor = '#33414a';
      const palette = ['#596E79', '#C7B198', '#DFD3C3', '#3f4f58', '#a89478', '#7c8b93'];

      // ===== Grafik 1: Pie per kategori =====
      new Chart(document.getElementById('chartCategory'), {
        type: 'pie',
        data: {
          labels: @json($categoryLabels),
          datasets: [{
            data: @json($categoryValues),
            backgroundColor: palette,
            borderWidth: 0,
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom', labels: { color: inkColor, font: { family: 'Inter' } } }
          }
        }
      });

      // ===== Grafik 2: Doughnut belum vs dibeli =====
      new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
          labels: ['Belum Dibeli', 'Sudah Dibeli'],
          datasets: [{
            data: [{{ $statusCount['belum'] }}, {{ $statusCount['dibeli'] }}],
            backgroundColor: ['#DFD3C3', '#596E79'],
            borderWidth: 0,
          }]
        },
        options: {
          plugins: {
            legend: { position: 'bottom', labels: { color: inkColor, font: { family: 'Inter' } } }
          }
        }
      });

      // ===== Grafik 3: Bar per prioritas =====
      new Chart(document.getElementById('chartPriority'), {
        type: 'bar',
        data: {
          labels: ['Tinggi', 'Sedang', 'Rendah'],
          datasets: [{
            label: 'Jumlah Item',
            data: [{{ $priorityCount['high'] }}, {{ $priorityCount['medium'] }}, {{ $priorityCount['low'] }}],
            backgroundColor: ['#8f382b', '#C7B198', '#5fbf8c'],
            borderRadius: 6,
          }]
        },
        options: {
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, color: inkColor } },
            x: { ticks: { color: inkColor } }
          }
        }
      });
    </script>
  @endif

</x-layout>
