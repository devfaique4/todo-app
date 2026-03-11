@extends('layouts.app')
@section('title', $todo->title)

@section('extra-css')
<style>
.detail-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: start; }
.detail-main { display: flex; flex-direction: column; gap: 20px; }
.detail-side { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 80px; }
.detail-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
.dc-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.dc-title { font-family: 'Cabinet Grotesk', sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--muted2); }
.dc-body { padding: 20px; }

/* Sub-tasks */
.sub-list { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.sub-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 8px;
    background: var(--surface2); border: 1px solid var(--border);
    transition: all 0.2s;
}
.sub-item:hover { border-color: var(--border2); }
.sub-cb { width: 18px; height: 18px; border-radius: 50%; border: 2px solid var(--border2); background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 10px; transition: all 0.2s; flex-shrink: 0; }
.sub-cb:hover { border-color: var(--accent); }
.sub-cb.done { background: var(--green); border-color: var(--green); color: #000; font-weight: 900; }
.sub-text { flex: 1; font-size: 13px; transition: all 0.2s; }
.sub-text.done { text-decoration: line-through; color: var(--muted); }
.sub-del { color: var(--muted); font-size: 11px; cursor: pointer; padding: 4px; transition: color 0.2s; }
.sub-del:hover { color: var(--red); }

.sub-add-row { display: flex; gap: 8px; }
.sub-input { flex: 1; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px; color: var(--text); font-family: inherit; font-size: 13px; outline: none; transition: border-color 0.2s; }
.sub-input:focus { border-color: var(--accent); }
.sub-add-btn { padding: 9px 16px; background: var(--accent-g); color: #fff; border: none; border-radius: 8px; font-family: inherit; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.sub-add-btn:hover { transform: translateY(-1px); }

/* Comments */
.comment-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
.comment-item { padding: 14px; background: var(--surface2); border-radius: 10px; border: 1px solid var(--border); }
.comment-meta { display: flex; justify-content: space-between; margin-bottom: 6px; }
.comment-date { font-size: 11px; color: var(--muted2); }
.comment-del  { font-size: 11px; color: var(--muted); cursor: pointer; }
.comment-del:hover { color: var(--red); }
.comment-body { font-size: 13px; line-height: 1.6; }

.comment-form textarea { width: 100%; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px; padding: 12px; color: var(--text); font-family: inherit; font-size: 13px; outline: none; resize: vertical; min-height: 80px; transition: border-color 0.2s; }
.comment-form textarea:focus { border-color: var(--accent); }

/* Attachments */
.attach-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
.attach-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: var(--surface2); border-radius: 8px; border: 1px solid var(--border); }
.attach-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(108,99,255,0.12); display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 13px; flex-shrink: 0; }
.attach-info { flex: 1; min-width: 0; }
.attach-name { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.attach-size { font-size: 11px; color: var(--muted2); }
.attach-actions { display: flex; gap: 4px; }

/* History */
.history-list { display: flex; flex-direction: column; gap: 0; }
.history-item { padding: 10px 0; border-bottom: 1px solid var(--border); display: flex; gap: 10px; align-items: flex-start; font-size: 12px; }
.history-item:last-child { border-bottom: none; }
.h-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); margin-top: 3px; flex-shrink: 0; }
.h-body { flex: 1; }
.h-action { font-weight: 600; text-transform: capitalize; }
.h-detail { color: var(--muted2); margin-top: 2px; }
.h-time { color: var(--muted); font-size: 10px; }

/* Info pills */
.info-pill { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
.info-pill:last-child { border-bottom: none; }
.info-pill-label { color: var(--muted2); font-size: 12px; font-weight: 500; }

@media(max-width: 900px) {
    .detail-grid { grid-template-columns: 1fr; }
    .detail-side { position: static; }
}
</style>
@endsection

@section('content')

{{-- BREADCRUMB --}}
<div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;font-size:13px;color:var(--muted2)">
    <a href="{{ route('todos.index') }}" style="color:var(--accent);text-decoration:none">All Tasks</a>
    <i class="fas fa-chevron-right" style="font-size:10px"></i>
    <span>{{ Str::limit($todo->title, 50) }}</span>
</div>

<div class="detail-grid">

    {{-- ── MAIN COLUMN ── --}}
    <div class="detail-main">

        {{-- Task Header --}}
        <div class="detail-card">
            <div class="dc-body">
                <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px">
                    <button class="cb {{ $todo->completed ? 'checked':'' }}" data-id="{{ $todo->id }}" style="width:26px;height:26px;margin-top:3px">
                        {{ $todo->completed ? '✓' : '' }}
                    </button>
                    <div style="flex:1">
                        <h1 style="font-family:'Cabinet Grotesk',sans-serif;font-size:24px;font-weight:900;line-height:1.3;{{ $todo->completed ? 'text-decoration:line-through;color:var(--muted2)':'' }}">
                            {{ $todo->title }}
                        </h1>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px">
                            <span class="chip chip-{{ $todo->priority }}">{{ ucfirst($todo->priority) }} Priority</span>
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
                        </div>
                    </div>
                </div>
                @if($todo->description)
                <div style="color:var(--muted2);font-size:14px;line-height:1.7;padding:16px;background:var(--surface2);border-radius:10px">
                    {!! nl2br(e($todo->description)) !!}
                </div>
                @endif
            </div>
        </div>

        {{-- SUB-TASKS --}}
        <div class="detail-card">
            <div class="dc-header">
                <div class="dc-title"><i class="fas fa-tasks" style="margin-right:7px"></i>Sub-tasks
                    @if($todo->subTasks->count())
                    <span style="font-size:11px;color:var(--accent);margin-left:6px">{{ $todo->subTaskProgress() }}%</span>
                    @endif
                </div>
            </div>
            <div class="dc-body">
                @if($todo->subTasks->count())
                <div class="subtask-bar" style="margin-bottom:16px">
                    <div class="subtask-track">
                        <div class="subtask-fill" id="subFill" style="width:{{ $todo->subTaskProgress() }}%"></div>
                    </div>
                </div>
                @endif
                <div class="sub-list" id="subList">
                    @foreach($todo->subTasks as $sub)
                    <div class="sub-item" id="sub-{{ $sub->id }}">
                        <button class="sub-cb {{ $sub->completed ? 'done':'' }}" onclick="toggleSub({{ $todo->id }}, {{ $sub->id }}, this)">
                            {{ $sub->completed ? '✓' : '' }}
                        </button>
                        <span class="sub-text {{ $sub->completed ? 'done':'' }}">{{ $sub->title }}</span>
                        <i class="fas fa-times sub-del" onclick="deleteSub({{ $todo->id }}, {{ $sub->id }})"></i>
                    </div>
                    @endforeach
                </div>
                <div class="sub-add-row">
                    <input type="text" class="sub-input" id="subInput" placeholder="Add a sub-task…">
                    <button class="sub-add-btn" onclick="addSub({{ $todo->id }})"><i class="fas fa-plus"></i> Add</button>
                </div>
            </div>
        </div>

        {{-- COMMENTS --}}
        <div class="detail-card">
            <div class="dc-header">
                <div class="dc-title"><i class="fas fa-comments" style="margin-right:7px"></i>Comments ({{ $todo->comments->count() }})</div>
            </div>
            <div class="dc-body">
                @if($todo->comments->count())
                <div class="comment-list">
                    @foreach($todo->comments as $comment)
                    <div class="comment-item" id="cm-{{ $comment->id }}">
                        <div class="comment-meta">
                            <span class="comment-date"><i class="fas fa-clock" style="margin-right:4px"></i>{{ $comment->created_at->diffForHumans() }}</span>
                            <span class="comment-del" onclick="deleteComment({{ $comment->id }})"><i class="fas fa-trash"></i></span>
                        </div>
                        <div class="comment-body">{{ $comment->body }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="comment-form">
                    <textarea id="commentInput" placeholder="Add a note or comment…"></textarea>
                    <button class="btn btn-accent" style="margin-top:10px" onclick="addComment({{ $todo->id }})">
                        <i class="fas fa-paper-plane"></i> Post Comment
                    </button>
                </div>
            </div>
        </div>

        {{-- ATTACHMENTS --}}
        <div class="detail-card">
            <div class="dc-header">
                <div class="dc-title"><i class="fas fa-paperclip" style="margin-right:7px"></i>Attachments ({{ $todo->attachments->count() }})</div>
            </div>
            <div class="dc-body">
                @if($todo->attachments->count())
                <div class="attach-list">
                    @foreach($todo->attachments as $att)
                    <div class="attach-item" id="att-{{ $att->id }}">
                        <div class="attach-icon">
                            @if(str_contains($att->mime_type,'image'))<i class="fas fa-image"></i>
                            @elseif(str_contains($att->mime_type,'pdf'))<i class="fas fa-file-pdf"></i>
                            @else<i class="fas fa-file"></i>@endif
                        </div>
                        <div class="attach-info">
                            <div class="attach-name">{{ $att->original_name }}</div>
                            <div class="attach-size">{{ $att->sizeFormatted() }}</div>
                        </div>
                        <div class="attach-actions">
                            <a href="{{ route('attachments.download',$att->id) }}" class="act-btn" title="Download"><i class="fas fa-download"></i></a>
                            <button class="act-btn danger" onclick="deleteAttachment({{ $att->id }})" title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <form action="{{ route('attachments.store', $todo->id) }}" method="POST" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:center">
                    @csrf
                    <input type="file" name="file" class="finput" style="flex:1" accept="*/*">
                    <button type="submit" class="btn btn-accent"><i class="fas fa-upload"></i> Upload</button>
                </form>
            </div>
        </div>

        {{-- TASK HISTORY --}}
        <div class="detail-card">
            <div class="dc-header">
                <div class="dc-title"><i class="fas fa-history" style="margin-right:7px"></i>Activity History</div>
            </div>
            <div class="dc-body">
                @if($todo->history->count())
                <div class="history-list">
                    @foreach($todo->history as $h)
                    <div class="history-item">
                        <div class="h-dot"></div>
                        <div class="h-body">
                            <div class="h-action">{{ $h->action }} — <span style="color:var(--text)">{{ $h->field_changed }}</span></div>
                            @if($h->old_value && $h->new_value)
                            <div class="h-detail">
                                <span style="color:var(--red)">{{ $h->old_value }}</span>
                                → <span style="color:var(--green)">{{ $h->new_value }}</span>
                            </div>
                            @endif
                            <div class="h-time">{{ $h->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p style="color:var(--muted2);font-size:13px">No history yet.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- ── SIDEBAR ── --}}
    <div class="detail-side">

        {{-- Task Info --}}
        <div class="detail-card">
            <div class="dc-header"><div class="dc-title">Task Info</div></div>
            <div class="dc-body" style="padding: 16px 20px">
                <div class="info-pill">
                    <span class="info-pill-label">Status</span>
                    <span class="chip chip-status">{{ str_replace('_',' ',ucfirst($todo->status)) }}</span>
                </div>
                <div class="info-pill">
                    <span class="info-pill-label">Priority</span>
                    <span class="chip chip-{{ $todo->priority }}">{{ ucfirst($todo->priority) }}</span>
                </div>
                <div class="info-pill">
                    <span class="info-pill-label">Category</span>
                    <span style="font-size:13px">{{ ucfirst($todo->category) }}</span>
                </div>
                @if($todo->due_date)
                <div class="info-pill">
                    <span class="info-pill-label">Due Date</span>
                    <span class="chip chip-date {{ $todo->isOverdue() ? 'overdue':'' }}">
                        {{ $todo->due_date->format('M d, Y') }}
                    </span>
                </div>
                @endif
                @if($todo->recurring !== 'none')
                <div class="info-pill">
                    <span class="info-pill-label">Recurring</span>
                    <span style="font-size:13px;color:var(--accent)"><i class="fas fa-redo" style="margin-right:4px"></i>{{ ucfirst($todo->recurring) }}</span>
                </div>
                @endif
                <div class="info-pill">
                    <span class="info-pill-label">Created</span>
                    <span style="font-size:12px;color:var(--muted2)">{{ $todo->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="detail-card">
            <div class="dc-header"><div class="dc-title">Quick Actions</div></div>
            <div class="dc-body" style="display:flex;flex-direction:column;gap:8px">
                <button class="btn btn-accent" style="width:100%;justify-content:center" onclick="openModal('addModal')">
                    <i class="fas fa-plus"></i> New Task
                </button>
                <a href="{{ route('todos.index') }}" class="btn btn-ghost" style="justify-content:center;text-decoration:none">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <button class="btn" style="background:rgba(255,77,109,0.1);color:var(--red);border:1px solid rgba(255,77,109,0.2);justify-content:center" onclick="deleteTask({{ $todo->id }})">
                    <i class="fas fa-trash"></i> Delete Task
                </button>
            </div>
        </div>

    </div>
</div>

@endsection

@section('extra-js')
<script>
// Sub-tasks
async function addSub(todoId) {
    const input = document.getElementById('subInput');
    const title = input.value.trim();
    if (!title) return;
    const r = await fetch(`/todos/${todoId}/subtasks`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({title})
    });
    if (r.ok) {
        const sub = await r.json();
        const list = document.getElementById('subList');
        list.insertAdjacentHTML('beforeend', `
            <div class="sub-item" id="sub-${sub.id}">
                <button class="sub-cb" onclick="toggleSub(${todoId},${sub.id},this)"></button>
                <span class="sub-text">${sub.title}</span>
                <i class="fas fa-times sub-del" onclick="deleteSub(${todoId},${sub.id})"></i>
            </div>
        `);
        input.value = '';
        toast('Sub-task added', 'success');
    }
}

async function toggleSub(todoId, subId, btn) {
    const r = await fetch(`/todos/${todoId}/subtasks/${subId}`, {
        method:'PATCH', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
    });
    if (r.ok) {
        const d = await r.json();
        btn.classList.toggle('done', d.completed);
        btn.textContent = d.completed ? '✓' : '';
        const text = btn.nextElementSibling;
        text.classList.toggle('done', d.completed);
        const fill = document.getElementById('subFill');
        if (fill) fill.style.width = d.progress + '%';
    }
}

async function deleteSub(todoId, subId) {
    const r = await fetch(`/todos/${todoId}/subtasks/${subId}`, {
        method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
    });
    if (r.ok) {
        document.getElementById(`sub-${subId}`)?.remove();
        toast('Sub-task removed', 'error');
    }
}

// Comments
async function addComment(todoId) {
    const ta = document.getElementById('commentInput');
    const body = ta.value.trim();
    if (!body) return;
    const r = await fetch(`/todos/${todoId}/comments`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({body})
    });
    if (r.ok) { ta.value = ''; toast('Comment added','success'); location.reload(); }
}

async function deleteComment(id) {
    const r = await fetch(`/comments/${id}`, {
        method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
    });
    if (r.ok) { document.getElementById(`cm-${id}`)?.remove(); toast('Comment deleted','error'); }
}

// Attachments
async function deleteAttachment(id) {
    if (!confirm('Delete this attachment?')) return;
    const r = await fetch(`/attachments/${id}`, {
        method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
    });
    if (r.ok) { document.getElementById(`att-${id}`)?.remove(); toast('File deleted','error'); }
}

// Delete Task
async function deleteTask(id) {
    if (!confirm('Delete this task permanently?')) return;
    const r = await fetch(`/todos/${id}`, {
        method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
    });
    if (r.ok) { window.location = '{{ route("todos.index") }}'; }
}
</script>
@endsection