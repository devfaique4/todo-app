<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function markRead(Notification $notification)
    {
        $notification->update(['read' => true]);
        return redirect()->back();
    }

    public function readAll()
    {
        Notification::where('read', false)->update(['read' => true]);
        return redirect()->back()->with('success', 'All marked as read');
    }
}