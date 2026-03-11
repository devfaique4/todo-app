@extends('layouts.app')
@section('title','Calendar View')

@section('extra-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
<style>
.calendar-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
}
/* Override FullCalendar for dark theme */
.fc-toolbar h2 {
    font-family: 'Cabinet Grotesk', sans-serif !important;
    font-size: 20px !important; font-weight: 900 !important;
    color: var(--text) !important;
}
.fc-toolbar .fc-button {
    background: var(--surface2) !important;
    border: 1px solid var(--border2) !important;
    color: var(--text) !important;
    border-radius: 8px !important;
    font-family: inherit !important;
    text-shadow: none !important;
    box-shadow: none !important;
}
.fc-toolbar .fc-button:hover { background: var(--surface3) !important; }
.fc-toolbar .fc-button-active { background: var(--accent) !important; border-color: var(--accent) !important; }
.fc-head-container, .fc td, .fc th { border-color: var(--border) !important; }
.fc-day-header { color: var(--muted2) !important; font-size: 12px !important; font-weight: 600 !important; text-transform: uppercase !important; letter-spacing: 0.5px !important; padding: 8px 0 !important; }
.fc-day-number { color: var(--muted2) !important; font-size: 13px !important; }
.fc-day.fc-today { background: rgba(108,99,255,0.06) !important; }
.fc-day.fc-today .fc-day-number { color: var(--accent) !important; font-weight: 700 !important; }
.fc-event { border-radius: 6px !important; border: none !important; font-size: 11px !important; font-weight: 600 !important; padding: 2px 6px !important; }
.fc-bg { background: var(--bg) !important; }
.fc-widget-content, .fc-widget-header { background: transparent !important; }
</style>
@endsection

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
        <h2 style="font-family:'Cabinet Grotesk',sans-serif;font-size:22px;font-weight:900">Calendar View</h2>
        <p style="color:var(--muted2);font-size:13px;margin-top:2px">Tasks organized by due date</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('todos.index') }}"    class="view-btn">      <i class="fas fa-list"></i></a>
        <a href="{{ route('todos.kanban') }}"   class="view-btn">      <i class="fas fa-columns"></i></a>
        <a href="{{ route('todos.calendar') }}" class="view-btn active"><i class="fas fa-calendar"></i></a>
    </div>
</div>

<div class="calendar-wrap">
    <div id="calendar"></div>
</div>
@endsection

@section('extra-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<script>
$(document).ready(function() {
    const events = @json($events);
    $('#calendar').fullCalendar({
        header: { left:'prev,next today', center:'title', right:'month,agendaWeek,agendaDay' },
        events: events,
        eventClick: function(event) { if (event.url) { window.location = event.url; return false; } },
        dayClick: function(date) {
            // Pre-fill due date in modal
            document.querySelector('#addModal input[name="due_date"]').value = date.format('YYYY-MM-DD');
            openModal('addModal');
        },
        eventRender: function(event, el) {
            el.attr('title', event.title);
        },
        height: 'auto',
        editable: false,
        eventLimit: true,
    });
});
</script>
@endsection