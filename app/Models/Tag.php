<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (Tag $tag)
        {
            if (empty($tag->slug))
            {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function products(){ return $this->belongsToMany(Product::class); }
}
