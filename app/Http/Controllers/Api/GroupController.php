<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index() { return Group::all(); }
    public function store(Request $request) {
        $request->validate(['name'=>'required|string|max:100']);
        $group = Group::create(['name'=>$request->name]);
        return response()->json($group, 201);
    }
    public function show($id) {
        return Group::with('members','expenses')->findOrFail($id);
    }
}