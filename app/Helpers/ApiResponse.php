<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class ApiResponse{
    public $data = null;
    public $success = false;
    public $message = null;

    /**
     * Create a new result set
     * @param Builder $query Query used to retrieve results
     * @param int $limit Results limit
     */
    function __construct($response)
    {
        $this->data = $response['data'];
        $this->success = $response['success'];
        $this->message = $response['message'];
    }

    /**
     * Check if response was successful
     * @return bool
     */
    function wasSuccessful(){
        return $this->success;
    }

    /**
     * Get result from data
     * @return array
     */
    function getResult(){
        return $this->data['result'] ?? [];
    }

    /**
     * Get items from data
     * @return array
     */
    function getItems(){
        return $this->getResult()['items'] ?? [];
    }
}
