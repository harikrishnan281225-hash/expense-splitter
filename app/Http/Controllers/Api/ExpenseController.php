<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Member;
use App\Models\Group;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(Request $request, $groupId) {
        $request->validate([
            'paid_by'=>'required|exists:members,id',
            'description'=>'required|string',
            'amount'=>'required|numeric|min:0.01'
        ]);
        $expense = Expense::create([
            'group_id'=>$groupId,
            'paid_by'=>$request->paid_by,
            'description'=>$request->description,
            'amount'=>$request->amount
        ]);
        return response()->json($expense, 201);
    }

    public function index($groupId) {
        return Expense::where('group_id',$groupId)->with('payer')->get();
    }

    public function destroy($groupId, $expenseId)
    {
        $expense = Expense::where('group_id', $groupId)
                    ->where('id', $expenseId)
                    ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found in this group'], 404);
        }

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully'], 200);
    }

    public function update(Request $request, $groupId, $expenseId)
    {
        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $expense = Expense::where('group_id', $groupId)
                    ->where('id', $expenseId)
                    ->first();

        if (!$expense) {
            return response()->json(['message' => 'Expense not found in this group'], 404);
        }

        $request->validate([
            'description' => 'sometimes|string|max:100',
            'amount' => 'sometimes|numeric|min:0.01',
            'paid_by' => 'sometimes|exists:members,id'
        ]);

        $expense->update($request->only(['description', 'amount', 'paid_by']));

        return response()->json([
            'message' => 'Expense updated successfully',
            'expense' => $expense
        ], 200);
    }
}