<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) min(max((int) $request->integer('per_page', 12), 1), 100);

        $products = Product::query()
            ->with(['category:id,name,slug', 'tags:id,name,slug'])
            ->filter($request->only(['q', 'category', 'tag']))
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $product = DB::transaction(function () use ($data) {
            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $product = Product::create($data);

            if (!empty($tags)) {
                $product->tags()->sync($this->resolveTagIds($tags));
            }

            return $product;
        });

        return (new ProductResource($product->load(['category','tags'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Product $product)
    {
        $product->load(['category:id,name,slug', 'tags:id,name,slug']);
        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        DB::transaction(function () use ($product, $data)
        {
            $tags = $data['tags'] ?? null;
            unset($data['tags']);

            $product->update($data);

            // [] → stacca tutti i tag; null → lascia invariato
            if (!is_null($tags))
            {
                $tagIds = $this->resolveTagIds($tags);
                $product->tags()->sync($tagIds);
            }
        });

        return new ProductResource($product->load(['category', 'tags']));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    protected function resolveTagIds(array $incoming): array
    {
        $ids = [];

        foreach ($incoming as $value) {
            if (is_numeric($value)) {
                $ids[] = (int) $value;
                continue;
            }

            $value = trim((string) $value);
            $slug  = Str::slug($value);

            $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $value]);
            $ids[] = $tag->id;
        }

        return array_values(array_unique($ids));
    }
}