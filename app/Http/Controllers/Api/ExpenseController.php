<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Member;
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
}