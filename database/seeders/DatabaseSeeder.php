<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // utente admin di test
        User::factory()->create([
            'name' => 'Alex',
            'email' => 'info@phitnetwork.com',
            'password' => Hash::make('password'),
        ]);

        // categorie
        $categories = [
            'E-books',
            'Courses',
            'Plugins',
            'Templates',
            'Assets',
        ];

        $catIds = [];
        foreach ($categories as $name) {
            $cat = Category::create(['name' => $name]);
            $catIds[$name] = $cat->id;
        }

        // tag
        $tags = [
            'laravel', 'php', 'javascript', 'design', 'seo',
            'vue', 'filament', 'starter-kit', 'ux', 'backend',
            'assets',
        ];

        $tagIds = [];
        foreach ($tags as $name) {
            $tag = Tag::create(['name' => $name]);
            $tagIds[$name] = $tag->id;
        }

        // prodotti
        $products = [
            [
                'name' => 'Laravel 12 Quickstart Guide',
                'description' => 'Manuale pratico con esempi passo-passo.',
                'image' => 'https://via.placeholder.com/640x480.png?text=Laravel+Guide',
                'category' => 'E-books',
                'tags' => ['laravel','php','starter-kit'],
            ],
            [
                'name' => 'UX Writing Essentials',
                'description' => 'Nozioni base per scrivere micro-testi efficaci.',
                'image' => 'https://via.placeholder.com/640x480.png?text=UX+Writing',
                'category' => 'E-books',
                'tags' => ['ux','design'],
            ],
            [
                'name' => 'Vue 3 Crash Course',
                'description' => 'Videolezioni introduttive al framework.',
                'image' => 'https://via.placeholder.com/640x480.png?text=Vue+3',
                'category' => 'Courses',
                'tags' => ['vue','javascript'],
            ],
            [
                'name' => 'SEO Fundamentals 2025',
                'description' => 'Come ottimizzare siti e contenuti per Google.',
                'image' => 'https://via.placeholder.com/640x480.png?text=SEO+Course',
                'category' => 'Courses',
                'tags' => ['seo'],
            ],
            [
                'name' => 'Filament Admin Widgets Pack',
                'description' => 'Estensioni per dashboard Filament.',
                'image' => 'https://via.placeholder.com/640x480.png?text=Filament+Widgets',
                'category' => 'Plugins',
                'tags' => ['filament','laravel'],
            ],
            [
                'name' => 'WP Security Enhancer',
                'description' => 'Plugin WordPress per hardening di base.',
                'image' => 'https://via.placeholder.com/640x480.png?text=WP+Security',
                'category' => 'Plugins',
                'tags' => ['php','backend'],
            ],
            [
                'name' => 'SaaS Landing Page Kit',
                'description' => 'Template responsive per startup SaaS.',
                'image' => 'https://via.placeholder.com/640x480.png?text=SaaS+Landing',
                'category' => 'Templates',
                'tags' => ['design','ux'],
            ],
            [
                'name' => 'Resume Template Pro',
                'description' => 'Curriculum in formato moderno.',
                'image' => 'https://via.placeholder.com/640x480.png?text=Resume+Template',
                'category' => 'Templates',
                'tags' => ['design'],
            ],
            [
                'name' => 'Icon Set 500+',
                'description' => 'Pack di icone SVG pronte per web app.',
                'image' => 'https://via.placeholder.com/640x480.png?text=Icon+Set',
                'category' => 'Assets',
                'tags' => ['design'],
            ],
            [
                'name' => 'Stock Video Bundle',
                'description' => 'Clips HD royalty free.',
                'image' => 'https://via.placeholder.com/640x480.png?text=Stock+Videos',
                'category' => 'Assets',
                'tags' => ['assets'],
            ],
        ];

        foreach ($products as $prod) {
            $p = Product::create([
                'category_id' => $catIds[$prod['category']] ?? null,
                'name' => $prod['name'],
                'description' => $prod['description'],
                'image' => $prod['image'],
            ]);

            $tagAttach = collect($prod['tags'])
                ->map(fn($t) => $tagIds[$t] ?? null)
                ->filter()
                ->all();

            $p->tags()->sync($tagAttach);
        }
    }
}
