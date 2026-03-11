@extends('layouts.app')
@section('title','Notifications')

@section('content')
<div style="max-width:680px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <h2 style="font-family:'Cabinet Grotesk',sans-serif;font-size:22px;font-weight:900">Notifications</h2>
        @if(\App\Models\Notification::where('read',false)->count())
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-ghost" style="font-size:13px">
                <i class="fas fa-check-double"></i> Mark All Read
            </button>
        </form>
        @endif
    </div>

    @php $notifs = \App\Models\Notification::latest()->paginate(20); @endphp

    @forelse($notifs as $n)
    <div style="background:var(--surface);border:1px solid {{ $n->read ? 'var(--border)' : 'var(--accent)' }};border-radius:var(--radius);padding:18px 20px;margin-bottom:10px;display:flex;gap:14px;align-items:flex-start;transition:all 0.2s;{{ !$n->read ? 'box-shadow:0 0 0 1px rgba(108,99,255,0.15)' : '' }}">
        <div style="width:38px;height:38px;border-radius:10px;background:{{ match($n->type){ 'due_soon'=>'rgba(255,204,0,0.12)','overdue'=>'rgba(255,77,109,0.12)',default=>'rgba(108,99,255,0.12)' } }};display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">
            {{ match($n->type){ 'due_soon'=>'⏰','overdue'=>'⚠️','completed'=>'✅',default=>'🔔' } }}
        </div>
        <div style="flex:1">
            <div style="font-size:14px;font-weight:600;margin-bottom:3px">{{ $n->title }}</div>
            <div style="font-size:13px;color:var(--muted2)">{{ $n->message }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:6px">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        @if(!$n->read)
        <form action="{{ route('notifications.read', $n->id) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" style="background:transparent;border:none;color:var(--accent);cursor:pointer;font-size:12px;font-family:inherit;white-space:nowrap">Mark Read</button>
        </form>
        @else
        <div style="width:7px;height:7px;border-radius:50%;background:var(--muted);flex-shrink:0;margin-top:6px"></div>
        @endif
    </div>
    @empty
    <div style="text-align:center;padding:60px;color:var(--muted2)">
        <div style="font-size:48px;margin-bottom:16px">🔕</div>
        <p>No notifications yet</p>
    </div>
    @endforelse

    <div class="pagination-wrap">{{ $notifs->links() }}</div>
</div>
@endsection