<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'url',
        'status',
        'clicks',
    ];

    /**
     * Get the page configuration associated with the link.
     */
    public function page()
    {
        return $this->hasOne(LinkPage::class);
    }

    /**
     * Scope a query to search links by title, slug, or url.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('slug', 'like', "%{$term}%")
              ->orWhere('url', 'like', "%{$term}%");
        });
    }

    /**
     * Scope a query to filter links by status.
     */
    public function scopeFilterStatus($query, ?string $status)
    {
        if (empty($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }
}
