<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class ResultSet{
    public $page = 1;
    public $next_page = null;
    public $prev_page = null;
    public $max_pages = null;

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
    function __construct($query, $limit, $callable = null, $data = null)
    {
        $this->data = $data;

        if($query != null){
            $this->limit = $limit;

            // Total
            $this->total = $query->count();

            // paginate
            $page = intval(request()->get('page'));
            if($page == 0) $page = 1;

            $offset = ($page - 1) * $limit;
            $query->offset($offset)->limit($limit);

            $items = $query->get()->each(function($item) use($callable){
                if($callable != null){
                    $item = call_user_func($callable, $item);
                    return $item;
                }
            });

            $this->items = $items;

            // Pages
            $max_pages = intval(ceil($this->total / $this->limit));
            $this->max_pages = $max_pages != 0 ? $max_pages:1;
            $this->page = $page;
            if($page > 1) $this->prev_page = $page - 1;
            if($page < $this->max_pages) $this->next_page = $page + 1;

            // current result set
            if(count($items) > 0){
                // e.g for page 2, limit of 15, not last page, will be from 16 to 30
                $from = (($page - 1) * $limit) + 1;

                // if not last page
                if($page != $this->max_pages){
                    $to = $from + $limit - 1;
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
     */
    function isEmpty(){
        return count($this->items) == 0;
    }

    /**
     * Check if the result set has a previous page
     */
    function hasPreviousPage(){
        return $this->page > 1;
    }

    /**
     * Check if the result set has a next
     */
    function hasNextPage(){
        return $this->max_pages > $this->page;
    }
}
