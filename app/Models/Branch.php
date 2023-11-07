<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [ 'name',
                            'unit',
                            'barangay',
                            'city',
                            'province',
                            'zip',
                        ];

    public function bike(): HasMany
    {
        return $this->HasMany(Bike::class);
    }
    public function gcash(): HasMany
    {
        return $this->HasMany(Gcash::class);
    }
    public function user(): BelongsToMany
    {
        return $this->BelongsToMany(User::class);
    }
    public function applicant(): HasMany
    {
        return $this->HasMany(Applicant::class);
    }
    public function Billing(): HasMany
    {
        return $this->HasMany(Billing::class);
    }
    public function create()
{
    $branches = Branch::all(); // Fetch all branches from the database
    return view('auth.register', compact('branches'));
}
}
