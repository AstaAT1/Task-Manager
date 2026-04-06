<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;

#[Fillable(['title', 'description', 'assigned_to', 'created_by'])]
class Task extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_DONE = 'done';

    public function assignedTo(): BelongsToRelation
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsToRelation
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
