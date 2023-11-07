<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Gcash extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'branch_id',
        'ewallet',
        'name',
        'phone',
        'amount',
        'image',
    ];


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'branch_id', 'branch_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
