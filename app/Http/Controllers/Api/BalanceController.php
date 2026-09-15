<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Group;

class BalanceController extends Controller
{
    public function show($groupId) {
        $group = Group::with(['members','expenses'])->findOrFail($groupId);
        $members = $group->members;
        $expenses = $group->expenses;
        if($members->count()==0) return response()->json(['message'=>'No members'], 400);
        $total = $expenses->sum('amount');
        $share = $members->count() > 0 ? $total / $members->count() : 0;
        $balances = [];
        foreach($members as $member) {
            $paid = $expenses->where('paid_by',$member->id)->sum('amount');
            $balances[] = [
                'member_id'=>$member->id,
                'member_name'=>$member->name,
                'total_paid'=>$paid,
                'share'=>$round = round($share,2),
                'balance'=>round($paid - $share,2)
            ];
        }
        return response()->json([
            'group_id'=>$groupId,
            'group_name'=>$group->name,
            'total_expense'=>$total,
            'equal_share'=>round($share,2),
            'balances'=>$balances
        ]);
    }
}