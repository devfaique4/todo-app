<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;

Route::get('/', fn() => redirect()->route('todos.index'));


Route::get('todos/kanban',            [TodoController::class, 'kanban'])->name('todos.kanban');
Route::get('todos/calendar',          [TodoController::class, 'calendar'])->name('todos.calendar');
Route::patch('todos/{todo}/toggle',   [TodoController::class, 'toggle'])->name('todos.toggle');
Route::patch('todos/{todo}/status',   [TodoController::class, 'updateStatus'])->name('todos.status');
Route::patch('todos/{todo}/position', [TodoController::class, 'updatePosition'])->name('todos.position');
Route::get('todos/stats', [TodoController::class, 'stats'])->name('todos.stats');

// Sub-tasks
Route::post('todos/{todo}/subtasks',         [TodoController::class, 'storeSubtask'])->name('subtasks.store');
Route::patch('todos/{todo}/subtasks/{sub}',  [TodoController::class, 'toggleSubtask'])->name('subtasks.toggle');
Route::delete('todos/{todo}/subtasks/{sub}', [TodoController::class, 'deleteSubtask'])->name('subtasks.destroy');


Route::resource('todos', TodoController::class);

// Projects
Route::resource('projects', ProjectController::class);

// Comments
Route::post('todos/{todo}/comments',   [CommentController::class, 'store'])->name('comments.store');
Route::delete('comments/{comment}',    [CommentController::class, 'destroy'])->name('comments.destroy');

// Attachments
Route::post('todos/{todo}/attachments',         [AttachmentController::class, 'store'])->name('attachments.store');
Route::delete('attachments/{attachment}',       [AttachmentController::class, 'destroy'])->name('attachments.destroy');
Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');

// Notifications
Route::get('notifications',            [NotificationController::class, 'index'])->name('notifications.index');
Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
Route::post('notifications/read-all',  [NotificationController::class, 'readAll'])->name('notifications.readAll');

// Reports
Route::get('reports',            [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');