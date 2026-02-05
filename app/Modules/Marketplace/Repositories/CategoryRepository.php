<?php

namespace Modules\Marketplace\Repositories;

use Modules\Marketplace\Entities\Category;
use Modules\Marketplace\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function findById(string $id): ?Category
    {
        return Category::find($id);
    }
    
    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }
    
    public function getAll()
    {
        return Category::with('children')->orderBy('name')->get();
    }
    
    public function getRootCategories()
    {
        return Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();
    }
    
    public function getChildren(string $parentId)
    {
        return Category::where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
    }
}
