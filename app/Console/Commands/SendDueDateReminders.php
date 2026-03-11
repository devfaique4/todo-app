<?php

namespace App\Console\Commands;

use App\Models\Todo;
use App\Models\Notification;
use Illuminate\Console\Command;

class SendDueDateReminders extends Command
{
    protected $signature   = 'todos:reminders';
    protected $description = 'Create notifications for due and overdue tasks';

    public function handle()
    {
        $dueSoonCount  = 0;
        $overdueCount  = 0;

        // ── DUE TODAY ──
        $dueSoon = Todo::whereDate('due_date', today())
                       ->where('completed', false)
                       ->get();

        foreach ($dueSoon as $todo) {
            Notification::updateOrCreate(
                [
                    'todo_id' => $todo->id,
                    'type'    => 'due_soon',
                ],
                [
                    'title'   => 'Due Today ⏰',
                    'message' => "Task \"{$todo->title}\" is due today!",
                    'read'    => false,
                ]
            );
            $dueSoonCount++;
        }

        // ── OVERDUE ──
        $overdue = Todo::whereDate('due_date', '<', today())
                       ->where('completed', false)
                       ->get();

        foreach ($overdue as $todo) {
            Notification::updateOrCreate(
                [
                    'todo_id' => $todo->id,
                    'type'    => 'overdue',
                ],
                [
                    'title'   => 'Overdue! ⚠️',
                    'message' => "Task \"{$todo->title}\" is overdue!",
                    'read'    => false,
                ]
            );
            $overdueCount++;
        }

        $this->info("✅ Due today:  {$dueSoonCount} notifications");
        $this->info("⚠️  Overdue:    {$overdueCount} notifications");
        $this->info("🔔 Total:      " . ($dueSoonCount + $overdueCount) . " notifications created");

        return 0;
    }
}