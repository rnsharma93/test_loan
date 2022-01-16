<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

class Application extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'tenure',
        'tenure_type',
        'interest_rate',
        'emi',
        'total_amount_to_paid',
        'total_interest_to_paid',
        'is_approve',
        'approved_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function totalPaid() {
        $amount = Schedule::where(['application_id'=>$this->id,'is_paid'=>1])->sum('amount');
        return $amount;
    }

    public function totalRemaining() {
        $amount = Schedule::where(['application_id'=>$this->id,'is_paid'=>0])->sum('amount');
        return $amount;
    }

    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
}
