<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'low',
        'rate',
        'high',
        'rate1',
        'status',
    ];

    public function isApproved()
    {
        return $this->status === 'active';
    }
    public function isRejected()
    {
        return $this->status === 'deactivated';
    }
}
