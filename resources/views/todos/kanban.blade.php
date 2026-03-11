@extends('layouts.app')
@section('title','Kanban Board')

@section('extra-css')
<style>
.kanban-wrap {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    align-items: start;
    min-height: calc(100vh - 200px);
}
.kanban-col {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: border-color 0.2s;
}
.kanban-col.drag-over {
    border-color: var(--accent);
    box-shadow: 0 0 0 2px rgba(108,99,255,0.2);
}
.col-header {
    padding: 16px 16px 12px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid var(--border);
}
.col-title {
    font-family: 'Cabinet Grotesk', sans-serif;
    font-size: 13px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 1px; display: flex; align-items: center; gap: 8px;
}
.col-dot { width: 8px; height: 8px; border-radius: 50%; }
.col-count {
    background: var(--surface2); color: var(--muted2);
    font-size: 11px; font-weight: 700;
    padding: 2px 8px; border-radius: 20px;
}
.col-cards {
    padding: 12px;
    display: flex; flex-direction: column; gap: 8px;
    min-height: 200px;
}
.k-card {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 14px;
    cursor: grab;
    transition: all 0.2s;
    position: relative;
    animation: taskIn 0.4s cubic-bezier(0.16,1,0.3,1) both;
}
.k-card:hover {
    border-color: var(--border2);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    transform: translateY(-2px);
}
.k-card.dragging {
    opacity: 0.4; transform: rotate(2deg);
}
.k-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 2px; border-radius: 10px 10px 0 0;
}
.k-card.p-high::before   { background: var(--red); }
.k-card.p-medium::before { background: var(--yellow); }
.k-card.p-low::before    { background: var(--green); }

.k-title { font-size: 13px; font-weight: 600; margin-bottom: 8px; line-height: 1.4; }
.k-meta  { display: flex; gap: 5px; flex-wrap: wrap; }
.k-badge {
    padding: 2px 7px; border-radius: 20px;
    font-size: 10px; font-weight: 600;
}
.k-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 10px; padding-top: 8px;
    border-top: 1px solid var(--border);
}
.k-date { font-size: 11px; color: var(--muted2); display: flex; align-items: center; gap: 4px; }
.k-date.overdue { color: var(--red); }
.k-actions { display: flex; gap: 4px; opacity: 0; transition: opacity 0.2s; }
.k-card:hover .k-actions { opacity: 1; }

.col-empty {
    text-align: center; padding: 32px 16px;
    color: var(--muted); font-size: 13px;
}
.col-empty i { font-size: 28px; margin-bottom: 8px; display: block; opacity: 0.3; }

@media(max-width: 900px) {
    .kanban-wrap { grid-template-columns: repeat(2,1fr); }
}
@media(max-width: 540px) {
    .kanban-wrap { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
        <h2 style="font-family:'Cabinet Grotesk',sans-serif;font-size:22px;font-weight:900">Kanban Board</h2>
        <p style="color:var(--muted2);font-size:13px;margin-top:2px">Drag & drop tasks between columns</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('todos.index') }}"    class="view-btn" title="List"><i class="fas fa-list"></i></a>
        <a href="{{ route('todos.kanban') }}"   class="view-btn active" title="Kanban"><i class="fas fa-columns"></i></a>
        <a href="{{ route('todos.calendar') }}" class="view-btn" title="Calendar"><i class="fas fa-calendar"></i></a>
    </div>
</div>

<div class="kanban-wrap">
    @php
    $cols = [
        'pending'     => ['label'=>'Pending',     'dot'=>'var(--yellow)', 'icon'=>'hourglass-half'],
        'in_progress' => ['label'=>'In Progress',  'dot'=>'var(--cyan)',   'icon'=>'spinner'],
        'completed'   => ['label'=>'Completed',    'dot'=>'var(--green)',  'icon'=>'check-circle'],
        'archived'    => ['label'=>'Archived',     'dot'=>'var(--muted)',  'icon'=>'archive'],
    ];
    @endphp

    @foreach($cols as $status => $meta)
    <div class="kanban-col" data-status="{{ $status }}" ondragover="onDragOver(event)" ondrop="onDrop(event,this)">
        <div class="col-header">
            <div class="col-title">
                <div class="col-dot" style="background:{{ $meta['dot'] }}"></div>
                {{ $meta['label'] }}
            </div>
            <span class="col-count">{{ $board[$status]->count() }}</span>
        </div>
        <div class="col-cards" id="col-{{ $status }}">
            @forelse($board[$status] as $todo)
            <div class="k-card p-{{ $todo->priority }}"
                 id="kc-{{ $todo->id }}"
                 draggable="true"
                 ondragstart="onDragStart(event, {{ $todo->id }})">
                <div class="k-title">{{ $todo->title }}</div>
                <div class="k-meta">
                    <span class="k-badge chip-{{ $todo->priority }}">{{ ucfirst($todo->priority) }}</span>
                    @if($todo->project)
                    <span class="k-badge" style="background:rgba(0,212,255,0.1);color:var(--cyan)">{{ $todo->project->name }}</span>
                    @endif
                    @foreach($todo->tags->take(2) as $tag)
                    <span class="k-badge" style="background:var(--surface3);color:{{ $tag->color }}">{{ $tag->name }}</span>
                    @endforeach
                </div>
                @if($todo->due_date || $todo->subTasks->count())
                <div class="k-footer">
                    @if($todo->due_date)
                    <div class="k-date {{ $todo->isOverdue() ? 'overdue':'' }}">
                        <i class="fas fa-clock"></i>
                        {{ $todo->due_date->format('M d') }}
                        @if($todo->isOverdue()) ⚠️ @endif
                    </div>
                    @endif
                    @if($todo->subTasks->count())
                    <div class="k-date">
                        <i class="fas fa-tasks"></i>
                        {{ $todo->subTasks->where('completed',true)->count() }}/{{ $todo->subTasks->count() }}
                    </div>
                    @endif
                    <div class="k-actions">
                        <a href="{{ route('todos.show',$todo->id) }}" class="act-btn" style="width:24px;height:24px;font-size:10px"><i class="fas fa-eye"></i></a>
                        <button class="act-btn danger del-btn" data-id="{{ $todo->id }}" style="width:24px;height:24px;font-size:10px"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="col-empty">
                <i class="fas fa-{{ $meta['icon'] }}"></i>
                No {{ $meta['label'] }} tasks
            </div>
            @endforelse
        </div>
    </div>
    @endforeach
</div>

@endsection

@section('extra-js')
<script>
    const CSRF2 = document.querySelector('meta[name="csrf-token"]').content;
let dragId = null;

function onDragStart(e, id) {
    dragId = id;
    setTimeout(() => {
        document.getElementById(`kc-${id}`).classList.add('dragging');
    }, 0);
    e.dataTransfer.effectAllowed = 'move';
}

function onDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    document.querySelectorAll('.kanban-col').forEach(c => c.classList.remove('drag-over'));
    e.currentTarget.classList.add('drag-over');
}

function onDrop(e, col) {
    e.preventDefault();
    document.querySelectorAll('.kanban-col').forEach(c => c.classList.remove('drag-over'));

    const newStatus = col.dataset.status;
    const card = document.getElementById(`kc-${dragId}`);
    if (!card) return;

    // ── Empty state text hata do ──
    const colCards = col.querySelector('.col-cards');
    const emptyEl  = colCards.querySelector('.col-empty');
    if (emptyEl) emptyEl.remove();

    // ── Card move karo ──
    colCards.appendChild(card);
    card.classList.remove('dragging');

    // ── Count update karo ──
    updateColCounts();

    // ── Server pe save karo ──
// Server save
// Server save
fetch(`{{ url('/') }}/todos/${dragId}/status`, {
    method:  'PATCH',
    headers: {
        'X-CSRF-TOKEN': CSRF2,
        'Content-Type': 'application/json',
        'Accept':       'application/json',
    },
    body: JSON.stringify({ status: newStatus }),
})
.then(r => r.json())
.then(d => {
    // ── All Tasks page mein bhi card update karo ──
    const listCard = document.getElementById(`tc-${dragId}`);
    if (listCard) {
        listCard.classList.toggle('is-done', d.completed);
        const cb = listCard.querySelector('.cb');
        if (cb) {
            cb.classList.toggle('checked', d.completed);
            cb.textContent = d.completed ? '✓' : '';
        }
    }

toast(`Moved to "${newStatus.replace('_',' ')}" ✅`, 'success')
})
// NAHI
.catch(() => toast('Save failed!', 'error'));
}

function updateColCounts() {
    document.querySelectorAll('.kanban-col').forEach(col => {
        const count   = col.querySelectorAll('.k-card').length;
        const countEl = col.querySelector('.col-count');
        if (countEl) countEl.textContent = count;
    });
}
// PURANA — shayad missing ho

document.addEventListener('dragend', () => {
    document.querySelectorAll('.k-card').forEach(c => c.classList.remove('dragging'));
    document.querySelectorAll('.kanban-col').forEach(c => c.classList.remove('drag-over'));
});

// Toast function (agar layouts se nahi aa rahi)
function toast(msg, type = 'info') {
    const stack = document.getElementById('toastStack');
    if (!stack) return;
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<div class="toast-dot"></div><span>${msg}</span>`;
    stack.appendChild(t);
    setTimeout(() => { t.classList.add('out'); setTimeout(() => t.remove(), 300); }, 3000);
}
</script>
@endsection