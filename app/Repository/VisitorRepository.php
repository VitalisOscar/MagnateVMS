<?php

namespace App\Repository;

use App\Models\Visitor;

class VisitorRepository{
    /**
     * find a visitor using ID number
     * @param string $id_number Visitor's id number
     * @return null|Visitor
     */
    function getUsingIdNumber($id_number){
        // get existing visitor
        return Visitor::/*withTrashed()->*/where('id_number', $id_number)->first();
    }

    /**
     * find a visitor using ID (database generated id)
     * @param string $id Visitor's table id
     * @return null|Visitor
     */
    function getUsingId($id){
        // get existing visitor
        return Visitor::/*withTrashed()->*/where('id', $id)->first();
    }
}
