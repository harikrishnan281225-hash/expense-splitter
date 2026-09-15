<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Group;

class SettlementController extends Controller
{
    public function show($groupId) {
        $group = Group::with(['members','expenses'])->findOrFail($groupId);
        $members = $group->members;
        $expenses = $group->expenses;
        
        if($members->count() == 0) return response()->json(['message'=>'No members'], 400);
        
        $total = $expenses->sum('amount');
        $share = $members->count() > 0 ? $total / $members->count() : 0;
        
        // Calculate balances
        $balances = [];
        foreach($members as $m) {
            $paid = $expenses->where('paid_by', $m->id)->sum('amount');
            $balances[$m->id] = [
                'id' => $m->id,
                'name' => $m->name,
                'balance' => round($paid - $share, 2)
            ];
        }

        // Settlement logic - minimize transactions
        $creditors = array_filter($balances, fn($b) => $b['balance'] > 0);
        $debtors = array_filter($balances, fn($b) => $b['balance'] < 0);
        
        // Sort: creditors desc, debtors asc (most negative first)
        usort($creditors, fn($a,$b) => $b['balance'] <=> $a['balance']);
        usort($debtors, fn($a,$b) => $a['balance'] <=> $b['balance']);
        
        $creditors = array_values($creditors);
        $debtors = array_values($debtors);
        
        $transactions = [];
        $i = 0; $j = 0;
        while($i < count($creditors) && $j < count($debtors)) {
            $credit = $creditors[$i];
            $debit = $debtors[$j];
            
            $amount = min($credit['balance'], abs($debit['balance']));
            if($amount > 0.01) {
                $transactions[] = [
                    'from' => $debit['name'],
                    'from_id' => $debit['id'],
                    'to' => $credit['name'],
                    'to_id' => $credit['id'],
                    'amount' => round($amount, 2)
                ];
                $creditors[$i]['balance'] -= $amount;
                $debtors[$j]['balance'] += $amount;
            }
            if($creditors[$i]['balance'] < 0.01) $i++;
            if(abs($debtors[$j]['balance']) < 0.01) $j++;
        }

        return response()->json([
            'group_id' => $groupId,
            'group_name' => $group->name,
            'total_expense' => $total,
            'equal_share' => round($share,2),
            'balances' => array_values($balances),
            'settlements' => $transactions,
            'message' => count($transactions).' transaction(s) needed to settle'
        ]);
    }
}
