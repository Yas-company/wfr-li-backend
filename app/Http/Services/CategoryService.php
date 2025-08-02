<?php

namespace App\Http\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class CategoryService
{
    public function storeCategory($data, $user)
    {

        if (! Gate::allows('canManageCategories', Category::class)) {
            return ['error' => __('messages.errors.unauthorized_category_creation')];
        }

     
        $data['supplier_id'] = $user->id;

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $data['image']->store('categories', 'public'); // path: storage/app/public/products
        }
        $category = Category::create([
            'name'        => $data['name'],
            'field_id'    => $data['field_id'],
            'supplier_id' => $data['supplier_id'],
            'image'       => $data['image'] ?? null,
        ]);

        return $category->load('field')->loadCount('products');
    }

    public function index($data, $user)
    {
        if (! Gate::allows('canManageCategories', Category::class)) {
            return ['error' => __('messages.errors.unauthorized_category_access')];
        }

        $query = Category::with('field')->withCount('products')
            ->where('supplier_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (isset($data['search']) && ! empty($data['search'])) {
            $searchTerm = $data['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name->en', 'like', "%{$searchTerm}%")
                    ->orWhere('name->ar', 'like', "%{$searchTerm}%");
            });
        }

        // Apply field filter
        if (isset($data['field_id']) && ! empty($data['field_id'])) {
            $query->where('field_id', $data['field_id']);
        }

        return $query->paginate(10);
    }

    public function show(Category $category)
    {

        if (! Gate::allows('canManageCategories', Category::class)) {
            return ['error' => __('messages.errors.unauthorized_category_access')];
        }
        if (! Gate::allows('ownCategory', $category)) {
            return ['error' => __('messages.errors.unauthorized_category_ownership')];
        }

        return $category->load('field')->loadCount('products');
    }

    public function update($data, Category $category)
    {
        if (! Gate::allows('canManageCategories', Category::class)) {
            return ['error' => __('messages.errors.unauthorized_category_update')];
        }
        if (! Gate::allows('ownCategory', $category)) {
            return ['error' => __('messages.errors.unauthorized_category_ownership')];
        }

        
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old image if it exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            // Store new image
            $data['image'] = $data['image']->store('categories', 'public');
        }
        $category->update($data);
        return $category->load('field')->loadCount('products');
    }

    public function destroy(Category $category)
    {
        if (! Gate::allows('canManageCategories', Category::class)) {
            return ['error' => __('messages.errors.unauthorized_category_delete')];
        }
        if (! Gate::allows('ownCategory', $category)) {
            return ['error' => __('messages.errors.unauthorized_category_ownership')];
        }
        if ($category->products()->exists()) {
            return ['error' => __('messages.errors.category_has_products')];
        }
        return $category->delete();
    }

    public function getCategoriesByField(int $field_id)
    {
        $categories = Category::with('field')->where('field_id', $field_id)->get();
        return $categories;
    }

    public function search($request)
    {
        $search = $request->search;

        $categories = Category::where(function($query) use ($search) {
            $query->where('name->en', 'like', "%{$search}%")
                  ->orWhere('name->ar', 'like', "%{$search}%");
            })->paginate(10);
        return $categories;
    }

}
