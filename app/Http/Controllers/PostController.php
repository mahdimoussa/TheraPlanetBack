<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\Comment;
use App\Http\Controllers\Controller;
use App\Tags;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\DocBlock\Tag;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $posts = Post::orderBy('created_at','desc')->paginate(5);
        foreach ($posts as $post) {
            $comments = $post->comments()->count();
            // foreach ($comments as $comment) {
            //     $comment->subcomments = $comment->subcomments();
            // }
            $post->tags = $post->tags()->get();
            $post->comments_count = $comments;
            $post->user = $post->therapist()->first();
            // Like::where('post_id', $post->id)->get()->count();
            $post->likes_count = $post->likes()->count();
            $post->tag = $post->tags();
            $post->saved = $post->saves()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
            $post->liked = $post->likes()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
        }
        return $posts;
    }

    public function show(Post $post)
    {

//        $post = Post::with('tags')->findOrFail($)
        $post->tags = $post->tags()->get();
        $comments = $post->comments()->get();
        foreach ($comments as $comment) {
            $comment->comments = $comment->subcomments()->get();
            foreach ($comment->comments as $c) {
                $c->user = $c->user()->first();
                $c->comment = $c->subcomment;
            }

            $comment->user = $comment->user()->first();
        }
        $post->comments = $comments;
        $post->user = $post->therapist()->first();
        $post->likes = $post->likes()->get();
        $post->saved = $post->saves();

        return $post;
    }

    public function store(Request $request)
    {
        if (!$request->user()->is('Therapist')) {
            return response('You cannot create a post', 403);
        }
        $caption = $request['caption'];

        $validator = $request->validate([
            'caption' => ['required', 'string', 'max:16777215']
        ]);

        if ($request->hasFile('media')) {
            //Storage::delete('/public/avatars/'.$user->avatar);

            // Get filename with the extension
            $filenameWithExt = $request->file('media')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('media')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload Image
            $path = $request->file('media')->storeAs('public/media', $fileNameToStore);

            $post = Post::create([
                "user_id" => $request->user()->id,
                "caption" => $caption,
                "media_url" => $fileNameToStore,
                "media_type" => ""
            ]);
        } else {
            $post = Post::create([
                "user_id" => $request->user()->id,
                "caption" => $caption,
                "media_url" => "",
                "media_type" => ""
            ]);
        }
        $post->tags()->attach($request->tag);

        return response()->json('Post was created successfully', 200);
    }

    public function update(Request $request, Post $post)
    {
        if (!$request->user()->ownsPost($post)) {
            return response('Unauthorized! You cannot edit this post', 403);
        }

        $validator = $request->validate([
            'caption' => ['required', 'string', 'max:255']
        ]);


        if ($request->hasFile('media')) {
            //Storage::delete('/public/avatars/'.$user->avatar);

            // Get filename with the extension
            $filenameWithExt = $request->file('media')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('media')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload Image
            $path = $request->file('media')->storeAs('public/media', $fileNameToStore);

            $post->media_url = $fileNameToStore;

        }

        $post->caption = $request['caption'];
        $post->user_id = $request->user()->id;
        $post->tags()->sync($request['tag']);

        $post->save();
        return response()->json([
            'message' => 'Post was updated successfully'
        ]);
    }

    public function destroy(Post $post)
    {
        # TODO: delete image from storage: using path. Syntax is similar to:
        # Storage::delete($post->media);
        if (!request()->user()->ownsPost($post)) {
            return response('You do not have the permission to delete this post', 403);
        }
        $post->delete();
        return response()->json('Post was deleted successfully');
    }

    public function getRecent()
    {
        $posts = Post::orderBy('created_at', 'desc')->take(4)->get();
        foreach ($posts as $post) {
            $comments = $post->comments()->count();
            // foreach ($comments as $comment) {
            //     $comment->subcomments = $comment->subcomments();
            // }
            $post->comments_count = $comments;
            $post->user = $post->therapist()->first();
            // Like::where('post_id', $post->id)->get()->count();
            $post->likes_count = $post->likes()->count();
//            $post->saved = $post->saves()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
//            $post->liked = $post->likes()->where('user_id', $request->user()->id)->count()== 0 ? false : true;
        }
        return $posts;
    }

    public function getByTherapistId(Request $request, User $user)
    {
        $posts = Post::where('user_id', '=', $user->id)->get();

        foreach ($posts as $post) {
            $comments = $post->comments()->count();
            // foreach ($comments as $comment) {
            //     $comment->subcomments = $comment->subcomments();
            // }
            $post->tags = $post->tags()->get();
            $post->comments_count = $comments;
            $post->user = $post->therapist()->first();
            // Like::where('post_id', $post->id)->get()->count();
            $post->likes_count = $post->likes()->count();
            $post->saved = $post->saves()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
            $post->liked = $post->likes()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
        }
        return $posts;
    }

    public function search(Request $request, $searchText)
    {
        if (isset($searchText)) {
            $posts = Post::where('caption', 'like', '%' . $searchText . '%')->paginate(1);
        } else {
            $posts = Post::paginate(1);
        }
        foreach ($posts as $post) {
            $comments = $post->comments()->count();
            // foreach ($comments as $comment) {
            //     $comment->subcomments = $comment->subcomments();
            // }
            $post->comments_count = $comments;
            $post->user = $post->therapist()->first();
            // Like::where('post_id', $post->id)->get()->count();
            $post->likes_count = $post->likes()->count();
            $post->saved = $post->saves()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
            $post->liked = $post->likes()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
        }
        return $posts;
    }

    public function getByTagId(Request $request, Tags $tag)
    {
        $tg = Tags::with('posts')->where('id', '=', $tag->id)->get();
        $tg = $tg[0];
        foreach ($tg->posts as $post) {
            $comments = $post->comments()->count();
            // foreach ($comments as $comment) {
            //     $comment->subcomments = $comment->subcomments();
            // }
            $post->comments_count = $comments;
            $post->user = $post->therapist()->first();
            // Like::where('post_id', $post->id)->get()->count();
            $post->likes_count = $post->likes()->count();
            $post->saved = $post->saves()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
            $post->liked = $post->likes()->where('user_id', $request->user()->id)->count() == 0 ? false : true;
        }
        return $tg;
    }
}
