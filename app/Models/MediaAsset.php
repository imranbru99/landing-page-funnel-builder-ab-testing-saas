<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'uploaded_by', 'disk', 'path', 'filename',
        'mime_type', 'size', 'alt_text', 'meta',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}