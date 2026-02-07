<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPackage extends Model
{
    protected $fillable = [
        'user_id',
        'package_name',
        'version',
        'type',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function docs(): HasMany
    {
        return $this->hasMany(PackageDoc::class, 'package_name', 'package_name')
            ->where('user_id', $this->user_id);
    }
}
