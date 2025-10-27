@extends('app')

@section('content')
<style>
  :root{
    --bg:#0d0b16;
    --panel:#17132a;
    --panel-2:#1f1938;
    --stroke:#7c3aed;
    --accent:#22d3ee;
    --accent2:#a78bfa;
    --text:#f1f5f9;
    --muted:#94a3b8;
    --border:#2a234a;
  }

  body{ background: radial-gradient(1200px 600px at 20% -10%, #201a3a 0%, var(--bg) 55%), var(--bg); }
  .dash{ color:var(--text); }

  .panel{
    position:relative; background:linear-gradient(180deg,var(--panel),var(--panel-2));
    border-radius:18px; padding:16px 18px; box-shadow:0 8px 30px rgba(0,0,0,.35);
    overflow:visible; isolation:isolate;
  }
  .panel::before{
    content:""; position:absolute; inset:0; border-radius:18px; padding:1px;
    background:linear-gradient(135deg,transparent 30%,var(--stroke),transparent 70%);
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite: xor; mask-composite: exclude; z-index:0;
  }
  .panel *{ position:relative; z-index:1; }

  .panel h6{ font-weight:700; letter-spacing:.2px; margin-bottom:6px; }
  .muted{ color:var(--muted); font-size:12px; }
  .grid{ display:grid; gap:16px; }
  @media(min-width:992px){ .grid-4{ grid-template-columns: repeat(4, 1fr); } }

  .filter .form-select, .filter .form-control{
    background:#120f22; border:1px solid var(--border); color:var(--text);
  }
  .filter label{ font-size:12px; color:var(--muted); }

  /* Apply dihapus; gaya gradient dipindahkan ke .btn-reset */
  .btn-reset{
    background:linear-gradient(90deg,var(--stroke),var(--accent));
    border:0; color:#fff;
  }

  .h180{ height:180px; } .h220{ height:220px; } .h280{ height:280px; }

  .corner{position:absolute;width:14px;height:14px;border:2px solid var(--accent);z-index:2;}
  .c1{left:8px;top:8px;border-right:none;border-bottom:none}
  .c2{right:8px;top:8px;border-left:none;border-bottom:none}
  .c3{left:8px;bottom:8px;border-right:none;border-top:none}
  .c4{right:8px;bottom:8px;border-left:none;border-top:none}

  .donut-center{
    position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
    font-size:22px; font-weight:700; text-align:center; color:var(--accent);
  }
</style>

<div class="dash container py-3">
  {{-- FILTERS --}}
  <div class="panel filter mb-3">
    <div class="corner c1"></div><div class="corner c2"></div><div class="corner c3"></div><div class="corner c4"></div>

    {{-- onChange auto-submit --}}
    <form method="get" class="grid grid-4" id="filterForm">
      <div>
        <label>Year</label>
        <select name="year" class="form-select auto-submit">
          @foreach(($years ?? collect([date('Y')])) as $y)
            <option value="{{ $y }}" {{ (int)$y === (int)($year ?? date('Y')) ? 'selected':'' }}>
              {{ $y }}
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label>Month</label>
        <select name="month" class="form-select auto-submit">
          <option value="">All</option>
          @for($m=1;$m<=12;$m++)
            <option value="{{ $m }}" {{ (int)($month ?? 0) === $m ? 'selected':'' }}>
              {{ \Carbon\Carbon::create()->month($m)->format('F') }}
            </option>
          @endfor
        </select>
      </div>
      <div>
        <label>Day</label>
        <select name="day" class="form-select auto-submit">
          <option value="">All</option>
          @for($d=1;$d<=31;$d++)
            <option value="{{ $d }}" {{ (int)($day ?? 0) === $d ? 'selected':'' }}>{{ $d }}</option>
          @endfor
        </select>
      </div>
      <div></div>
      <div>
        <label>Material</label>
        <select name="material" class="form-select auto-submit">
          <option value="">All</option>
          @foreach(($materials ?? collect()) as $m)
            <option value="{{ $m }}" {{ ($material ?? '') === $m ? 'selected':'' }}>{{ $m }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>Unit</label>
        <select name="unit" class="form-select auto-submit">
          <option value="">All</option>
          @foreach(($units ?? collect()) as $u)
            <option value="{{ $u }}" {{ ($unit ?? '') === $u ? 'selected':'' }}>{{ $u }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label>SO Type</label>
        <select name="so_type" class="form-select auto-submit">
          <option value="">All</option>
          @foreach(($soTypes ?? collect()) as $s)
            <option value="{{ $s }}" {{ ($soType ?? '') === $s ? 'selected':'' }}>{{ $s }}</option>
          @endforeach
        </select>
      </div>

      {{-- Apply dihapus; sisa tombol Reset saja dan pakai gradient --}}
      <div class="d-flex align-items-end">
        <a href="{{ route('dashboard') }}" class="btn btn-reset px-3">Reset</a>
      </div>
    </form>
  </div>

  {{-- KPI ROW --}}
  <div class="grid grid-4 mb-3">
    {{-- YTD --}}
    <div class="panel position-relative">
      <div class="corner c1"></div><div class="corner c2"></div><div class="corner c3"></div><div class="corner c4"></div>
      <h6>YTD Sales Order (Ton)</h6>
      <div class="muted">Target: {{ number_format($targetYTD ?? 0,3) }} Ton</div>
      <div class="position-relative">
        <canvas id="ytdDonut" class="h180 mt-2"></canvas>
        <div class="donut-center">{{ number_format($realisasiYTD ?? 0,0) }}</div>
      </div>
    </div>

    {{-- MTD --}}
    <div class="panel position-relative">
      <div class="corner c1"></div><div class="corner c2"></div><div class="corner c3"></div><div class="corner c4"></div>
      <h6>Monthly Sales Order (Ton)</h6>
      <div class="muted">Target: {{ number_format($targetMTD ?? 0,3) }} Ton</div>
      <div class="position-relative">
        <canvas id="mtdDonut" class="h180 mt-2"></canvas>
        <div class="donut-center">{{ number_format($realisasiMTD ?? 0,0) }}</div>
      </div>
    </div>

    {{-- Top 10 --}}
    <div class="panel" style="grid-column: span 2;">
      <div class="corner c1"></div><div class="corner c2"></div><div class="corner c3"></div><div class="corner c4"></div>
      <h6>Sales Order Quantity (Top 10) (Ton)</h6>
      <canvas id="top10Bar" class="h220"></canvas>
    </div>
  </div>

  {{-- LSD --}}
  <div class="panel">
    <div class="corner c1"></div><div class="corner c2"></div><div class="corner c3"></div><div class="corner c4"></div>
    <h6>LSD Jaminan Status (Ton)</h6>
    <canvas id="lsdBar" class="h280 mt-2"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
  const $id = (x)=>document.getElementById(x);
  Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--text').trim();
  Chart.defaults.font.family = `'Inter', 'Segoe UI', system-ui, -apple-system, Roboto, Arial, sans-serif`;

  // Auto-submit: submit form saat salah satu filter berubah
  (function(){
    const form = document.getElementById('filterForm');
    if(!form) return;
    // delegasi: semua element dengan class .auto-submit
    form.querySelectorAll('.auto-submit').forEach(el=>{
      el.addEventListener('change', ()=> form.submit(), {passive:true});
    });
  })();

  // YTD
  (function(){
    const el = $id('ytdDonut'); if(!el) return;
    const pct = Number({{ $ytdPct ?? 0 }}) || 0;
    new Chart(el,{ type:'doughnut',
      data:{ labels:['Progress','Remain'],
        datasets:[{ data:[pct, Math.max(0,100-pct)], backgroundColor:['#22d3ee','#3b2c67'] }] },
      options:{ cutout:'72%', plugins:{legend:{display:false}}, animation:{duration:700,easing:'easeOutQuart'} }
    });
  })();

  // MTD
  (function(){
    const el = $id('mtdDonut'); if(!el) return;
    const pct = Number({{ $mtdPct ?? 0 }}) || 0;
    new Chart(el,{ type:'doughnut',
      data:{ labels:['Progress','Remain'],
        datasets:[{ data:[pct, Math.max(0,100-pct)], backgroundColor:['#a78bfa','#3b2c67'] }] },
      options:{ cutout:'72%', plugins:{legend:{display:false}}, animation:{duration:700,easing:'easeOutQuart'} }
    });
  })();

  // Top 10 (horizontal bar)
  (function(){
    const el = $id('top10Bar'); if(!el) return;
    let labels = {!! json_encode(($top10 ?? collect())->pluck('company_name')->values()) !!};
    let data   = {!! json_encode(($top10 ?? collect())->pluck('total_ton')->map(fn($v)=>(float)$v)->values()) !!};
    @if(($others ?? 0) > 0)
      labels = ['Others', ...labels];
      data   = [Number({{ $others ?? 0 }}), ...data];
    @endif
    new Chart(el,{ type:'bar',
      data:{ labels, datasets:[{ data, backgroundColor:'#22d3ee' }] },
      options:{ indexAxis:'y', plugins:{legend:{display:false}},
        scales:{ x:{beginAtZero:true, grid:{color:'#2b2348'}}, y:{grid:{display:false}} } }
    });
  })();

  // LSD Bar
  (function(){
    const el = $id('lsdBar'); if(!el) return;
    const labels = {!! json_encode($lsdLabels ?? []) !!};
    const data   = {!! json_encode($lsdValues ?? []) !!}.map(Number);
    new Chart(el,{ type:'bar',
      data:{ labels, datasets:[{ data, backgroundColor:'#a78bfa' }] },
      options:{ plugins:{legend:{display:false}},
        scales:{ y:{beginAtZero:true, grid:{color:'#2b2348'}}, x:{grid:{display:false}} } }
    });
  })();
</script>
@endsection
