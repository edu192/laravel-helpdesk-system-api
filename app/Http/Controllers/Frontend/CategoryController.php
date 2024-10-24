<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    public function __invoke(\Request $request)
    {
        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters(['name', 'id', 'department_id'])
            ->get();
        return response()->json([
            'categories' => $categories
        ]);
    }
}
