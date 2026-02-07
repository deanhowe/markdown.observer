<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SteeringDocVersion extends Model
{
    protected $fillable = ['steering_doc_id', 'content', 'change_type', 'user_id'];

    public function steeringDoc(): BelongsTo
    {
        return $this->belongsTo(SteeringDoc::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
