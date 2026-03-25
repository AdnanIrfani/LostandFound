<?php
namespace App\Services;

use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\ItemMatch;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MatchingService
{
    /**
     * Generate matches between unverified lost and found items
     * with more flexible matching criteria
     */
    public static function generateMatches()
    {
        $lostItems = LostItem::whereIn('status', ['pending','open'])->get();
        $foundItems = FoundItem::whereIn('status', ['pending','unclaimed'])->get();

        foreach ($lostItems as $lost) {
            foreach ($foundItems as $found) {
                if (self::isPotentialMatch($lost, $found)) {
                    self::createMatch($lost, $found);
                }
            }
        }
        Log::info("Matching service ran. Created matches for admin review. ");

    }
    
    /**
     * Check if lost and found items are potential matches
     */
    private static function isPotentialMatch($lost, $found)
    {
        // 1. Check if items are already matched
        if (ItemMatch::where('lost_id', $lost->lost_id)
                     ->where('found_id', $found->found_id)
                     ->exists()) {
            return false;
        }
        
        // 2. Item name similarity (more flexible)
        $nameSimilarity = self::calculateSimilarity(
            strtolower($lost->item_name),
            strtolower($found->item_name)
        );
        
        // 3. Check location if available
        $locationMatch = false;
        if ($lost->lost_location && $found->found_location) {
            $locationSimilarity = self::calculateSimilarity(
                strtolower($lost->lost_location),
                strtolower($found->found_location)
            );
            $locationMatch = ($locationSimilarity >= 0.6); // 60% similarity
        }
        
        // 4. Check date proximity (within 7 days)
        $dateMatch = false;
        if ($lost->lost_date && $found->found_date) {
            $daysDifference = abs($lost->lost_date->diffInDays($found->found_date));
            $dateMatch = ($daysDifference <= 7);
        }
        
        // Match if: name is similar AND (location matches OR date matches)
        return ($nameSimilarity >= 0.7) && ($locationMatch || $dateMatch);
    }
    // private static function isPotentialMatch($lost, $found)
    // {
    // if (ItemMatch::where('lost_id', $lost->lost_id)
    //     ->where('found_id', $found->found_id)
    //     ->exists()) {
    //     return false;
    // }

    // $nameSimilarity = self::calculateSimilarity(
    //     strtolower($lost->item_name),
    //     strtolower($found->item_name)
    // );

    // $locationMatch = false;
    // if ($lost->lost_location && $found->found_location) {
    //     $locationSimilarity = self::calculateSimilarity(
    //         strtolower($lost->lost_location),
    //         strtolower($found->found_location)
    //     );
    //     $locationMatch = $locationSimilarity >= 0.6;
    // }

    // $dateMatch = false;
    // if ($lost->lost_date && $found->found_date) {
    //     $daysDifference = abs($lost->lost_date->diffInDays($found->found_date));
    //     $dateMatch = $daysDifference <= 7;
    // }

    // return $nameSimilarity >= 0.6;
    // }

    
    /**
     * Calculate string similarity (simple implementation)
     */
    private static function calculateSimilarity($str1, $str2)
    {
        similar_text($str1, $str2, $percent);
        return $percent / 100;
    }
    
    /**
     * Create a match record and update item statuses
     */
    private static function createMatch($lost, $found)
    {
            // Prevent duplicate matches
    if (
        ItemMatch::where('lost_id', $lost->lost_id)
            ->where('found_id', $found->found_id)
            ->exists()
    ) {
        return null;
    }
        $match = ItemMatch::create([
            'lost_id' => $lost->lost_id,
            'found_id' => $found->found_id,
            'match_score' => self::calculateMatchScore($lost, $found),
            'status' => 'pending',
            'match_reason' => 'Auto-matched by system',
            'admin_notified' => false,
        ]);
        
        // Update item statuses
        $lost->update(['status' => 'matched']);
        $found->update(['status' => 'unclaimed']);
        
        // Notify admin
        self::notifyAdmin($match);
        
        return $match;
    }
    
    /**
     * Calculate match score based on multiple factors
     */
    private static function calculateMatchScore($lost, $found)
    {
        $score = 0;
        
        // Name similarity (0-40 points)
        $nameSimilarity = self::calculateSimilarity(
            strtolower($lost->item_name),
            strtolower($found->item_name)
        );
        $score += $nameSimilarity * 40;
        
        // Location similarity if available (0-30 points)
        if ($lost->lost_location && $found->found_location) {
            $locationSimilarity = self::calculateSimilarity(
                strtolower($lost->lost_location),
                strtolower($found->found_location)
            );
            $score += $locationSimilarity * 30;
        }
        
        // Date proximity (0-30 points)
        if ($lost->lost_date && $found->found_date) {
            $daysDifference = abs($lost->lost_date->diffInDays($found->found_date));
            if ($daysDifference <= 1) $score += 30;
            elseif ($daysDifference <= 3) $score += 20;
            elseif ($daysDifference <= 7) $score += 10;
        }
        
        return min(100, $score); // Cap at 100
    }
    
    /**
     * Notify admin about new match
     */
    private static function notifyAdmin($match)
    {

    if (!$match->lost || !$match->found) {
        Log::error("Match {$match->id} has missing relations");
        return;
    }

        // Create notification for admin users
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'match_pending',
                'title' => 'New Match Needs Verification',
                'message' => "Potential match found between lost item '{$match->lost->item_name}' and found item '{$match->found->item_name}'",
                'data' => [
                    'match_id' => $match->id,
                    'lost_id' => $match->lost_id,
                    'found_id' => $match->found_id,
                ],
                'read' => false,
            ]);
        }
        
        $match->update(['admin_notified' => true]);
        Log::info("Admin notified about match ID: {$match->id}");
    }
    
    /**
     * Run matching for specific items (for real-time matching)
     */
    public static function matchForNewItem($item, $type = 'lost')
    {
        if ($type === 'lost') {
            $foundItems = FoundItem::where('status', ['unclaimed','open'])->get();
            foreach ($foundItems as $found) {
                if (self::isPotentialMatch($item, $found)) {
                    self::createMatch($item, $found);
                }
            }
        } else {
            $lostItems = LostItem::where('status', ['open'])->get();
            foreach ($lostItems as $lost) {
                if (self::isPotentialMatch($lost, $item)) {
                    self::createMatch($lost, $item);
                }
            }
        }
    }
    public static function verifyMatch($matchId, $adminId)
    {
    $match = ItemMatch::with(['lost', 'found'])->findOrFail($matchId);

    // Update match status
    $match->update([
        'status' => 'verified'
    ]);

    // Update lost item
    $match->lost->update([
        'status' => 'verified',
        'is_verified' => true,
        'verified_at' => now(),
        'verified_by' => $adminId
    ]);

    // Update found item
    $match->found->update([
        'status' => 'verified',
        'is_verified' => true,
        'verified_at' => now(),
        'verified_by' => $adminId
    ]);

    // Notify lost item owner
    Notification::create([
        'user_id' => $match->lost->user_id,
        'title' => 'Item Verified ✅',
        'message' => "Your lost item '{$match->lost->item_name}' has been verified. Please contact admin for further details.",
        'read' => false,
    ]);

    // Notify found item owner
    Notification::create([
        'user_id' => $match->found->user_id,
        'title' => 'Item Verified ✅',
        'message' => "The found item '{$match->found->item_name}' has been verified. Thank you for your help!",
        'read' => false,
    ]);

    return true;
    }
}