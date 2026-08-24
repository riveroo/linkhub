<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_id',
        'store_name',
        'bio',
        'profile_photo',
        'cover_type',
        'cover_color',
        'cover_image',
        'header_alignment',
        'product_layout',
        'whatsapp_number',
        'source',
        'sources',
    ];

    protected $casts = [
        'sources' => 'array',
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

    public function socials(): HasMany
    {
        return $this->hasMany(LinkSocial::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(LinkCategory::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(LinkProduct::class)->orderBy('display_order', 'asc')->orderBy('id', 'asc');
    }
}
