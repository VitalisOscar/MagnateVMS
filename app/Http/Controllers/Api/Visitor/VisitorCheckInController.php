<?php

namespace App\Http\Controllers\Api\Visitor;

use App\Http\Controllers\Controller;
use App\Repository\VisitorRepository;
use App\Services\CheckInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitorCheckInController extends Controller
{
    function __invoke(Request $request, CheckInService $checkIn, VisitorRepository $repo)
    {

        // for new and saved visitors
        $rules = [
            'reason' => 'required|string',
            'staff_id' => 'required|exists:staff,id',
            'car_registration' => 'nullable',
            'card_number' => 'nullable',
            'signature' => 'required',
            'items_in' => 'nullable|string',
            'id_number' => 'required|regex:/([0-9]){7,8}/',
        ];

	$visitor = $repo->getUsingIdNumber($request->get('id_number'));

        if($visitor == null){
            // new visitor
            $rules = array_merge($rules, [
                'name' => 'required|string',
                'phone' => 'required|regex:/0([0-9]){9}/',
		        'from' => 'required|string',
                'id_photo' => 'required', // should be image
            ]);
        }

        $validator = Validator::make(array_merge($request->post(), $request->file()), $rules);

        if($validator->fails()){
            return $this->json->errors($validator->errors()->all());
        }

        // save
        $result = $checkIn->checkInVisitor($request->post());

        if(is_bool($result) && $result){
            return $this->json->success('Visitor has been checked in and can proceed');
        }

        return $this->json->error($result);
    }
}
