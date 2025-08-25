<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(), // crea una category se non la passi tu
            'name'        => $this->faker->unique()->words(3, true),
            'description' => $this->faker->paragraphs(2, true),
            'image'       => $this->faker->imageUrl(640, 480, 'tech'),
        ];
    }
}
