<?php
namespace App\Http\Controllers;


use App\Models\User;
use App\Models\ActivityLog;

use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\ItemMatch;
use App\Models\Category;
use App\Models\SuccessStory;
use App\Models\Notification;
use App\Notifications\MatchVerifiedNotification;
use App\Services\MatchingService;  // Correct namespace
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_lost_items' => LostItem::count(),
            'total_found_items' => FoundItem::count(),
            'pending_matches' => ItemMatch::pending()->count(),
            'pending_verifications' => LostItem::unverified()->count() + FoundItem::unverified()->count(),
            'recent_success_stories' => SuccessStory::where('is_published', true)->count(),
            'active_today' => User::whereDate('last_login_at', today())->count()
        ];

        $recentMatches = ItemMatch::with(['lostItem', 'foundItem'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentItems = [
            'lost' => LostItem::with('user')->orderBy('created_at', 'desc')->limit(5)->get(),
            'found' => FoundItem::with('user')->orderBy('created_at', 'desc')->limit(5)->get()
        ];

        return view('admin.dashboard', compact('stats', 'recentMatches', 'recentItems'));
    }

    // Verification Queue
    public function verificationQueue()
    {
        $matches = ItemMatch::with(['lostItem', 'foundItem', 'lostItem.user', 'foundItem.user'])
            ->whereIn('match_status', ['pending', 'review'])
            ->orderBy('similarity_score', 'desc')
            ->paginate(20);

        $pendingItems = [
            'lost' => LostItem::with('user')->where('is_verified', false)->orWhereNull('is_verified')->orderBy('created_at', 'desc')->get(),
            'found' => FoundItem::with('user')->where('is_verified', false)->orderBy('created_at', 'desc')->get()
        ];

        return view('admin.verification-queue', compact('matches', 'pendingItems'));
    }

    // Verify Match
    public function verifyMatch(Request $request)
    {
        $id = $request->id;
        
        $match = ItemMatch::with(['lostItem.user', 'foundItem.user'])->findOrFail($id);
        
        $validated = $request->validate([
            'verification_notes' => 'nullable|string|max:500'
        ]);

        $match->update(['match_status' => 'verified','verification_notes' => $validated['verification_notes'] ?? null]);
        $match->lostItem->update([
        'status' => 'verified',
        'is_verified' => true,
        'verified_at' => now(),
        'verified_by' => auth()->id(),
        ]);

        $match->foundItem->update([
        'status' => 'verified',
        'is_verified' => true,
        'verified_at' => now(),
        'verified_by' => auth()->id(),
        ]);
        $lostUser = $match->lostItem->user;
        $foundUser = $match->foundItem->user;
        $lostUser->notify(
        new MatchVerifiedNotification(
            $foundUser,
            $match->foundItem,
            'lost'
        )
        );

        // Notify FOUND user with LOST user details
        $foundUser->notify(
        new MatchVerifiedNotification(
            $lostUser,
            $match->lostItem,
            'found'
        )
        );
        $match->match_status = 'verified';
        $match->save();


    //     $match->lostItem->user->notify(
    //     new MatchVerifiedNotification($match)
    // );

    // $match->foundItem->user->notify(
    //     new MatchVerifiedNotification($match)
    // );

        // Verify the match
        // $match->verify(Auth::id(), $validated['verification_notes']);

            // Verify the match (you can call a service here)
        MatchingService::verifyMatch($match->id, Auth::id());

        // Send notifications to users
       // $this->sendVerificationNotifications($match);

        // Log activity
        ActivityLog::log('verified', $match, "Match verified by admin");

        // $this->matchingService->verifyMatch($match, Auth::id(), $validated['verification_notes']);

        //  return redirect()
        // ->route('admin.verifications')
        // ->with('success', 'Match verified successfully ');

        return back()->with('success', 'Match verified successfully!');
    }

    // Send Notifications
    // private function sendVerificationNotifications(ItemMatch $match)
    // {
    //     // Notification to lost item owner
    //     Notification::create([
    //         'user_id' => $match->lostItem->user_id,
    //         'title' => 'Your Lost Item Has Been Verified!',
    //         'message' => "Your lost item '{$match->lostItem->item_name}' has been verified. Contact details of the finder will be provided.",
    //         'type' => 'item_verified',
    //         'related_item_id' => $match->lost_id,
    //         'item_type' => 'lost',
    //         'data' => [
    //             'finder_name' => $match->foundItem->user->username,
    //             'finder_contact' => $match->foundItem->handover_contact ?? $match->foundItem->user->phone_number,
    //             'handover_person' => $match->foundItem->handover_person,
    //             'storage_location' => $match->foundItem->storage_location
    //         ]
    //     ]);

    //     // Notification to found item owner
    //     Notification::create([
    //         'user_id' => $match->foundItem->user_id,
    //         'title' => 'Your Found Item Has Been Verified!',
    //         'message' => "The item you found '{$match->foundItem->item_name}' has been verified. The owner will contact you.",
    //         'type' => 'item_verified',
    //         'related_item_id' => $match->found_id,
    //         'item_type' => 'found',
    //         'data' => [
    //             'owner_name' => $match->lostItem->user->username,
    //             'owner_contact' => $match->lostItem->user->phone_number
    //         ]
    //     ]);
    // }

    // User Management
    public function users()
    {
        $users = User::withCount(['lostItems', 'foundItems', 'notifications'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    // Categories Management
    public function categories()
    {
        $categories = Category::with('parent')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories', compact('categories'));
    }

    // Success Stories Management
    public function successStories()
    {
        $stories = SuccessStory::with(['lostItem', 'foundItem'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.success-stories', compact('stories'));
    }

    // System Reports
    public function reports()
    {
        // Monthly statistics
        $monthlyStats = [
            'lost_items' => LostItem::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get(),
            'found_items' => FoundItem::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get(),
            'recovered_items' => ItemMatch::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->where('match_status', 'completed')
                ->groupBy('month')
                ->get()
        ];

        // Category-wise distribution
        $categoryStats = Category::withCount(['lostItems', 'foundItems'])->get();

        // Recovery rate
        $totalLost = LostItem::count();
        $totalRecovered = ItemMatch::where('match_status', 'completed')->count();
        $recoveryRate = $totalLost > 0 ? round(($totalRecovered / $totalLost) * 100, 2) : 0;

        return view('admin.reports', compact('monthlyStats', 'categoryStats', 'recoveryRate'));
    }
}