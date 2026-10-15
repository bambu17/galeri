<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'image_path'])]
class Photo extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
