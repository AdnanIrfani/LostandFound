<?php
namespace App\Http\Controllers;


use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Get unread notifications count (for AJAX)
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->count();
            
        return response()->json(['count' => $count]);
    }

    // Mark as read
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    // Mark all as read
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->update(['status' => 'read', 'read_at' => now()]);
            
        return response()->json(['success' => true]);
    }

    // Delete notification
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification deleted successfully!');
    }
}