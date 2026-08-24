<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkSocial extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_page_id',
        'platform',
        'url',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(LinkPage::class, 'link_page_id');
    }
}
