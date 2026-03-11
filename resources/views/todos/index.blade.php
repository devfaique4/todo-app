@extends('layouts.app')
@section('title','All Tasks')

@section('content')

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card" style="animation-delay:0s">
        <div class="stat-icon" style="background:rgba(108,99,255,0.12)"><i class="fas fa-list-check" style="color:var(--accent)"></i></div>
        <div class="stat-val" style="color:var(--accent)" data-stat="total">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Tasks</div>
    </div>
    <div class="stat-card" style="animation-delay:.05s">
        <div class="stat-icon" style="background:rgba(0,229,160,0.12)"><i class="fas fa-check-circle" style="color:var(--green)"></i></div>
        <div class="stat-val" style="color:var(--green)" data-stat="completed">{{ $stats['completed'] }}</div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card" style="animation-delay:.1s">
        <div class="stat-icon" style="background:rgba(0,212,255,0.12)"><i class="fas fa-spinner" style="color:var(--cyan)"></i></div>
        <div class="stat-val" style="color:var(--cyan)" data-stat="in_progress">{{ $stats['in_progress'] }}</div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-card" style="animation-delay:.15s">
        <div class="stat-icon" style="background:rgba(255,77,109,0.12)"><i class="fas fa-clock" style="color:var(--red)"></i></div>
        <div class="stat-val" style="color:var(--red)" data-stat="overdue">{{ $stats['overdue'] }}</div>
        <div class="stat-label">Overdue</div>
    </div>
    <div class="stat-card" style="animation-delay:.2s">
        <div class="stat-icon" style="background:rgba(255,204,0,0.12)"><i class="fas fa-hourglass-half" style="color:var(--yellow)"></i></div>
        <div class="stat-val" style="color:var(--yellow)" data-stat="pending">{{ $stats['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
</div>

{{-- GLOBAL PROGRESS --}}
@php
    $pct = $stats['total'] > 0 ? round(($stats['completed']/$stats['total'])*100) : 0;
@endphp
<div class="global-progress">
    <div class="gp-header">
        <span class="gp-label"><i class="fas fa-chart-line" style="margin-right:6px"></i>Overall Completion Rate</span>
        <span class="gp-pct" id="progressPct">{{ $pct }}%</span>
    </div>
    <div class="gp-bar"><div class="gp-fill" id="progressFill" style="width:{{ $pct }}%"></div></div>
</div>

{{-- TOOLBAR --}}
<div class="toolbar">
    {{-- Status filter tabs --}}
    <div class="filter-tabs">
        <button class="ftab {{ !request('status') ? 'active' : '' }}" onclick="applyFilter('status','')">All</button>
        <button class="ftab {{ request('status')=='pending'     ? 'active' : '' }}" onclick="applyFilter('status','pending')">Pending</button>
        <button class="ftab {{ request('status')=='in_progress' ? 'active' : '' }}" onclick="applyFilter('status','in_progress')">In Progress</button>
        <button class="ftab {{ request('status')=='completed'   ? 'active' : '' }}" onclick="applyFilter('status','completed')">Done</button>
        <button class="ftab {{ request('status')=='archived'    ? 'active' : '' }}" onclick="applyFilter('status','archived')">Archived</button>
    </div>

    {{-- Priority --}}
    <select class="select-filter" onchange="applyFilter('priority',this.value)">
        <option value="">All Priorities</option>
        <option value="high"   {{ request('priority')=='high'   ? 'selected':'' }}>🔴 High</option>
        <option value="medium" {{ request('priority')=='medium' ? 'selected':'' }}>🟡 Medium</option>
        <option value="low"    {{ request('priority')=='low'    ? 'selected':'' }}>🟢 Low</option>
    </select>

    {{-- Project --}}
    <select class="select-filter" onchange="applyFilter('project',this.value)">
        <option value="">All Projects</option>
        @foreach($projects as $p)
        <option value="{{ $p->id }}" {{ request('project')==$p->id ? 'selected':'' }}>{{ $p->name }}</option>
        @endforeach
    </select>

    {{-- Tag --}}
    <select class="select-filter" onchange="applyFilter('tag',this.value)">
        <option value="">All Tags</option>
        @foreach($tags as $t)
        <option value="{{ $t->id }}" {{ request('tag')==$t->id ? 'selected':'' }}>{{ $t->name }}</option>
        @endforeach
    </select>

    {{-- Overdue --}}
    <button class="select-filter {{ request('overdue') ? 'active' : '' }}" style="cursor:pointer" onclick="applyFilter('overdue', '{{ request('overdue') ? '' : '1' }}')">
        ⚠️ Overdue Only
    </button>

    {{-- View switcher --}}
    <div class="view-btns">
        <a href="{{ route('todos.index') }}"    class="view-btn active" title="List"><i class="fas fa-list"></i></a>
        <a href="{{ route('todos.kanban') }}"   class="view-btn" title="Kanban"><i class="fas fa-columns"></i></a>
        <a href="{{ route('todos.calendar') }}" class="view-btn" title="Calendar"><i class="fas fa-calendar"></i></a>
    </div>
</div>

{{-- TASK LIST --}}
<div class="tasks-grid" id="taskList">
    @forelse($todos as $todo)
    <div class="task-card p-{{ $todo->priority }} {{ $todo->completed ? 'is-done' : '' }}" id="tc-{{ $todo->id }}">

        {{-- Checkbox --}}
        <button class="cb {{ $todo->completed ? 'checked' : '' }}" data-id="{{ $todo->id }}">
            {{ $todo->completed ? '✓' : '' }}
        </button>

        {{-- Body --}}
        <div class="task-body">
            <div class="task-title">{{ $todo->title }}</div>
            @if($todo->description)
            <div class="task-desc">{{ $todo->description }}</div>
            @endif
            <div class="task-meta">
                <span class="chip chip-{{ $todo->priority }}">{{ ucfirst($todo->priority) }}</span>
                <span class="chip chip-status">{{ str_replace('_',' ',ucfirst($todo->status)) }}</span>
                @if($todo->project)
                <span class="chip chip-cat">
                    <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:{{ $todo->project->color }};margin-right:4px"></span>
                    {{ $todo->project->name }}
                </span>
                @endif
                @foreach($todo->tags as $tag)
                <span class="chip chip-tag" style="color:{{ $tag->color }}">{{ $tag->name }}</span>
                @endforeach
                @if($todo->due_date)
                <span class="chip chip-date {{ $todo->isOverdue() ? 'overdue' : '' }}">
                    <i class="fas fa-clock" style="margin-right:3px"></i>
                    {{ $todo->due_date->format('M d, Y') }}
                    @if($todo->isOverdue()) • Overdue! @endif
                    @if($todo->isDueSoon() && !$todo->isOverdue()) • Due Today! @endif
                </span>
                @endif
                @if($todo->recurring !== 'none')
                <span class="chip" style="background:rgba(167,139,250,0.12);color:#a78bfa">
                    <i class="fas fa-redo" style="margin-right:3px"></i>{{ ucfirst($todo->recurring) }}
                </span>
                @endif
            </div>

            {{-- Subtask progress --}}
            @if($todo->subTasks->count())
            <div class="subtask-bar">
                <div class="subtask-bar-label">
                    <span>Subtasks</span>
                    <span>{{ $todo->subTasks->where('completed',true)->count() }}/{{ $todo->subTasks->count() }}</span>
                </div>
                <div class="subtask-track">
                    <div class="subtask-fill" style="width:{{ $todo->subTaskProgress() }}%"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right side --}}
        <div class="task-right">
            <div class="task-actions">
                <a href="{{ route('todos.show', $todo->id) }}" class="act-btn" title="View"><i class="fas fa-eye"></i></a>
                <button class="act-btn edit-act" title="Edit" onclick="openEditModal({{ $todo->id }}, '{{ addslashes($todo->title) }}', '{{ $todo->status }}', '{{ $todo->priority }}', '{{ $todo->category }}', '{{ $todo->project_id }}', '{{ $todo->due_date?->format('Y-m-d') }}', '{{ $todo->recurring }}', '{{ addslashes($todo->description) }}')">
                    <i class="fas fa-pen"></i>
                </button>
                <button class="act-btn danger del-btn" title="Delete" data-id="{{ $todo->id }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            @if($todo->attachments->count())
            <div class="attachment-count">
                <i class="fas fa-paperclip"></i> {{ $todo->attachments->count() }}
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-icon">🚀</div>
        <div class="empty-title">No tasks found</div>
        <div class="empty-sub">Add your first task using the "New Task" button above</div>
    </div>
    @endforelse
</div>

{{-- PAGINATION --}}
<div class="pagination-wrap">
   {{ $todos->appends(request()->query())->links() }}
</div>

{{-- EDIT MODAL --}}
<div class="overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">✏️ Edit Task</div>
            <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" id="eTitle" class="finput" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="eDesc" class="finput"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select name="priority" id="ePriority" class="finput">
                            <option value="high">🔴 High</option>
                            <option value="medium">🟡 Medium</option>
                            <option value="low">🟢 Low</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="eStatus" class="finput">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Project</label>
                        <select name="project_id" id="eProject" class="finput">
                            <option value="">No Project</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" id="eCategory" class="finput">
                            <option value="general">General</option>
                            <option value="work">Work</option>
                            <option value="personal">Personal</option>
                            <option value="health">Health</option>
                            <option value="shopping">Shopping</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" id="eDueDate" class="finput">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Recurring</label>
                        <select name="recurring" id="eRecurring" class="finput">
                            <option value="none">Not Recurring</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn btn-accent">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script>
// ── FILTERS ──
function applyFilter(key, val) {
    const url = new URL(window.location);
    if (val) url.searchParams.set(key, val);
    else url.searchParams.delete(key);
    window.location = url;
}

// ── EDIT MODAL ──
function openEditModal(id, title, status, priority, category, projectId, dueDate, recurring, desc) {
    document.getElementById('editForm').action = `/todos/${id}`;
    document.getElementById('eTitle').value    = title;
    document.getElementById('eDesc').value     = desc;
    document.getElementById('eStatus').value   = status;
    document.getElementById('ePriority').value = priority;
    document.getElementById('eCategory').value = category;
    document.getElementById('eProject').value  = projectId || '';
    document.getElementById('eDueDate').value  = dueDate || '';
    document.getElementById('eRecurring').value= recurring;
    openModal('editModal');
}
</script>
@endsection