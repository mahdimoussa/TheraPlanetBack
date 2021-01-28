<?php

namespace App\Http\Controllers;

use App\appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AppointmentController extends Controller
{
    public function changeAppointment(Request $request)
    {
        $request-> validate([
            'date' => ['required'],
            'user_id' => ['required']
        ]);
    $appoint_id = DB::table('calendars')->where(['date'=> $request->date])->value('id');

    $temp = DB::table('appointments')->where(['user_id'=> $request->user_id,'appoint_id'=> $appoint_id])->get();
    if(is_null($temp))
    {
        $new = new appointment();
        $new->user_id = $request->user_id;
        $new->appoint_id = $appoint_id;
        $new->save();
    }
    else{
        DB::table('appointments')->where(['user_id'=> $request->user_id,'appoint_id'=> $appoint_id])->delete();
        }
    return response()->json(['message'=>'success!']);
    }
}
