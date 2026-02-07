<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SteeringDoc extends Model
{
    protected $fillable = [
        'steering_collection_id',
        'file_path',
        'content',
        'is_edited',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(SteeringCollection::class, 'steering_collection_id');
    }

    public function versions()
    {
        return $this->hasMany(SteeringDocVersion::class);
    }

    public function createVersion(string $changeType = 'updated', ?int $userId = null): void
    {
        // Only create version if content actually changed
        $lastVersion = $this->versions()->latest()->first();
        
        if (!$lastVersion || $lastVersion->content !== $this->content) {
            $this->versions()->create([
                'content' => $this->content,
                'change_type' => $changeType,
                'user_id' => $userId,
            ]);
        }
    }
}
