<?php

namespace App\Http\Controllers;

use App\Like;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request)
    {
        $user_id = $request->user()->id;
        $post_id = $request['post_id'];

        $likes = Like::where('user_id', $user_id)->where('post_id', $post_id)->get();
        if(count($likes) > 0) {
            $this->destroy($likes[0]);

        return response()->json("POst was unliked",200);
        } else {
            Like::create([
                "user_id" =>$user_id,
                 "post_id"=>$post_id
                 ]);
                 return response()->json("POst was liked",200);

        }
    }

    public function destroy(Like $like)
    {
        $like->delete();
        return response()->json("Like was removed successfully",200);
    }
}
