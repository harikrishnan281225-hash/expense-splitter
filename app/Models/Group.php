<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Group extends Model {
    protected $fillable = ['name'];
    public function members() { return $this->hasMany(Member::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
}