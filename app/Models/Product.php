<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'description', 'image',
    ];

    protected $casts = [
        'category_id' => 'integer',
    ];
    
    public function category(){ return $this->belongsTo(Category::class); }
    public function tags(){ return $this->belongsToMany(Tag::class); }

    public function scopeFilter(Builder $q, array $filters): Builder
    {
        $q->when($filters['q'] ?? null, function (Builder $qq, $term)
        {
            $qq->where('name', 'like', "%{$term}%");
        });

        $q->when($filters['category'] ?? null, function (Builder $qq, $cat)
        {
            $qq->whereHas('category', function (Builder $c) use ($cat)
            {
                $c->where('id', $cat)->orWhere('slug', $cat);
            });
        });

        $q->when($filters['tag'] ?? null, function (Builder $qq, $tag)
        {
            $qq->whereHas('tags', function (Builder $t) use ($tag)
            {
                $t->where('id', $tag)->orWhere('slug', $tag);
            });
        });

        return $q;
    }
}
