<?php

namespace App\Services;

use App\Repository\VisitorRepository;
use App\Services\Traits\CapturesItems;
use App\Services\Traits\CapturesVisitors;

class CheckInService{
    use CapturesVisitors;

    function checkInVisitor($data){
        // check if existing
        /** @var VisitorRepository */
        $repo = resolve(VisitorRepository::class);
        $visitor = $repo->getUsingIdNumber($data['id_number']);

        if($visitor != null){
            // exisiting
            // can be checked in?
            if($visitor->canCheckIn()){
                return $this->handleExistingVisitor($visitor, $data);
            }

            return 'This visitor is already checked in the site, and has not checked out today through the app';
        }

        return $this->handleNewVisitor($data);
    }
}
