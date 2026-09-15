<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function store(Request $request, $groupId) {
        $request->validate(['name'=>'required|string|max:50']);
        $member = Member::create(['group_id'=>$groupId, 'name'=>$request->name]);
        return response()->json($member, 201);
    }
}