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
}
