<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Expense;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function store(Request $request, $groupId) {
        $request->validate(['name'=>'required|string|max:50']);
        $member = Member::create(['group_id'=>$groupId, 'name'=>$request->name]);
        return response()->json($member, 201);
    }

    public function destroy($groupId, $memberId)
    {
        // Find member in that specific group
        $member = Member::where('group_id', $groupId)
                    ->where('id', $memberId)
                    ->first();

        if (!$member) {
            return response()->json(['message' => 'Member not found in this group'], 404);
        }

        // SAFETY LOGIC: Check if member has any expenses
        $hasExpense = Expense::where('paid_by', $memberId)->exists();
        
        if ($hasExpense) {
            return response()->json([
                'message' => 'Cannot delete member - member has expenses. Delete expenses first.'
            ], 400);
        }

        $member->delete();

        return response()->json(['message' => 'Member deleted successfully'], 200);
    }
}