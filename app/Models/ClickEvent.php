<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClickEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_id',
        'source_id',
        'product_id',
        'event_type',
        'visitor_id',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    // Event type constants
    const PAGE_VIEW     = 'page_view';
    const PRODUCT_CLICK = 'product_click';

    /**
     * The link this event belongs to.
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

    /**
     * The source (traffic origin) of this event — nullable (direct traffic).
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * The product that was clicked — nullable (page views don't have products).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(LinkProduct::class, 'product_id');
    }
}
