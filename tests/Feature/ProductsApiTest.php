<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductsApiTest extends TestCase
{
    use RefreshDatabase; // migra lo schema pulito ad ogni test

    /** @test */
    #[Test] public function index_is_paginated_and_returns_data(): void
    {
        Product::factory()->count(15)->create();

        $res = $this->getJson('/api/products?per_page=10');

        $res->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10);
    }

    /** @test */
    #[Test] public function filters_by_name_category_and_tag(): void
    {
        $cat = Category::factory()->create(['name' => 'Courses']);
        $tag = Tag::factory()->create(['name' => 'laravel']);

        $p = Product::factory()->create([
            'name' => 'Laravel 12 Course',
            'category_id' => $cat->id,
        ]);
        $p->tags()->sync([$tag->id]);

        Product::factory()->count(3)->create(); // rumore

        $res = $this->getJson('/api/products?q=Laravel&category=courses&tag=laravel');

        $res->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Laravel 12 Course']);
    }

    /** @test */
    #[Test] public function can_store_update_and_delete_a_product(): void
    {
        $cat = Category::factory()->create();
        Tag::factory()->create(['name' => 'php']);
        Tag::factory()->create(['name' => 'backend']);

        $create = $this->postJson('/api/products', [
            'category_id' => $cat->id,
            'name'        => 'Digital Pack',
            'description' => 'Desc',
            'image'       => 'https://example.test/img.png',
            'tags'        => ['php', 'backend'],
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.name', 'Digital Pack');

        $id = $create->json('data.id');

        $update = $this->patchJson("/api/products/{$id}", [
            'name' => 'Digital Pack Pro',
            'tags' => [], // stacca tutti i tag
        ]);

        $update->assertOk()
            ->assertJsonPath('data.name', 'Digital Pack Pro')
            ->assertJsonCount(0, 'data.tags');

        $this->deleteJson("/api/products/{$id}")->assertNoContent();
    }

    /** @test */
    #[Test] public function store_validates_required_fields(): void
    {
        $res = $this->postJson('/api/products', [
            // manca name/description/image
            'category_id' => null,
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description', 'image']);
    }
}
