<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

// Shared behaviour for the admin datatables: whitelisted column sorting
// (?sort=column&dir=asc|desc) and a clamped page size (?per_page=10|20|50).
trait WithDataTable
{
    private const PER_PAGE_OPTIONS = [10, 20, 50];

    private function dataTable(
        Request $request,
        Builder $query,
        array $sortable,
        ?callable $defaultOrder = null
    ): LengthAwarePaginator {
        $sort = $request->input('sort');

        if (in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $request->input('dir') === 'desc' ? 'desc' : 'asc');
        } elseif ($defaultOrder) {
            $defaultOrder($query);
        }

        $perPage = (int) $request->input('per_page');

        return $query
            ->paginate(in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 20)
            ->withQueryString();
    }
}
