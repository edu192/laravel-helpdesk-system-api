<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class DepartmentController extends Controller
{
    public function __invoke(Request $request)
    {
        $departments = QueryBuilder::for(Department::class)
            ->allowedFilters(['name', 'id'])
            ->get();
        return response()->json([
            'departments' => $departments
        ]);
    }
}
