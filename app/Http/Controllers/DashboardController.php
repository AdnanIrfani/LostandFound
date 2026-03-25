<?php
namespace App\Http\Controllers;


use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Match;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'lost_items' => LostItem::where('user_id', $user->user_id)->count(),
            'found_items' => FoundItem::where('user_id', $user->user_id)->count(),
            'open_lost' => LostItem::where('status', 'open')->count(),
            'unclaimed_found' => FoundItem::where('status', 'unclaimed')->count(),
            'total_matches' => ItemMatch::where('matched_by_user_id', $user->user_id)->count(),
            'pending_matches' => ItemMatch::where('match_status', 'pending')->count(),
        ];

        $recentLost = LostItem::where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentFound = FoundItem::where('status', 'unclaimed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // $notifications = Notification::where('user_id', $user->user_id)
        //     ->where('status', 'unread')
        //     ->orderBy('created_at', 'desc')
        //     ->limit(10)
        //     ->get();

        return view('dashboard.index', compact('stats', 'recentLost', 'recentFound', 'notifications'));
    }
}