@extends('layouts.app')
@section('title','Projects')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 style="font-family:'Cabinet Grotesk',sans-serif;font-size:22px;font-weight:900">Projects</h2>
    <button class="btn-new" onclick="openModal('newProjectModal')">
        <i class="fas fa-plus"></i> New Project
    </button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
    @forelse($projects as $proj)
    @php
        $totalTodos = $proj->todos()->count();
        $doneTodos  = $proj->todos()->where('completed', true)->count();
        $pct        = $totalTodos > 0 ? round(($doneTodos / $totalTodos) * 100) : 0;
    @endphp

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:22px;position:relative;overflow:hidden;transition:all 0.25s"
         onmouseover="this.style.transform='translateY(-3px)';this.style.borderColor='var(--border2)'"
         onmouseout="this.style.transform='';this.style.borderColor='var(--border)'">

        <div style="position:absolute;top:0;left:0;right:0;height:3px;background:{{ $proj->color }}"></div>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
            <div style="width:42px;height:42px;border-radius:12px;background:{{ $proj->color }}22;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
                <i class="fas fa-{{ $proj->icon }}" style="color:{{ $proj->color }}"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-family:'Cabinet Grotesk',sans-serif;font-size:16px;font-weight:800">{{ $proj->name }}</div>
                <div style="font-size:12px;color:var(--muted2)">{{ $totalTodos }} tasks</div>
            </div>
            <form action="{{ route('projects.destroy', $proj->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit"
                    onclick="return confirm('Delete this project?')"
                    style="background:transparent;border:none;color:var(--muted);cursor:pointer;font-size:13px">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>

        @if($proj->description)
        <p style="font-size:13px;color:var(--muted2);margin-bottom:14px;line-height:1.5">
            {{ \Illuminate\Support\Str::limit($proj->description, 80) }}
        </p>
        @endif

        <div style="margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted2);margin-bottom:6px">
                <span>Progress</span>
                <span style="color:{{ $proj->color }};font-weight:700">{{ $pct }}%</span>
            </div>
            <div style="height:5px;background:var(--surface3);border-radius:99px;overflow:hidden">
                <div style="width:{{ $pct }}%;height:100%;background:{{ $proj->color }};border-radius:99px;transition:width 0.8s"></div>
            </div>
        </div>

        <a href="{{ route('todos.index', ['project' => $proj->id]) }}"
           style="display:block;text-align:center;padding:9px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;color:var(--text);text-decoration:none;font-size:13px;font-weight:500">
            View Tasks →
        </a>
    </div>

    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--muted2)">
        <div style="font-size:56px;margin-bottom:16px">📁</div>
        <div style="font-family:'Cabinet Grotesk',sans-serif;font-size:20px;font-weight:800;margin-bottom:8px">No Projects Yet</div>
        <p style="font-size:14px">Create your first project!</p>
    </div>
    @endforelse
</div>

{{-- MODAL --}}
<div class="overlay" id="newProjectModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">📁 New Project</div>
            <button class="modal-close" onclick="closeModal('newProjectModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Project Name *</label>
                    <input type="text" name="name" class="finput" required placeholder="e.g. Website Redesign">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="finput" placeholder="What is this project about?"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Color</label>
                        <input type="color" name="color" value="#6c63ff" class="finput" style="height:44px;cursor:pointer;padding:4px">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Icon</label>
                        <select name="icon" class="finput">
                            <option value="folder">Folder</option>
                            <option value="briefcase">Work</option>
                            <option value="home">Home</option>
                            <option value="heart">Health</option>
                            <option value="star">Star</option>
                            <option value="code">Code</option>
                            <option value="book">Study</option>
                            <option value="shopping-bag">Shopping</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('newProjectModal')">Cancel</button>
                    <button type="submit" class="btn btn-accent"><i class="fas fa-plus"></i> Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection