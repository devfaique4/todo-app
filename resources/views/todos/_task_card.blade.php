<div class="task-card {{ $todo->completed ? 'done' : '' }} p-{{ $todo->priority }}" id="task-{{ $todo->id }}">
    <!-- Checkbox -->
    <button class="check-btn {{ $todo->completed ? 'checked' : '' }}" data-id="{{ $todo->id }}"></button>

    <!-- Content -->
    <div class="task-content">
        <div class="task-title">{{ $todo->title }}</div>
        @if($todo->description)
        <div class="task-desc">{{ $todo->description }}</div>
        @endif
        <div class="task-meta">
            <span class="badge badge-{{ $todo->priority }}">
                {{ ucfirst($todo->priority) }}
            </span>
            <span class="badge badge-cat">{{ ucfirst($todo->category) }}</span>
            @if($todo->due_date)
            <span class="badge badge-date">
                <i class="fas fa-clock" style="margin-right:4px"></i>
                {{ \Carbon\Carbon::parse($todo->due_date)->format('M d, Y') }}
                @if(!$todo->completed && \Carbon\Carbon::parse($todo->due_date)->isPast())
                    <span style="color:#fa4d6d; margin-left:4px">Overdue!</span>
                @endif
            </span>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="task-actions">
        <button class="action-btn edit-btn" onclick="openEdit(
            {{ $todo->id }},
            '{{ addslashes($todo->title) }}',
            '{{ $todo->category }}',
            '{{ $todo->priority }}',
            '{{ $todo->due_date }}',
            '{{ addslashes($todo->description) }}'
        )">
            <i class="fas fa-pen"></i>
        </button>
        <button class="action-btn delete" data-id="{{ $todo->id }}">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>