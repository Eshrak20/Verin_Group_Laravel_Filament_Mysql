<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait FilterAndPaginate
{
    /**
     * Scope a query to handle dynamic filtering, searching, sorting, and pagination.
     */
    protected function scopeFilterSortPaginate(Builder $query, Request $request, array $searchableFields = ['name']): LengthAwarePaginator
    {
        // 1. 🔍 Search Filter (e.g., ?search=phone)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function (Builder $subQuery) use ($searchableFields, $searchTerm) {
                foreach ($searchableFields as $field) {
                    $subQuery->orWhere($field, 'LIKE', "%{$searchTerm}%");
                }
            });
        }

        // 2. 🎛️ Exact Matches Filtering (e.g., ?category_id=2&sub_category_id=5&brand_id=1)
        $filterableInputs = $request->only(['category_id', 'sub_category_id', 'brand_id', 'status']);
        foreach ($filterableInputs as $key => $value) {
            if ($value !== null && $value !== '') {
                $query->where($key, $value);
            }
        }

        // 3. ↕️ Price & Alphabetical Sorting (e.g., ?sort=price_asc, ?sort=name_desc)
        if ($request->filled('sort')) {
            switch ($request->input('sort')) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'name_asc':
                default:
                    $query->orderBy('name', 'asc');
                    break;
            }
        } else {
            // Default Sorting: A-Z
            $query->orderBy('name', 'asc');
        }

        // 4. 📄 Dynamic Pagination (e.g., ?per_page=15)
        $perPage = (int) $request->input('per_page', 10); // Defaults to 10 records per page

        return $query->paginate($perPage)->withQueryString();
    }
}