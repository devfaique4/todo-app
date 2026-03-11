@extends('layouts.app')
@section('title','Reports & Analytics')

@section('extra-css')
<style>
.reports-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.report-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
.rc-header { padding: 18px 22px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.rc-title { font-family: 'Cabinet Grotesk', sans-serif; font-weight: 800; font-size: 15px; }
.rc-body { padding: 22px; }
.chart-wrap { position: relative; height: 240px; }

.proj-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border); }
.proj-row:last-child { border-bottom: none; }
.proj-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.proj-name { flex: 1; font-size: 13px; font-weight: 500; }
.proj-progress-wrap { width: 100px; height: 5px; background: var(--surface3); border-radius: 99px; overflow: hidden; }
.proj-progress-fill { height: 100%; border-radius: 99px; background: var(--accent-g); transition: width 0.8s; }
.proj-pct { font-size: 12px; color: var(--accent); font-weight: 700; min-width: 34px; text-align: right; }

.overdue-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
.overdue-item:last-child { border-bottom: none; }
.overdue-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--red); flex-shrink: 0; }
.overdue-name { flex: 1; }
.overdue-date { color: var(--red); font-size: 12px; }

.export-btns { display: flex; gap: 10px; flex-wrap: wrap; }
@media(max-width:768px) { .reports-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')

{{-- STATS ROW --}}
<div class="stats-grid" style="margin-bottom:24px">
    @php $cols = [
        ['val'=>$stats['total'],      'label'=>'Total Tasks',  'color'=>'var(--accent)', 'bg'=>'rgba(108,99,255,0.12)', 'icon'=>'list-check'],
        ['val'=>$stats['completed'],  'label'=>'Completed',    'color'=>'var(--green)',  'bg'=>'rgba(0,229,160,0.12)',  'icon'=>'check-circle'],
        ['val'=>$stats['in_progress'],'label'=>'In Progress',  'color'=>'var(--cyan)',   'bg'=>'rgba(0,212,255,0.12)', 'icon'=>'spinner'],
        ['val'=>$stats['pending'],    'label'=>'Pending',      'color'=>'var(--yellow)', 'bg'=>'rgba(255,204,0,0.12)', 'icon'=>'hourglass'],
        ['val'=>$stats['overdue'],    'label'=>'Overdue',      'color'=>'var(--red)',    'bg'=>'rgba(255,77,109,0.12)','icon'=>'exclamation-circle'],
        ['val'=>$stats['high'],       'label'=>'High Priority','color'=>'var(--orange)', 'bg'=>'rgba(255,140,66,0.12)','icon'=>'flag'],
    ]; @endphp
    @foreach($cols as $i => $s)
    <div class="stat-card" style="animation-delay:{{ $i*0.05 }}s">
        <div class="stat-icon" style="background:{{ $s['bg'] }}"><i class="fas fa-{{ $s['icon'] }}" style="color:{{ $s['color'] }}"></i></div>
        <div class="stat-val" style="color:{{ $s['color'] }}">{{ $s['val'] }}</div>
        <div class="stat-label">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- EXPORT --}}
<div class="report-card" style="margin-bottom:20px">
    <div class="rc-header">
        <div class="rc-title"><i class="fas fa-download" style="margin-right:8px;color:var(--accent)"></i>Export Reports</div>
    </div>
    <div class="rc-body">
        <div class="export-btns">
            <a href="{{ route('reports.csv') }}" class="btn btn-ghost">
                <i class="fas fa-file-csv" style="color:var(--green)"></i> Export CSV
            </a>
            <a href="{{ route('reports.pdf') }}" class="btn btn-ghost">
                <i class="fas fa-file-pdf" style="color:var(--red)"></i> Export PDF
            </a>
        </div>
    </div>
</div>

<div class="reports-grid">

    {{-- STATUS CHART --}}
    <div class="report-card">
        <div class="rc-header"><div class="rc-title">Tasks by Status</div></div>
        <div class="rc-body">
            <div class="chart-wrap"><canvas id="statusChart"></canvas></div>
        </div>
    </div>

    {{-- CATEGORY CHART --}}
    <div class="report-card">
        <div class="rc-header"><div class="rc-title">Tasks by Category</div></div>
        <div class="rc-body">
            <div class="chart-wrap"><canvas id="catChart"></canvas></div>
        </div>
    </div>

    {{-- PROJECT PROGRESS --}}
    <div class="report-card">
        <div class="rc-header"><div class="rc-title">Project Progress</div></div>
        <div class="rc-body">
            @if($byProject->count())
            @foreach($byProject as $proj)
            @php $pct = $proj->todos_count > 0 ? round(($proj->done_count/$proj->todos_count)*100) : 0; @endphp
            <div class="proj-row">
                <div class="proj-dot" style="background:{{ $proj->color }}"></div>
                <div class="proj-name">{{ $proj->name }}</div>
                <div class="proj-progress-wrap">
                    <div class="proj-progress-fill" style="width:{{ $pct }}%"></div>
                </div>
                <div class="proj-pct">{{ $pct }}%</div>
            </div>
            @endforeach
            @else
            <p style="color:var(--muted2);font-size:13px">No projects yet.</p>
            @endif
        </div>
    </div>

    {{-- OVERDUE --}}
    <div class="report-card">
        <div class="rc-header">
            <div class="rc-title" style="color:var(--red)"><i class="fas fa-exclamation-triangle" style="margin-right:8px"></i>Overdue Tasks</div>
            <span style="background:rgba(255,77,109,0.12);color:var(--red);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">{{ $overdue->count() }}</span>
        </div>
        <div class="rc-body">
            @if($overdue->count())
            @foreach($overdue->take(8) as $t)
            <div class="overdue-item">
                <div class="overdue-dot"></div>
                <div class="overdue-name">
                    <a href="{{ route('todos.show',$t->id) }}" style="color:var(--text);text-decoration:none">{{ Str::limit($t->title,40) }}</a>
                </div>
                <div class="overdue-date">{{ $t->due_date->format('M d') }}</div>
            </div>
            @endforeach
            @else
            <div style="text-align:center;padding:30px;color:var(--green)">
                <i class="fas fa-check-circle" style="font-size:32px;margin-bottom:10px;display:block"></i>
                <p style="font-size:13px">No overdue tasks! Great job 🎉</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@section('extra-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const isDark = document.documentElement.dataset.theme !== 'light';
const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const textColor = isDark ? '#6b6b90' : '#8080a0';

Chart.defaults.color = textColor;
Chart.defaults.borderColor = gridColor;
Chart.defaults.font.family = "'Instrument Sans', sans-serif";

// Status Doughnut
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'In Progress', 'Pending', 'Archived'],
        datasets: [{ data: [{{ $stats['completed'] }}, {{ $stats['in_progress'] }}, {{ $stats['pending'] }}, {{ \App\Models\Todo::where('status','archived')->count() }}],
            backgroundColor: ['#00e5a0','#00d4ff','#ffcc00','#5c5c7a'],
            borderWidth: 0, hoverOffset: 6
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, cutout:'70%',
        plugins: { legend: { position:'right', labels:{ boxWidth:10, padding:16 } } }
    }
});

// Category Bar
new Chart(document.getElementById('catChart'), {
    type: 'bar',
    data: {
        labels: @json($byCategory->pluck('category')->map(fn($c) => ucfirst($c))),
        datasets: [{
            label: 'Tasks',
            data: @json($byCategory->pluck('count')),
            backgroundColor: ['#6c63ff','#00d4ff','#00e5a0','#ffcc00','#ff6b9d'],
            borderRadius: 8, borderSkipped: false
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins: { legend: { display:false } },
        scales: {
            y: { beginAtZero:true, ticks:{ stepSize:1 } },
            x: { grid: { display:false } }
        }
    }
});
</script>
@endsection