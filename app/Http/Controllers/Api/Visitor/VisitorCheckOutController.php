<?php

namespace App\Http\Controllers\Api\Visitor;

use App\Http\Controllers\Controller;
use App\Services\CheckOutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitorCheckOutController extends Controller
{
    function __invoke(Request $request, CheckOutService $checkOut)
    {
         $rules = [
            'id_number' => 'required|regex:/([0-9]){7,8}/',
            'items_out' => 'nullable|string',
        ];

        $validator = Validator::make(array_merge($request->post(), $request->file()), $rules);

        if($validator->fails()){
            return $this->json->errors($validator->errors()->all());
        }

        // save
        $result = $checkOut->checkOutVisitor($validator->validated());

        if(is_bool($result) && $result){
            return $this->json->success('Visitor has been checked out and can proceed out of the site');
        }

        return $this->json->error($result);
    }
}
