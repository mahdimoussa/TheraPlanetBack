<?php

namespace App\Http\Controllers;

use App\Review;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {

        $validator = $request->validate([

                'user_therapist_id' => ['required'],
                'review' => ['required', 'string', 'max:255'],
                'rating' => ['required', 'numeric']
            ]);
//        if (!$request->user()->id == $request->user_therapist_id) {
//            return response('You cannot put your review', 403);
//        }
        $review = new Review($validator);
        $review->user_id = $request->user()->id;
        $review->save();
        return $review;
        return response('Review was added successfully');
    }

    public function update(Request $request, Review $review)
    {
        if (!$request->user()->is('user')) {
            return response('You cannot put your review', 403);
        }
        $validator = $request->validate([
                'user_therapist_id' => ['required'],
                'review' => ['required', 'string', 'max:255'],
                'rating' => ['required', 'numeric']
            ]);
        $review->fill($validator);
        $review->user_id = $request->user()->id;
        $review->save();
        return Response('Review was updated successfully');
    }

    public function destroy(Review $review)
    {
        if (!request()->user()->is('user')) {
            return response('You cannot put your review', 403);
            $review->delete();
            return Response('Review was deleted successfully');
        }
    }
}
