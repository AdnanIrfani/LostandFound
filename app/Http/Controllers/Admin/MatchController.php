<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ItemMatch;

class MatchController
{
    public function index()
    {
        // Show ALL matches regardless of status
        $matches = ItemMatch::with(['lost', 'found'])->get();
        
        return view('admin.matches', ['matches' => $matches]);
    }
    
    public function verify(Request $request, $id)
    {
        $match = ItemMatch::find($id);
        
        // Add a status column if doesn't exist
        if ($request->action === 'approve') {
            $match->update(['status' => 'approved']);
            // Also update items if needed
            if ($match->lost) $match->lost->update(['verified' => 1]);
            if ($match->found) $match->found->update(['verified' => 1]);
        } else {
            $match->update(['status' => 'rejected']);
        }
        
        return back()->with('success', 'Match verified!');
    }
}