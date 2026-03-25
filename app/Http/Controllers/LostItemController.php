<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\Category;
use App\Services\MatchingService;  // Correct namespace
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LostItemController extends Controller
{
       protected $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }
    public function index()
    {
        $lostItems = LostItem::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $categories = Category::all();
        return view('lost-items.index', compact('lostItems', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('lost-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'description' => 'required|string',
            'lost_date' => 'required|date',
            'lost_location' => 'required|string|max:255',
            'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'reward' => 'nullable|numeric|min:0',
            'claim_deadline' => 'nullable|date|after:lost_date'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'open';
        $validated['is_resolved'] = false;

        if ($request->hasFile('item_image')) {
            $path = $request->file('item_image')->store('lost-items', 'public');
            $validated['item_image'] = basename($path);
        }

        $lostItem = LostItem::create($validated);
        
        LostItem::create($validated);   
        MatchingService::matchForNewItem($lostItem, 'lost'); 
        // try {
        //     $this->matchingService->findMatchesForLostItem($lostItem);
        // } catch (\Exception $e) {
        //     // Log error but continue
        //     \Log::error('Matching error: ' . $e->getMessage());
        // }
        return redirect()->route('lost-items.index')
            ->with('success', 'Lost item reported successfully! We\'ll notify you if matches are found.');


    }

    public function show($id)
    {
        $lostItem = LostItem::with(['user', 'category', 'matches.foundItem'])
            ->findOrFail($id);
        
        return view('lost-items.show', compact('lostItem'));
    }

    public function edit($id)
    {
        $lostItem = LostItem::findOrFail($id);
        $categories = Category::all();
        
        // Check authorization
        if ($lostItem->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        return view('lost-items.edit', compact('lostItem', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $lostItem = LostItem::findOrFail($id);
        
        // Check authorization
        if ($lostItem->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'description' => 'required|string',
            'lost_date' => 'required|date',
            'lost_location' => 'required|string|max:255',
            'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'reward' => 'nullable|numeric|min:0',
            'claim_deadline' => 'nullable|date|after:lost_date',
            'status' => 'required|in:open,found,closed'
        ]);

        if ($request->hasFile('item_image')) {
            // Delete old image
            if ($lostItem->item_image) {
                Storage::delete('public/lost-items/' . $lostItem->item_image);
            }
            
            $path = $request->file('item_image')->store('lost-items', 'public');
            $validated['item_image'] = basename($path);
        }

        $lostItem->update($validated);

        return redirect()->route('lost-items.show', $lostItem->lost_id)
            ->with('success', 'Lost item updated successfully!');
    }

    public function destroy($id)
    {
        $lostItem = LostItem::findOrFail($id);
        
        // Check authorization
        if ($lostItem->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Delete image if exists
        if ($lostItem->item_image) {
            Storage::delete('public/lost-items/' . $lostItem->item_image);
        }

        $lostItem->delete();

        return redirect()->route('lost-items.index')
            ->with('success', 'Lost item deleted successfully!');
    }

    public function search(Request $request)
    {
        $query = LostItem::where('status', 'open');

        if ($request->has('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('item_name', 'LIKE', "%{$request->keyword}%")
                  ->orWhere('description', 'LIKE', "%{$request->keyword}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('location')) {
            $query->where('lost_location', 'LIKE', "%{$request->location}%");
        }

        if ($request->has('date_from')) {
            $query->where('lost_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('lost_date', '<=', $request->date_to);
        }

        $lostItems = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::all();

        return view('lost-items.index', compact('lostItems', 'categories'));
    }
}