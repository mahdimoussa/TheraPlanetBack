<?php

namespace App\Http\Controllers;

use App\Review;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $profile = $user->profile();
        $posts = $user->posts();
        foreach($posts as $post) {
            $comments = $post->comments();
            foreach($comments as $comment) {
                $comment->subcomments = $comment->subcomments();
            }
            $post->comments = $comments;
            // Like::where('post_id', $post->id)->get()->count();
            $post->likes = $post->likes()->count();
        }

        $reviews = $user->reviews();
        $data = [
            "posts" => $posts,
            "profile" => $profile,
            "reviews" => $reviews
        ];
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user = User::findOrFail($user->id);

        $user->biography = $request->biography;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->location = $request->location;
        $user->phone_number = $request->phone_number;

        if ($request->hasFile('profile_pic')) {
            // Get filename with the extension
            $filenameWithExt = $request->file('profile_pic')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('profile_pic')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload Image
            $path = $request->file('profile_pic')->storeAs('public/media', $fileNameToStore);

            $user->profile_pic = $fileNameToStore;
        }
        $user->save();
        return response()->json("success", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function getUserById(Request $request){
        $user = User::findOrFAil($request->id);
        $reviews = Review::where('user_therapist_id','=',$user->id)->get();
        $user->reviews = $reviews;
        return $user;

    }

    public function setOnline(Request $request,User $user){
        $user->status = 1;
        $user->save();
        return response()->json(['message' => 'User Is Online']);

    }

    public function setOffline(Request $request,$id){

        $user = User::findOrFail($id);
        $user->status = 0;
        $user->save();
//        $user->update(['status' =>0 ]);
        return response()->json(['message' => 'User Is Offline']);

    }
}
