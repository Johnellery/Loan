<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
class Bike extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [ 'image',
                            'branch_id',
                            'user_id',
                            'category_id',
                            'name',
                            'brand',
                            'price',
                            'rate',
                            'description',
                            'status',
                            'interest',
                            'color',
                            'down',
                            'is_available',
                        ];

    public function category(): BelongsTo
    {
        return $this->BelongsTo(Category::class);
    }
    public function branch(): BelongsTo
    {
        return $this->BelongsTo(Branch::class);
    }
    public function applicant(): HasMany
    {
        return $this->HasMany(Applicant::class);
    }
    public function billing(): HasMany
    {
        return $this->HasMany(Billing::class);
    }
    public function isApproved()
    {
        return $this->status === 'approved';
    }
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
    public function isAvailable()
    {
        return $this->is_available === 'available';
    }
    public function isUnavailable()
    {
        return $this->is_available === 'unavailable';
    }
    public function getThumbnailUrl()
    {
        $isUrl = str_contains($this->image, 'http');

        return ($isUrl) ? $this->image : Storage::disk('public')->url($this->image);
    }
}
