<?php

namespace App\Http\Controllers;

use App\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function change(Request $request)
    {
        $request-> validate([
            'date' => ['required'],
            'available' => ['required']
        ]);

    $temp = DB::table('calendars')->where(['date'=> $request->date])->value('available');
    if(is_null($temp))
    {
        $new = new Calendar;
        $new->date = $request->date;
        $new->available = $request->available;
        $new->save();
    }
    else{
        if($temp == 1)
            $flag = 0;
        else
            $flag = 1;
        DB::table('calendars')->where(['date'=> $request->date])->update(['available'=>$flag]);
        }
    return response()->json(['message'=>'success!']);
    }

}
