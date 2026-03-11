<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%236c63ff'/><text y='72' x='50' text-anchor='middle' font-size='60'>⚡</text></svg>">
    <title>TaskFlow Pro — @yield('title','Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;600;700;800;900&family=Instrument+Sans:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    /* ════════════════════════════════════════
       DESIGN SYSTEM
    ════════════════════════════════════════ */
    :root {
        --bg:        #080810;
        --surface:   #0f0f1a;
        --surface2:  #181825;
        --surface3:  #222235;
        --border:    rgba(255,255,255,0.06);
        --border2:   rgba(255,255,255,0.12);
        --text:      #e2e2f0;
        --muted:     #5c5c7a;
        --muted2:    #8888aa;
        --accent:    #6c63ff;
        --accent-g:  linear-gradient(135deg,#6c63ff,#a78bfa);
        --pink:      #ff6b9d;
        --cyan:      #00d4ff;
        --green:     #00e5a0;
        --yellow:    #ffcc00;
        --red:       #ff4d6d;
        --orange:    #ff8c42;
        --sidebar-w: 260px;
        --radius:    14px;
    }
    [data-theme="light"] {
        --bg:       #f0f0f8;
        --surface:  #ffffff;
        --surface2: #f5f5fd;
        --surface3: #ebebf8;
        --border:   rgba(0,0,0,0.07);
        --border2:  rgba(0,0,0,0.14);
        --text:     #1a1a2e;
        --muted:    #9090b0;
        --muted2:   #6060a0;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
        font-family: 'Instrument Sans', sans-serif;
        background: var(--bg); color: var(--text);
        min-height: 100vh; overflow-x: hidden;
        display: flex;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        width: var(--sidebar-w); min-height: 100vh;
        background: var(--surface);
        border-right: 1px solid var(--border);
        display: flex; flex-direction: column;
        position: fixed; top: 0; left: 0; z-index: 50;
        transition: transform 0.3s cubic-bezier(0.16,1,0.3,1);
    }
    .sidebar-logo {
        padding: 24px 20px 20px;
        display: flex; align-items: center; gap: 12px;
        border-bottom: 1px solid var(--border);
    }
    .logo-mark {
        width: 38px; height: 38px; border-radius: 10px;
        background: var(--accent-g);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #fff;
        box-shadow: 0 0 24px rgba(108,99,255,0.4);
        animation: pulse-glow 3s ease-in-out infinite;
    }
    @keyframes pulse-glow {
        0%,100% { box-shadow: 0 0 24px rgba(108,99,255,0.4); }
        50%      { box-shadow: 0 0 40px rgba(108,99,255,0.7); }
    }
    .logo-name {
        font-family: 'Cabinet Grotesk', sans-serif;
        font-size: 18px; font-weight: 800; letter-spacing: -0.5px;
        background: var(--accent-g);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .logo-badge {
        font-size: 9px; font-weight: 700; letter-spacing: 1px;
        background: var(--accent); color: #fff;
        padding: 2px 6px; border-radius: 4px; margin-left: 2px;
        -webkit-text-fill-color: #fff;
    }

    .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
    .nav-section-label {
        font-size: 10px; font-weight: 700; letter-spacing: 1.5px;
        color: var(--muted); text-transform: uppercase;
        padding: 12px 8px 6px;
    }
    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px; border-radius: 10px;
        color: var(--muted2); font-size: 14px; font-weight: 500;
        text-decoration: none; cursor: pointer;
        transition: all 0.2s; margin-bottom: 2px; position: relative;
    }
    .nav-item:hover { background: var(--surface2); color: var(--text); }
    .nav-item.active {
        background: rgba(108,99,255,0.12);
        color: var(--accent);
    }
    .nav-item.active::before {
        content: ''; position: absolute; left: 0; top: 20%; bottom: 20%;
        width: 3px; background: var(--accent); border-radius: 0 3px 3px 0;
    }
    .nav-item .icon { width: 18px; text-align: center; font-size: 15px; }
    .nav-badge {
        margin-left: auto; background: var(--red);
        color: #fff; font-size: 11px; font-weight: 700;
        padding: 1px 7px; border-radius: 20px;
    }

    /* Project dots in sidebar */
    .project-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* Sidebar footer */
    .sidebar-footer {
        padding: 16px 12px;
        border-top: 1px solid var(--border);
    }
    .theme-toggle {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 12px; border-radius: 10px;
        background: var(--surface2); cursor: pointer;
        font-size: 13px; color: var(--muted2);
    }
    .toggle-pill {
        width: 36px; height: 20px; border-radius: 10px;
        background: var(--surface3); position: relative;
        transition: background 0.3s;
    }
    .toggle-pill::after {
        content: ''; position: absolute;
        width: 14px; height: 14px; border-radius: 50%;
        background: var(--muted); top: 3px; left: 3px;
        transition: all 0.3s;
    }
    [data-theme="light"] .toggle-pill { background: var(--accent); }
    [data-theme="light"] .toggle-pill::after { left: 19px; background: #fff; }

    /* ── MAIN ── */
    .main {
        margin-left: var(--sidebar-w);
        flex: 1; min-height: 100vh;
        display: flex; flex-direction: column;
    }

    /* ── TOPBAR ── */
    .topbar {
        height: 64px;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 16px;
        padding: 0 28px; position: sticky; top: 0; z-index: 40;
    }
    .topbar-title {
        font-family: 'Cabinet Grotesk', sans-serif;
        font-size: 20px; font-weight: 800; flex: 1;
    }
    .topbar-search {
        position: relative; width: 280px;
    }
    .topbar-search input {
        width: 100%; background: var(--surface2);
        border: 1px solid var(--border); border-radius: 10px;
        padding: 9px 16px 9px 38px;
        color: var(--text); font-family: inherit; font-size: 13px;
        outline: none; transition: all 0.2s;
    }
    .topbar-search input:focus { border-color: var(--accent); }
    .topbar-search .search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); color: var(--muted); font-size: 13px;
    }

    .topbar-actions { display: flex; gap: 8px; align-items: center; }
    .icon-btn {
        width: 36px; height: 36px; border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--surface2);
        display: flex; align-items: center; justify-content: center;
        color: var(--muted2); cursor: pointer; font-size: 14px;
        transition: all 0.2s; position: relative;
    }
    .icon-btn:hover { color: var(--text); border-color: var(--border2); }
    .notif-dot {
        position: absolute; top: 6px; right: 6px;
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--red); border: 1.5px solid var(--surface);
    }

    .btn-new {
        display: flex; align-items: center; gap: 7px;
        background: var(--accent-g); color: #fff;
        border: none; border-radius: 10px;
        padding: 9px 18px; font-family: inherit; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.25s;
        box-shadow: 0 4px 16px rgba(108,99,255,0.35);
    }
    .btn-new:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(108,99,255,0.5);
    }

    /* ── PAGE CONTENT ── */
    .page { padding: 28px; flex: 1; }

    /* ── STAT CARDS ── */
    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr));
        gap: 14px; margin-bottom: 28px;
    }
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        display: flex; flex-direction: column; gap: 8px;
        transition: all 0.3s; cursor: default;
        animation: fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
    }
    .stat-card:hover { transform: translateY(-3px); border-color: var(--border2); }
    @keyframes fadeSlideUp {
        from { opacity:0; transform:translateY(20px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .stat-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
    }
    .stat-val {
        font-family: 'Cabinet Grotesk', sans-serif;
        font-size: 32px; font-weight: 900; line-height: 1;
    }
    .stat-label { font-size: 12px; color: var(--muted2); font-weight: 500; }

    /* ── TOOLBAR ROW ── */
    .toolbar {
        display: flex; align-items: center; gap: 12px;
        margin-bottom: 20px; flex-wrap: wrap;
    }
    .filter-tabs { display: flex; gap: 4px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 4px; }
    .ftab {
        padding: 6px 14px; border-radius: 7px; font-size: 13px; font-weight: 500;
        color: var(--muted2); cursor: pointer; transition: all 0.2s; border: none; background: none;
    }
    .ftab.active { background: var(--accent); color: #fff; }
    .ftab:hover:not(.active) { color: var(--text); }

    .select-filter {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 10px; padding: 8px 14px; color: var(--text);
        font-family: inherit; font-size: 13px; outline: none; cursor: pointer;
    }
    .select-filter:focus { border-color: var(--accent); }

    .view-btns { display: flex; gap: 4px; margin-left: auto; }
    .view-btn {
        width: 34px; height: 34px; border-radius: 8px;
        border: 1px solid var(--border); background: var(--surface);
        color: var(--muted2); display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 14px; transition: all 0.2s; text-decoration: none;
    }
    .view-btn:hover, .view-btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* ── TASK CARDS ── */
    .tasks-grid { display: flex; flex-direction: column; gap: 8px; }

    .task-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 18px;
        display: flex; align-items: flex-start; gap: 14px;
        transition: all 0.25s cubic-bezier(0.16,1,0.3,1);
        position: relative; overflow: hidden;
        animation: taskIn 0.4s cubic-bezier(0.16,1,0.3,1) both;
    }
    @keyframes taskIn {
        from { opacity:0; transform:translateX(-16px); }
        to   { opacity:1; transform:translateX(0); }
    }
    .task-card:hover {
        border-color: var(--border2);
        transform: translateX(6px);
        box-shadow: -4px 0 0 var(--accent), 0 4px 24px rgba(0,0,0,0.3);
    }
    .task-card.is-done { opacity: 0.45; }
    .task-card.is-done .task-title { text-decoration: line-through; }

    /* Priority left bar */
    .task-card::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
    }
    .task-card.p-high::before   { background: var(--red); }
    .task-card.p-medium::before { background: var(--yellow); }
    .task-card.p-low::before    { background: var(--green); }

    /* Checkbox */
    .cb {
        width: 22px; height: 22px; border-radius: 50%;
        border: 2px solid var(--border2);
        background: transparent; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.25s; flex-shrink: 0; margin-top: 1px;
        font-size: 11px; color: transparent;
    }
    .cb:hover { border-color: var(--accent); background: rgba(108,99,255,0.1); }
    .cb.checked { background: var(--green); border-color: var(--green); color: #000; font-weight: 900; }

    .task-body { flex: 1; min-width: 0; }
    .task-title { font-size: 14px; font-weight: 600; margin-bottom: 5px; line-height: 1.4; }
    .task-desc  { font-size: 12px; color: var(--muted2); margin-bottom: 10px; line-height: 1.5; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .task-meta { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
    .chip {
        padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 600;
        letter-spacing: 0.2px;
    }
    .chip-high   { background: rgba(255,77,109,0.12); color: var(--red); }
    .chip-medium { background: rgba(255,204,0,0.12);  color: var(--yellow); }
    .chip-low    { background: rgba(0,229,160,0.12);  color: var(--green); }
    .chip-status { background: rgba(108,99,255,0.12); color: var(--accent); }
    .chip-cat    { background: rgba(0,212,255,0.1);   color: var(--cyan); }
    .chip-date   { background: var(--surface2); color: var(--muted2); }
    .chip-date.overdue { color: var(--red); background: rgba(255,77,109,0.1); }
    .chip-tag    { background: var(--surface3); color: var(--muted2); }

    /* Subtask progress */
    .subtask-bar { margin-top: 10px; }
    .subtask-bar-label { font-size: 11px; color: var(--muted2); margin-bottom: 4px; display: flex; justify-content: space-between; }
    .subtask-track { height: 3px; background: var(--surface3); border-radius: 3px; overflow: hidden; }
    .subtask-fill  { height: 100%; background: linear-gradient(90deg,var(--accent),var(--cyan)); border-radius: 3px; transition: width 0.5s; }

    .task-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }
    .task-actions { display: flex; gap: 5px; opacity: 0; transition: opacity 0.2s; }
    .task-card:hover .task-actions { opacity: 1; }
    .act-btn {
        width: 28px; height: 28px; border-radius: 7px;
        border: 1px solid var(--border); background: var(--surface2);
        color: var(--muted2); display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 11px; transition: all 0.2s; text-decoration: none;
    }
    .act-btn:hover            { color: var(--text); border-color: var(--border2); }
    .act-btn.danger:hover     { color: var(--red);   border-color: var(--red);    background: rgba(255,77,109,0.1); }
    .act-btn.edit-act:hover   { color: var(--accent); border-color: var(--accent); background: rgba(108,99,255,0.1); }

    .attachment-count { font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 4px; }

    /* ── EMPTY STATE ── */
    .empty-state {
        text-align: center; padding: 80px 20px;
        animation: fadeSlideUp 0.5s ease both;
    }
    .empty-icon { font-size: 64px; margin-bottom: 20px; filter: grayscale(1); opacity: 0.3; }
    .empty-title { font-family: 'Cabinet Grotesk', sans-serif; font-size: 22px; font-weight: 800; margin-bottom: 8px; color: var(--muted2); }
    .empty-sub   { font-size: 14px; color: var(--muted); }

    /* ── MODAL ── */
    .overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.75); backdrop-filter: blur(10px);
        z-index: 200; display: none; align-items: center; justify-content: center; padding: 20px;
    }
    .overlay.open { display: flex; }
    .modal {
        background: var(--surface); border: 1px solid var(--border2);
        border-radius: 20px; width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto;
        animation: modalPop 0.4s cubic-bezier(0.16,1,0.3,1) both;
    }
    @keyframes modalPop {
        from { opacity:0; transform:scale(0.88) translateY(24px); }
        to   { opacity:1; transform:scale(1) translateY(0); }
    }
    .modal-header {
        padding: 24px 28px 0;
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title {
        font-family: 'Cabinet Grotesk', sans-serif; font-size: 18px; font-weight: 800;
    }
    .modal-close {
        width: 32px; height: 32px; border-radius: 8px;
        border: 1px solid var(--border); background: var(--surface2);
        color: var(--muted2); display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 14px; transition: all 0.2s;
    }
    .modal-close:hover { color: var(--text); }
    .modal-body { padding: 20px 28px 28px; }

    /* ── FORM ELEMENTS ── */
    .form-group { margin-bottom: 16px; }
    .form-label { font-size: 12px; font-weight: 600; color: var(--muted2); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block; }
    .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    .finput {
        width: 100%; background: var(--surface2);
        border: 1px solid var(--border); border-radius: 10px;
        padding: 10px 14px; color: var(--text);
        font-family: inherit; font-size: 14px; outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .finput:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(108,99,255,0.12); }
    select.finput { cursor: pointer; appearance: none; }
    textarea.finput { resize: vertical; min-height: 80px; }

    /* Tags select */
    .tags-wrap { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
    .tag-chip {
        padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
        border: 1.5px solid transparent; cursor: pointer; transition: all 0.2s;
        background: var(--surface3); color: var(--muted2);
    }
    .tag-chip.selected { border-color: currentColor; background: transparent; }

    .modal-footer { display: flex; gap: 10px; margin-top: 20px; }
    .btn { display: flex; align-items: center; gap: 7px; padding: 10px 20px; border-radius: 10px; border: none; font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-accent { background: var(--accent-g); color: #fff; box-shadow: 0 4px 16px rgba(108,99,255,0.3); flex: 1; justify-content: center; }
    .btn-accent:hover { transform: translateY(-1px); box-shadow: 0 7px 24px rgba(108,99,255,0.45); }
    .btn-ghost  { background: var(--surface2); color: var(--muted2); border: 1px solid var(--border); }
    .btn-ghost:hover  { color: var(--text); }

    /* ── TOAST ── */
    .toast-stack { position: fixed; bottom: 24px; right: 24px; z-index: 300; display: flex; flex-direction: column; gap: 8px; }
    .toast {
        background: var(--surface2); border: 1px solid var(--border2);
        border-radius: 12px; padding: 13px 18px;
        display: flex; align-items: center; gap: 10px;
        min-width: 260px; font-size: 13px; font-weight: 500;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        animation: toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both;
    }
    @keyframes toastIn { from { opacity:0; transform:translateX(30px); } to { opacity:1; transform:translateX(0); } }
    .toast.out { animation: toastOut 0.3s ease forwards; }
    @keyframes toastOut { to { opacity:0; transform:translateX(30px); } }
    .toast-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .toast.success .toast-dot { background: var(--green); }
    .toast.error   .toast-dot { background: var(--red); }
    .toast.info    .toast-dot { background: var(--accent); }

    /* ── PROGRESS BAR ── */
    .global-progress {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 18px 22px; margin-bottom: 22px;
    }
    .gp-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; }
    .gp-label  { color: var(--muted2); font-weight: 500; }
    .gp-pct    { font-family: 'Cabinet Grotesk', sans-serif; font-weight: 800; color: var(--accent); }
    .gp-bar    { height: 6px; background: var(--surface3); border-radius: 99px; overflow: hidden; }
    .gp-fill   { height: 100%; border-radius: 99px; background: linear-gradient(90deg,var(--accent),var(--cyan)); box-shadow: 0 0 14px rgba(108,99,255,0.5); transition: width 0.8s cubic-bezier(0.16,1,0.3,1); }

    /* Pagination */
    .pagination-wrap { display: flex; justify-content: center; margin-top: 24px; gap: 6px; }
    .pagination-wrap .page-link, .pagination-wrap .page-num {
        padding: 7px 13px; border-radius: 8px; border: 1px solid var(--border);
        background: var(--surface); color: var(--muted2); font-size: 13px;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .pagination-wrap .page-num.active { background: var(--accent); color: #fff; border-color: var(--accent); }
    .pagination-wrap .page-link:hover, .pagination-wrap .page-num:hover { border-color: var(--accent); color: var(--accent); }

    /* ── MOBILE ── */
    .hamburger { display: none; }
    @media(max-width: 768px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .main { margin-left: 0; }
        .hamburger { display: flex; }
        .topbar-search { width: 180px; }
        .form-row { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: repeat(2,1fr); }
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface3); border-radius: 3px; }

    /* Notification badge glow */
    .notif-count { position:absolute; top:-4px; right:-4px; background:var(--red); color:#fff; font-size:9px; font-weight:700; min-width:16px; height:16px; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:0 3px; }
    </style>
    @yield('extra-css')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('todos.index') }}" style="text-decoration:none;display:flex;align-items:center;gap:12px">
    <div class="logo-mark"><i class="fas fa-bolt"></i></div>
    <div>
        <span class="logo-name">TaskFlow</span>
        <span class="logo-badge">PRO</span>
    </div>
</a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Workspace</div>
        <a href="{{ route('todos.index') }}" class="nav-item {{ request()->routeIs('todos.index') ? 'active' : '' }}">
            <i class="fas fa-list-check icon"></i> All Tasks
        </a>
        <a href="{{ route('todos.kanban') }}" class="nav-item {{ request()->routeIs('todos.kanban') ? 'active' : '' }}">
            <i class="fas fa-columns icon"></i> Kanban Board
        </a>
        <a href="{{ route('todos.calendar') }}" class="nav-item {{ request()->routeIs('todos.calendar') ? 'active' : '' }}">
            <i class="fas fa-calendar icon"></i> Calendar View
        </a>

        <div class="nav-section-label" style="margin-top:8px">Projects</div>
        @foreach(\App\Models\Project::where('status','active')->take(6)->get() as $proj)
        <a href="{{ route('todos.index', ['project' => $proj->id]) }}" class="nav-item">
            <div class="project-dot" style="background:{{ $proj->color }}"></div>
            {{ $proj->name }}
        </a>
        @endforeach
        <a href="{{ route('projects.index') }}" class="nav-item" style="font-size:12px;color:var(--muted)">
            <i class="fas fa-plus icon" style="font-size:11px"></i> Manage Projects
        </a>

        <div class="nav-section-label" style="margin-top:8px">Insights</div>
        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar icon"></i> Reports
        </a>
        <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell icon"></i> Notifications
            @php $unread = \App\Models\Notification::where('read',false)->count(); @endphp
            @if($unread) <span class="nav-badge">{{ $unread }}</span> @endif
        </a>
    </nav>

    <div class="sidebar-footer">
<div class="theme-toggle" onclick="toggleTheme()">
    <span id="themeLabel">
        <i class="fas fa-sun" style="margin-right:7px"></i>Light Mode
    </span>
    <div class="toggle-pill"></div>
</div>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <!-- TOPBAR -->
    <header class="topbar">
        <button class="icon-btn hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-title">@yield('title','Dashboard')</div>
        <div class="topbar-search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="globalSearch" placeholder="Search tasks…">
        </div>
        <div class="topbar-actions">
            <a href="{{ route('notifications.index') }}" class="icon-btn" style="position:relative">
                <i class="fas fa-bell"></i>
                @if($unread ?? 0) <span class="notif-count">{{ $unread }}</span> @endif
            </a>
            <button class="btn-new" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> New Task
            </button>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page">
        @yield('content')
    </main>
</div>

<!-- ADD TASK MODAL -->
<div class="overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">✨ Create New Task</div>
            <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('todos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Task Title *</label>
                    <input type="text" name="title" class="finput" placeholder="What needs to be done?" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="finput" placeholder="Add more details…"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="finput">
                            <option value="medium">🟡 Medium</option>
                            <option value="high">🔴 High</option>
                            <option value="low">🟢 Low</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="finput">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="finput">
                            <option value="">No Project</option>
                            @foreach(\App\Models\Project::where('status','active')->get() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="finput">
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
                        <input type="date" name="due_date" class="finput">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Recurring</label>
                        <select name="recurring" class="finput">
                            <option value="none">Not Recurring</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                <!-- Tags -->
                @php $allTags = \App\Models\Tag::all(); @endphp
                @if($allTags->count())
                <div class="form-group">
                    <label class="form-label">Tags</label>
                    <div class="tags-wrap" id="tagsWrap">
                        @foreach($allTags as $tag)
                        <div class="tag-chip" style="color:{{ $tag->color }}" onclick="toggleTag(this, {{ $tag->id }})">
                            {{ $tag->name }}
                        </div>
                        @endforeach
                    </div>
                    <div id="tagInputs"></div>
                </div>
                @endif
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn btn-accent"><i class="fas fa-plus"></i> Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TOAST STACK -->
<div class="toast-stack" id="toastStack"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── THEME ──
// ── THEME ──
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.dataset.theme === 'dark';
    html.dataset.theme = isDark ? 'light' : 'dark';
    localStorage.setItem('theme', html.dataset.theme);
    updateThemeLabel();
}

function updateThemeLabel() {
    const isDark = document.documentElement.dataset.theme === 'dark';
    const label  = document.getElementById('themeLabel');
    if (!label) return;

    label.innerHTML = isDark
        ? '<i class="fas fa-sun"  style="margin-right:7px"></i>Light Mode'
        : '<i class="fas fa-moon" style="margin-right:7px"></i>Dark Mode';
}

(function() {
    const saved = localStorage.getItem('theme') || 'dark'; // default dark
    document.documentElement.dataset.theme = saved;
    updateThemeLabel();
})();

// ── MODALS ──
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});

// ── TAGS ──
function toggleTag(el, id) {
    el.classList.toggle('selected');
    const wrap = document.getElementById('tagInputs');
    const existing = wrap.querySelector(`input[value="${id}"]`);
    if (existing) existing.remove();
    else {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'tags[]'; inp.value = id;
        wrap.appendChild(inp);
    }
}

// ── TOAST ──
function toast(msg, type = 'info') {
    const stack = document.getElementById('toastStack');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<div class="toast-dot"></div><span>${msg}</span>`;
    stack.appendChild(t);
    setTimeout(() => { t.classList.add('out'); setTimeout(() => t.remove(), 300); }, 3500);
}

// ── AJAX TOGGLE ──
document.querySelectorAll('.cb').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id  = btn.dataset.id;
        const r   = await fetch(`{{ url('/') }}/todos/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (r.ok) {
            const d = await r.json();

            // ── 1. Checkbox update ──
            btn.classList.toggle('checked', d.completed);
            btn.textContent = d.completed ? '✓' : '';

            // ── 2. Card fade ──
            const card = document.getElementById(`tc-${id}`);
            if (card) {
                card.classList.toggle('is-done', d.completed);
            }

            // ── 3. Stats update (bina reload) ──
            updateStats();

            // ── 4. Toast ──
            toast(
                d.completed ? 'Task completed! 🎉' : 'Task reopened 🔄',
                d.completed ? 'success' : 'info'
            );
        }
    });
});

// Stats AJAX se update karo
async function updateStats() {
    const r = await fetch(`{{ url('/') }}/todos/stats`, {
        headers: { 'Accept': 'application/json' }
    });
    if (!r.ok) return;
    const d = await r.json();

    // Numbers update karo
    const els = {
        total:       document.querySelector('[data-stat="total"]'),
        completed:   document.querySelector('[data-stat="completed"]'),
        in_progress: document.querySelector('[data-stat="in_progress"]'),
        overdue:     document.querySelector('[data-stat="overdue"]'),
        pending:     document.querySelector('[data-stat="pending"]'),
    };
    Object.entries(els).forEach(([key, el]) => {
        if (el && d[key] !== undefined) {
            el.textContent = d[key];
            // Count up animation
            el.style.transform = 'scale(1.3)';
            el.style.transition = 'transform 0.2s';
            setTimeout(() => el.style.transform = 'scale(1)', 200);
        }
    });

    // Progress bar update
    const pct  = d.total > 0 ? Math.round((d.completed / d.total) * 100) : 0;
    const fill = document.getElementById('progressFill');
    const pctEl= document.getElementById('progressPct');
    if (fill)  fill.style.width  = pct + '%';
    if (pctEl) pctEl.textContent = pct + '%';
}
// ── AJAX DELETE ──
document.querySelectorAll('.del-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Delete this task?')) return;
        const id = btn.dataset.id;
        const r = await fetch(`{{ url('/') }}/todos/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json'
            }
        });
        if (r.ok) {
            const card = document.getElementById(`tc-${id}`);
            card.style.cssText = 'opacity:0;transform:translateX(-100%);transition:all 0.4s ease;max-height:0;margin:0;padding:0;overflow:hidden';
            setTimeout(() => card.remove(), 400);
            toast('Task deleted', 'error');
        }
    });
});
// ── GLOBAL SEARCH ──
document.getElementById('globalSearch').addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.task-card').forEach(card => {
        const text = card.querySelector('.task-title')?.textContent.toLowerCase() || '';
        card.style.display = text.includes(q) ? 'flex' : 'none';
    });
});

// ── SESSION TOAST ──
@if(session('success')) toast('{{ session("success") }}', 'success'); @endif
@if(session('error'))   toast('{{ session("error") }}',   'error');   @endif
</script>
@yield('extra-js')
</body>
</html>