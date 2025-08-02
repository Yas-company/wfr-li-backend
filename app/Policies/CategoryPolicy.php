<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user owns the category.
     */
    public function ownCategory(User $user, Category $category): bool
    {
        return $category->supplier_id === $user->id;
    }

    /**
     * Check if user can manage categories.
     */
    public function canManageCategories(User $user): bool
    {
        // Debug information
        logger()->info('CategoryPolicy::canManageCategories called', [
            'user_id' => $user->id ?? 'null',
            'user_role' => $user->role ?? 'null',
            'user_status' => $user->status ?? 'null',
            'isSupplier' => $user->isSupplier(),
            'isApproved' => $user->isApproved(),
            'final_result' => $user && $user->isSupplier() && $user->isApproved(),
        ]);

        return $user && $user->isSupplier() && $user->isApproved();
    }
}
