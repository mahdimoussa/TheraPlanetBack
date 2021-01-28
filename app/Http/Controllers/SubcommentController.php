<?php

namespace App\Http\Controllers;

use App\Post;
use App\Comment;
use App\Subcomment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubcommentController extends Controller
{
    public function store(Request $request, Post $post, Comment $comment)
    {
        $validator = $request->validate([
            'subcomment' => ['required', 'string', 'max:16777215']
        ]);

        $subcomment = new Subcomment([
            "comment_id" => $comment->id,
            "subcomment" =>  $request['subcomment'],
        ]);

        $subcomment->user_id = $request->user()->id;
        $subcomment->save();

        $subcomment->user = $comment->user()->first();


        return response()->json($subcomment, 200);
    }

    public function update(Request $request, Post $post, Comment $comment, Subcomment $subcomment)
    {
        if (!$request->user()->ownsSubcomment($subcomment)) {
            return Response('Unauthorized. You cannot edit this comment', 403);
        }
        $validator = $request->validate([
            'comment_id' => ['required', 'unique:comments'],
            'subcomment' => ['required', 'string', 'max:16777215']
        ]);
        $subcomment->fill($validator);
        $subcomment->user_id = $request->user()->id;
        $subcomment->save();
        return Response('Comment updated successfully');
    }

    public function destroy(Request $request, Post $post, Comment $comment, Subcomment $subcomment)
    {
        $user = $request->user();
        $unauthorized = !$user->ownsPost($post) && !$user->ownsComment($comment) && !$user->ownsSubcomment($subcomment);
        if ($unauthorized) {
            return response('Unauthorized. You cannot delete this comment', 403);
        }
        $subcomment->delete();
        return response()->json('Comment deleted successfully');
    }
}
