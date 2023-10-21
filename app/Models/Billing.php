<?php

namespace App\Models;

use App\Filament\Resources\LoanResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Request;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class Billing extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'applicant_id',
        'branch_id',
        'user_id',
        'transaction_number',
        'cashier',
        'amount',
        'amountpdf',
        'signature',
        'billing_status',
        'is_processed',
        'payment_type',
        'interests',
        'applicant_user_id'
    ];

    public function Applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function isRemitted()
    {
        return $this->billing_status === 'remitted';
    }
    public function isNot_recieved()
    {
        return $this->billing_status === 'not_recieved';
    }
}
