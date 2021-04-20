<?php

namespace App\Helper;

use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/*
 * This class used to paginate normal collections
 * REASON FOR EXISTENCE: Laravel supports pagination of eloquent collections only.
 */
class CollectionHelper
{
    /**
     * @param Collection $results
     * @param $pageSize
     * @return LengthAwarePaginator
     */
    public static function paginate(Collection $results, $pageSize): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage('page');

        $total = $results->count();

        return self::paginator($results->forPage($page, $pageSize)->values(), $total, $pageSize, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param Collection $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return LengthAwarePaginator
     */
    protected static function paginator(Collection $items, $total, $perPage, $currentPage, $options): LengthAwarePaginator
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items',
            'total',
            'perPage',
            'currentPage',
            'options'
        ));
    }
}
