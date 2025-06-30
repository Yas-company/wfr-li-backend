<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    public function storeCategory($request)
    {
        $user = Auth::user();
        
        if (!$this->canManageCategories($user)) {

            return ['error' => 'Only approved suppliers can add categories.'];
        }

        $data = $request->validated();
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

    public function index()
    {
        $user = Auth::user();
        if (!$this->canManageCategories($user)) {
            return ['error' => 'Only approved suppliers can view categories.'];
        }
            $categories = Category::with('field')->withCount('products')->where('supplier_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            //->get();     
   
        return $categories; 
    }

    public function show(Category $category)
    {
        $user = Auth::user();
        if (!$this->canManageCategories($user)) {
            return ['error' => 'Only approved suppliers can view categories.'];
        }
        if (!$this->ownsCategory($user, $category)) {
            return ['error' => 'You can only view your own categories.'];
        }
        return $category->load('field')->loadCount('products');
    }

    public function update($request, Category $category)
    {
        $user = Auth::user();
        if (!$this->canManageCategories($user)) {
            return ['error' => 'Only approved suppliers can update categories.'];
        }
        if (!$this->ownsCategory($user, $category)) {
            return ['error' => 'You can only update your own categories.'];
        }
        $data = $request->validated();
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
        $user = Auth::user();
        if (!$this->canManageCategories($user)) {
            return ['error' => 'Only approved suppliers can delete categories.'];
        }
        if (!$this->ownsCategory($user, $category)) {
            return ['error' => 'You can only delete your own categories.'];
        }
        if ($category->products()->exists()) {
            return ['error' => 'Cannot delete category. It has associated products.'];
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

    /**
     * Check if user can manage categories.
     */
    private function canManageCategories($user): bool
    {
        return $user && $user->isSupplier() && $user->isApproved();
    }

    /**
     * Check if user owns the category.
     */
    private function ownsCategory($user, Category $category): bool
    {
        return $category->supplier_id === $user->id;
    }
}