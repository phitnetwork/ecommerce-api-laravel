<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (Category $cat)
        {
            if (empty($cat->slug))
            {
                $cat->slug = Str::slug($cat->name);
            }
        });
    }

    public function products(){ return $this->hasMany(Product::class); }
}
