<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Helpers\ResultSet;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Screen;
use App\Models\Staff;
use App\Models\StaffLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffActivityController extends Controller
{
    function __invoke(Request $request)
    {
        $query = StaffLog::query();

        // for single account
        if($request->filled('staff')){
            $query->where('staff_id', $request->get('staff'));
        }

        // for single group
        if($request->filled('category')){
            $query->where('item', $request->get('category'));
        }

        // time
        if($request->filled('from') && !$request->filled('to')){
            $query->where('time', '>=', $request->get('from'));
        }else if($request->filled('to') && !$request->filled('from')){
            $query->where('time', '<=', $request->get('to'));
        }else if($request->filled('to') && $request->filled('from')){
            $query->where('time', '<=', $request->get('to'))
                ->where('time', '>=', $request->get('from'));
        }

        // Ordering
        $order = $request->get('order', 'recent');

        if($order == 'oldest') $query->orderBy('time', 'asc');
        else $query->orderBy('time', 'desc');

        // Include staff account
        $query->with('account');

        $result = new ResultSet($query, 15, function ($log){
            $time = Carbon::createFromTimeString($log->time);
            if($time->isToday()){
                $log->time = "Today";
            }else if($time->isYesterday()){
                $log->time = "Yesterday";
            }else{
                $log->time = substr($time->monthName, 0, 3)." ".$time->day.", ".$time->year;
            }

            $log->time .= ' '.($time->hour > 12 ? ($time->hour - 12):$time->hour).':'.($time->minute < 10 ? '0':'').$time->minute.' '.(($time->hour > 12) ? 'PM': ($time->hour == 12 ? 'Noon':'AM'));

            // link
            $log->link_text = 'View '.$log->item;
        });

        return response()->view('admin.staff.activity', [
            'result' => $result
        ]);
    }

    function redirect($item, $id){

        if($item == StaffLog::ITEM_AGENT){
            return redirect()->route('admin.agents.single', $id);
        }

        if($item == StaffLog::ITEM_ADVERT){
            return redirect()->route('admin.adverts.single', $id);
        }

        if($item == StaffLog::ITEM_CATEGORY){
            return redirect()->route('admin.categories');
        }

        if($item == StaffLog::ITEM_USER){
            $client = User::where('id', $id)->first();

            if(!$client){
                return back()->withErrors(['status' => 'Client account no longer exists']);
            }

            return redirect()->route('admin.clients.single', $client->email);
        }

        if($item == StaffLog::ITEM_STAFF){
            $staff = Staff::where('id', $id)->first();

            if(!$staff){
                return back()->withErrors(['status' => 'Staff account no longer exists']);
            }

            return redirect()->route('admin.staff.single', $staff->username);
        }

        if($item == StaffLog::ITEM_INVOICE){
            $invoice = Invoice::where('id', $id)->first();

            if(!$invoice){
                return back()->withErrors(['status' => 'Invoice no longer exists']);
            }

            return redirect()->route('admin.clients.invoices.single', $invoice->number);
        }

        if($item == StaffLog::ITEM_PACKAGE){
            return redirect()->route('admin.packages.manage', $id);
        }

        if($item == StaffLog::ITEM_SCREEN){
            $screen = Screen::where('id', $id)->first();

            if(!$screen){
                return back()->withErrors(['status' => 'The screen no longer exists']);
            }

            return redirect()->route('admin.screens.single', $screen->slug);
        }

        return back();
    }
}
