<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_page_id',
        'name',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(LinkPage::class, 'link_page_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(LinkProduct::class, 'category_id');
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(LinkSubcategory::class, 'category_id')->orderBy('id', 'asc');
    }
}
