<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'form_id', 'organization_id', 'contact_id', 'page_id', 'visitor_key',
        'payload', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }

    public function form(): BelongsTo { return $this->belongsTo(Form::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function page(): BelongsTo { return $this->belongsTo(Page::class); }
}