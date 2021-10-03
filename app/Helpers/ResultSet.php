<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class ResultSet{
    public $page = 1;
    public $next_page = null;
    public $prev_page = null;
    public $max_pages = null;

    public $next_page_url = null;
    public $prev_page_url = null;

    public $total = 0;
    public $limit = 0;

    public $items = [];
    public $from = 0;
    public $to = 0;

    public $data = null;

    /**
     * Create a new result set
     * @param Builder $query Query used to retrieve results
     * @param int $limit Results limit
     */
    function __construct($query, $limit = null, $callable = null, $data = null)
    {
        $this->data = $data;

        if($query != null){
            $this->limit = $limit;

            // Total
            $this->total = $query->count();

            // paginate
            $page = intval(request()->get('page'));
            if($page == 0) $page = 1;

            if($limit != null){
                $offset = ($page - 1) * $limit;
                $query->offset($offset)->limit($limit);
            }

            $items = $query->get()->each(function($item) use($callable){
                if($callable != null){
                    $item = call_user_func($callable, $item);
                    return $item;
                }
            });

            $this->items = $items;

            // Pages
            $max_pages = ($this->limit > 0) ? intval(ceil($this->total / $this->limit)) : 1;
            $this->max_pages = $max_pages != 0 ? $max_pages:1;
            $this->page = $page;
            if($page > 1) $this->prev_page = $page - 1;
            if($page < $this->max_pages) $this->next_page = $page + 1;

            // Page urls
            $this->prev_page_url = $this->pageUrl($this->prev_page ? $this->prev_page:$page);
            $this->next_page_url = $this->pageUrl($this->next_page ? $this->next_page:$page);

            // current result set
            if(count($items) > 0){
                // e.g for page 2, limit of 15, not last page, will be from 16 to 30
                $from = (($page - 1) * ($limit == null ? 0 : $limit)) + 1;

                // if not last page
                if($page != $this->max_pages){
                    $to = $from + ($limit == null ? count($items) : $limit) - 1;
                }else{
                    // e.g. last page (2) contains 10 items, will be from 16 to 25
                    $to = $from + count($items) - 1;
                }

                $this->from = $from;
                $this->to = $to;
            }
        }
    }

    /**
     * Get an empty resultset
     * @return ResultSet
     */
    static function empty($data = null){
        return new ResultSet(null, null, null, $data);
    }

    /**
     * Check if the result has no items
     * @return bool
     */
    function isEmpty(){
        return count($this->items) == 0;
    }

    /**
     * Check if the result set has a previous page
     * @return bool
     */
    function hasPreviousPage(){
        return $this->page > 1;
    }

    /**
     * Check if the result set has a next page
     * @return bool
     */
    function hasNextPage(){
        return $this->max_pages > $this->page;
    }

    /**
     * Generate url for a particular page
     * @param int $page
     * @return string
     */
    function pageUrl($page){
        $current = Route::current();
        $params = $current->parameters();

        return route($current->getName(),
            array_merge($params, request()->except('page'), ['page' => $page])
        );
    }

    /**
     * Generate url for next page
     * @return string
     */
    function nextPageUrl(){
        return $this->pageUrl($this->next_page);
    }

    /**
     * Generate url for prev page
     * @return string
     */
    function prevPageUrl(){
        return $this->pageUrl($this->prev_page);
    }
}
