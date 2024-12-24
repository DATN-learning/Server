<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'RatingController']);
    }
    public function createRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'rating_id' => 'required',
            'lesstion_chapter_id' => 'required',
            'content' => 'required',
            'rating' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'error' => $validator->errors()
            ], 400);
        }

        $rating = new Rating();
        $rating->user_id = $request->user_id;
        $rating->rating_id = $request->rating_id . time() . random_int(1000, 9999);
        $rating->lesstion_chapter_id = $request->lesstion_chapter_id;
        $rating->content = $request->content;
        $rating->rating = $request->rating;
        $rating->save();
        return response()->json([
            'status' => true,
            'message' => 'create rating success',
            'data' => $rating
        ], 200);

    }
    public function getAllRating()
    {
        $ratings = Rating::with(['userCreate', 'lesstionChapter'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Get all ratings success',
            'data' => [
                'rating' => $ratings
            ]
        ], 200);
    }

    public function getRatingByLessionChapterId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesstion_chapter_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'error' => $validator->errors()
            ], 200);
        }

        $ratings = Rating::with(['userCreate', 'lesstionChapter'])
                    ->where('lesstion_chapter_id', $request->lesstion_chapter_id)
                    ->get();

        return response()->json([
            'status' => true,
            'message' => 'Get rating success',
            'data' => $ratings
        ], 200);
    }

    public function updateRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating_id' => 'required',
            'content' => 'required',
            'rating' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'error' => $validator->errors()
            ], 200);
        }
        $rating = Rating::where('rating_id', $request->rating_id)->first();

        if (!$rating) {
            return response()->json([
                'status' => false,
                'message' => 'post not found',
            ], 200);
        }

        $rating->content = $request->content;
        $rating->rating = $request->rating;
        $rating->save();
        return response()->json([
            'status' => true,
            'message' => 'update rating success',
            'data' => $rating
        ], 200);
    }
    public function deleteRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'error' => $validator->errors()
            ], 200);
        }
        $rating = Rating::where('rating_id', $request->rating_id)->first();
        if (!$rating) {
            return response()->json([
                'status' => false,
                'message' => 'post not found',
            ], 200);
        }

        $rating->delete();
        return response()->json([
            'status' => true,
            'message' => 'delete rating success'
        ], 200);
    }
}
