<?php
namespace App\Http\Controllers;


use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\ItemMatch;
use App\Models\Notification;
use App\Models\Category;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // User Dashboard
    public function dashboard()
    {
        $user = Auth::user();
        
        // Recent success stories for home page
        $successStories = SuccessStory::with(['lostItem', 'foundItem'])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // User's items
        $userLostItems = LostItem::where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $userFoundItems = FoundItem::where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent matches for user's items
        $userMatches = ItemMatch::whereHas('lostItem', function($query) use ($user) {
                $query->where('user_id', $user->user_id);
            })
            ->orWhereHas('foundItem', function($query) use ($user) {
                $query->where('user_id', $user->user_id);
            })
            ->with(['lostItem', 'foundItem'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'successStories', 
            'userLostItems', 
            'userFoundItems', 
            'userMatches'
        ));
    }

    // Lost Item Form
    public function showLostForm()
    {
        $categories = Category::where('is_active', true)->get();
        return view('user.lost-form', compact('categories'));
    }

    // Found Item Form
    public function showFoundForm()
    {
        $categories = Category::where('is_active', true)->get();
        return view('user.found-form', compact('categories'));
    }

    // Notifications


public function notifications()
{
    // Get logged-in user's notifications (Laravel way)
    $notifications = Auth::user()
        ->notifications()
        ->latest()
        ->paginate(10);

    // Decode JSON data column for blade usage
    $notifications->getCollection()->transform(function ($notification) {
        $notification->data = is_array($notification->data)
            ? $notification->data
            : json_decode($notification->data, true);
        return $notification;
    });

    return view('user.notifications', compact('notifications'));
}

// Mark single notification as read
public function markAsRead($id)
{
    $notification = Auth::user()
        ->notifications()
        ->findOrFail($id);

    $notification->markAsRead();

    return response()->json(['success' => true]);
}

// Mark all notifications as read
public function markAllRead()
{
    Auth::user()
        ->unreadNotifications
        ->markAsRead();

    return response()->json(['success' => true]);
}

// Delete notification
public function destroyNotification($id)
{
    $notification = Auth::user()
        ->notifications()
        ->findOrFail($id);

    $notification->delete();

    return response()->json(['success' => true]);
}
public function unreadCount()
{
    return response()->json([
        'count' => Auth::user()->unreadNotifications()->count()
    ]);
}

    // public function notifications()
    // {
    //     $notifications = Notification::where('user_id', Auth::id())
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(20);

    //     // Mark all as read
    //     Notification::where('user_id', Auth::id())
    //         ->where('status', 'unread')
    //         ->update(['status' => 'read', 'read_at' => now()]);

    //     return view('user.notifications', compact('notifications'));
    // }

    // Profile
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    // Update Profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'phone_number' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1|max:5',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validated['profile_picture'] = basename($path);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    // Change Password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    // My Lost Items
    public function myLostItems()
    {
        $lostItems = LostItem::where('user_id', Auth::id())
            ->with(['category', 'matches'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user.my-lost-items', compact('lostItems'));
    }

    // My Found Items
    public function myFoundItems()
    {
        $foundItems = FoundItem::where('user_id', Auth::id())
            ->with(['category', 'matches'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user.my-found-items', compact('foundItems'));
    }

    // Search Items
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'lost');
        
        if ($type === 'lost') {
            $items = LostItem::where('status', 'open')
                ->where(function($q) use ($query) {
                    $q->where('item_name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('lost_location', 'LIKE', "%{$query}%");
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        } else {
            $items = FoundItem::where('status', 'unclaimed')
                ->where(function($q) use ($query) {
                    $q->where('item_name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('found_location', 'LIKE', "%{$query}%");
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }

        return view('user.search-results', compact('items', 'query', 'type'));
    }
}