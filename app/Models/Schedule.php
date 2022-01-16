<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'application_id',
        'date',
        'amount',
        'is_paid',
        'paid_at'
    ];

    public function application(){
        return $this->belongsTo(Aplication::class);
    }
}
