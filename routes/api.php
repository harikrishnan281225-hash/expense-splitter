<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\BalanceController;
use App\Http\Controllers\Api\SettlementController;



Route::get('/groups', [GroupController::class, 'index']);
Route::post('/groups', [GroupController::class, 'store']);
Route::get('/groups/{id}', [GroupController::class, 'show']);

Route::post('/groups/{groupId}/members', [MemberController::class, 'store']);

Route::post('/groups/{groupId}/expenses', [ExpenseController::class, 'store']);
Route::get('/groups/{groupId}/expenses', [ExpenseController::class, 'index']);

Route::get('/groups/{groupId}/balances', [BalanceController::class, 'show']);
Route::get('/groups/{groupId}/settlements', [SettlementController::class, 'show']);
Route::delete('/groups/{groupId}/members/{memberId}', [MemberController::class, 'destroy']);
Route::delete('/groups/{groupId}/expenses/{expenseId}', [ExpenseController::class, 'destroy']);