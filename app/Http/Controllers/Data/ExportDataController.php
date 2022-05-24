<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportDataController extends Controller
{
    function __invoke(Request $request, $type)
    {
        $type = strtolower($type);
        $export = null;

        $time = now()->toDateTimeString();
        $time = str_replace(['-', ' ', ':'], '_', $time);

        if($type == 'visitors'){
            $export = $this->visitors();
        }

        if($export != null){
            $path = "exports/".$type."_".$time.".json";
            Storage::put($path, $export);
            return response()->file(storage_path('app/'.$path));
        }

        return back()->withErrors([
            'status' => 'Invalid export type: '.$type
        ]);
    }

    function visitors(){
        // Get all
        $visitors = Visitor::with('last_activity')->get();

        return json_encode($visitors);
    }

    function batchProcess($batch_size, $data, $callable){
        $max = count($data);
        $batches = ceil($max / $batch_size);

        for($i = 0; $i<$batches; $i++){
            $start = $i * $batch_size;
            $end = $start + $batch_size;

            if($end > $max){
                $end = $max;
            }

            for($j = $start; $j < $end; $j++){
                return $callable($data[$j]);
            }
        }
    }
}
