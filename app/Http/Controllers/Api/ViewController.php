<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LesstionChapter;
use App\Models\Subject;


class ViewController extends Controller
{
    public function startView(Request $request)
    {
        
        // Validate các dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'view_id' => 'required',
            'id_view_query' => 'required',
            'time_view' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Lưu bản ghi mới với thời gian bắt đầu
        $view = new View();
        $view->view_id = $request->view_id;
        $view->user_id = $request->user_id;
        $view->id_view_query = $request->id_view_query;
        $view->time_view = $request->time_view;
        // Lưu thời gian bắt đầu
        $view->save();

        return response()->json([
            'status' => true,
            'message' => 'Start view recorded',
            'data' => $view
        ], 200);
    }

    public function getUserLastLesson(Request $request)
    {

        $validator = Validator::make($request->all (), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Lấy bản ghi View mới nhất của user theo `user_id`
        $lastView = View::where('user_id', $request->user_id)->orderBy('created_at', 'desc')->first();

        if (!$lastView) {
            return response()->json(['message' => 'No lessons found for this user'], 404);
        }

        // Lấy LesstionChapter từ id_view_query
        $lesstionChapter = LesstionChapter::where('id_lesstion_chapter', $lastView->id_view_query)->first();

        if (!$lesstionChapter) {
            return response()->json(['message' => 'Lesstion chapter not found'], 404);
        }

        // Lấy ChapterSubject từ chapter_subject_id
        $chapterSubject = $lesstionChapter->chapterSubject;

        if (!$chapterSubject) {
            return response()->json(['message' => 'Chapter subject not found'], 404);
        }

        // Lấy Subject từ subject_id
        $subject = $chapterSubject->subjects;

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        // Lấy Classroom từ classroom_id
        $classroom = $subject->classRoom;

        if (!$classroom) {
            return response()->json(['message' => 'Classroom not found'], 404);
        }

        // Trả về thông tin
        return response()->json([
            'id_lession_chapter' => [
                $lesstionChapter->id_lesstion_chapter
            ],
        ]);
    }

    public function getLastSession(Request $request)
    {
       
    }


}
