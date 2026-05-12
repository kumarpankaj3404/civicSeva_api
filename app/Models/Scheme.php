<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'ministry',
        'benefits',
        'eligibility_rules',
        'required_documents',
        'application_url',
        'tags',
        'is_active',
        'indexed_at',
    ];

    protected function casts(): array
    {
        return [
            'benefits'           => 'array',
            'eligibility_rules'  => 'array',
            'required_documents' => 'array',
            'tags'               => 'array',
            'is_active'          => 'boolean',
            'indexed_at'         => 'datetime',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%")
              ->orWhere('tags', 'LIKE', "%{$term}%");
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'scheme_id');
    }
}
