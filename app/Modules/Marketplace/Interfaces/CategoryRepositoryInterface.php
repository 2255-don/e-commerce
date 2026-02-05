<?php

namespace Modules\Marketplace\Interfaces;

use Modules\Marketplace\Entities\Category;

interface CategoryRepositoryInterface
{
    public function findById(string $id): ?Category;
    public function findBySlug(string $slug): ?Category;
    public function getAll();
    public function getRootCategories();
    public function getChildren(string $parentId);
}
