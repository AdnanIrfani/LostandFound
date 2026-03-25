<?php

namespace App\Http\Controllers;

use App\Models\FoundItem;
use App\Models\Category;
use App\Services\MatchingService;  // Correct namespace
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoundItemController extends Controller
{
    public function index()
    {
        $foundItems = FoundItem::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $categories = Category::all();
        return view('found-items.index', compact('foundItems', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('found-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'description' => 'required|string',
            'found_date' => 'required|date',
            'found_location' => 'required|string|max:255',
            'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'item_condition' => 'required|in:excellent,good,fair,poor',
            'storage_location' => 'nullable|string|max:255',
            'handover_person' => 'nullable|string|max:255',
            'handover_contact' => 'nullable|string|max:20',
            'handover_to_admin' => 'boolean'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'unclaimed';
        $validated['handover_to_admin'] = $request->has('handover_to_admin') ? true : false;

        if ($request->hasFile('item_image')) {
            $path = $request->file('item_image')->store('found-items', 'public');
            $validated['item_image'] = basename($path);
        }

        
        FoundItem::create($validated);
        $foundItem = FoundItem::create($validated);

MatchingService::matchForNewItem($foundItem, 'found');

        try {
            $this->matchingService->findMatchesForFoundItem($foundItem);
        } catch (\Exception $e) {
            \Log::error('Matching error: ' . $e->getMessage());
        }
        return redirect()->route('found-items.index')
            ->with('success', 'Found item reported successfully! We\'ll check for matches.');
    }

    public function show($id)
    {
        $foundItem = FoundItem::with(['user', 'category', 'matches.lostItem'])
            ->findOrFail($id);
        
        return view('found-items.show', compact('foundItem'));
    }

    public function edit($id)
    {
        $foundItem = FoundItem::findOrFail($id);
        $categories = Category::all();
        
        // Check authorization
        if ($foundItem->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        return view('found-items.edit', compact('foundItem', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $foundItem = FoundItem::findOrFail($id);
        
        // Check authorization
        if ($foundItem->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'description' => 'required|string',
            'found_date' => 'required|date',
            'found_location' => 'required|string|max:255',
            'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'item_condition' => 'required|in:excellent,good,fair,poor',
            'storage_location' => 'nullable|string|max:255',
            'handover_person' => 'nullable|string|max:255',
            'handover_contact' => 'nullable|string|max:20',
            'handover_to_admin' => 'boolean',
            'status' => 'required|in:unclaimed,claimed,verified,returned'
        ]);

        $validated['handover_to_admin'] = $request->has('handover_to_admin') ? true : false;

        if ($request->hasFile('item_image')) {
            // Delete old image
            if ($foundItem->item_image) {
                Storage::delete('public/found-items/' . $foundItem->item_image);
            }
            
            $path = $request->file('item_image')->store('found-items', 'public');
            $validated['item_image'] = basename($path);
        }

        $foundItem->update($validated);

        return redirect()->route('found-items.show', $foundItem->found_id)
            ->with('success', 'Found item updated successfully!');
    }

    public function destroy($id)
    {
        $foundItem = FoundItem::findOrFail($id);
        
        // Check authorization
        if ($foundItem->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Delete image if exists
        if ($foundItem->item_image) {
            Storage::delete('public/found-items/' . $foundItem->item_image);
        }

        $foundItem->delete();

        return redirect()->route('found-items.index')
            ->with('success', 'Found item deleted successfully!');
    }

    public function search(Request $request)
    {
        $query = FoundItem::where('status', 'unclaimed');

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
            $query->where('found_location', 'LIKE', "%{$request->location}%");
        }

        if ($request->has('date_from')) {
            $query->where('found_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('found_date', '<=', $request->date_to);
        }

        $foundItems = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::all();

        return view('found-items.index', compact('foundItems', 'categories'));
    }
}