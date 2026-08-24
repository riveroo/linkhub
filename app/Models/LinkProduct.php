<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_page_id',
        'category_id',
        'name',
        'description',
        'image_url',
        'product_url',
        'status',
        'display_order',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(LinkPage::class, 'link_page_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LinkCategory::class, 'category_id');
    }

    public function clickEvents()
    {
        return $this->hasMany(ClickEvent::class, 'product_id');
    }
}
