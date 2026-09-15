<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Expense extends Model {
    protected $fillable = ['group_id', 'paid_by', 'description', 'amount'];
    public function group() { return $this->belongsTo(Group::class); }
    public function payer() { return $this->belongsTo(Member::class, 'paid_by'); }
}