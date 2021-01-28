<?php

namespace App\Http\Controllers;

use App\Post;
use App\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validator = $request->validate([
            'comment' => ['required', 'string', 'max:16777215']
        ]);
        $comment = new Comment([
            "post_id" =>  $post->id,
            "comment" =>  $request['comment'],
        ]);
        $comment->user_id = $request->user()->id;
        $comment->save();

        $comment->user = $comment->user()->first();
        $comment->subcomments = [];
        return response()->json($comment, 200);
    }


    public function update(Request $request, Comment $comment)
    {
        if (!$request->user()->ownsComment($comment)) {
            return Response('Unauthorized. You cannot edit this comment', 403);
        }
        $validator = $request->validate([
            'post_id' => ['required', 'unique:posts'],
            'comment' => ['required', 'string', 'max:16777215']
        ]);
        $comment->fill($validator);
        $comment->user_id = $request->user()->id;
        $comment->save();
        return Response('Comment updated successfully');
    }

    public function destroy(Request $request, Post $post, Comment $comment)
    {
        $user = $request->user();
        $UNAUTHORIZED = !$user->ownsPost($post) && !$user->ownsComment($comment);
        if ($UNAUTHORIZED) {
            return Response('Unauthorized. You cannot delete this comment', 403);
        }
        $comment->delete();
        return response()->json('Comment deleted successfully');
    }
}
