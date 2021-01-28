<?php

namespace App\Http\Controllers;

use App\Post;
use App\Saved;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SavedController extends Controller
{
    public function store(Request $request)
    {
        $user_id = $request->user()->id;
        $post_id = $request['post_id'];
        $saved_posts = Saved::where('user_id', $user_id)->where('post_id', $post_id)->get();
        if (count($saved_posts) > 0) {
            $this->destroy($saved_posts[0]);
            return response()->json("Post was unsaved successfully", 200);
        } else {
            Saved::create([
                "user_id" => $user_id,
                "post_id" =>   $post_id
            ]);

            return response()->json("Post was saved successfully", 200);
        }
    }

    public function destroy(Saved $saved)
    {
        $saved->delete();
        return response()->json("Post was unsaved successfully", 200);
    }

    public function index(Request $request){
        $saves = Saved::where('user_id','=',$request->user()->id)->get();
        foreach ($saves as $s){
            $s->post = Post::find($s->post_id);
            $comments = $s->post->comments()->count();
            // foreach ($comments as $comment) {
            //     $comment->subcomments = $comment->subcomments();
            // }
            $s->post->comments_count = $comments;
            $s->post->user = $s->post->therapist()->first();
//             Like::where('post_id', $s->post->id)->get()->count();
            $s->post->likes_count = $s->post->likes()->count();
            $s->post->saved = $s->post->saves()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
            $s->post->liked = $s->post->likes()->where('user_id', $request->user()->id)->count()== 0 ? false : true;
        }
        return $saves;
    }
}
