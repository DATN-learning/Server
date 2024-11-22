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

        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }
    
        $userId = $request->user_id;
    
        // Lấy 5 lần xem gần nhất của user theo `user_id`
        $lastViews = View::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
    
        $data = [];
        foreach ($lastViews as $view) {
            $lesstionChapter = LesstionChapter::with(['chapterSubject.subjects.classRoom'])
                ->where('id_lesstion_chapter', $view->id_view_query)
                ->first();
    
            if ($lesstionChapter) {
                // Lấy bài học tiếp theo
                $nextLesson = LesstionChapter::where('chapter_subject_id', $lesstionChapter->chapter_subject_id)
                    ->where('number_lesstion_chapter', '>', $lesstionChapter->number_lesstion_chapter)
                    ->orderBy('number_lesstion_chapter', 'asc') // Sắp xếp để lấy bài tiếp theo
                    ->first();
    
                if($nextLesson){
                    $data[] = $nextLesson;
                }
            }
        }
    
        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function getLastSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $userId = $request->user_id;

        // Lấy 5 lần xem gần nhất của user theo `user_id`
        $lastViews = View::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $data = [];
        foreach ($lastViews as $view) {
            // Lấy thông tin bài học hiện tại
            $lesstionChapter = LesstionChapter::with(['chapterSubject'])
                ->where('id_lesstion_chapter', $view->id_view_query)
                ->first();

            if ($lesstionChapter) {
                // Lấy bài học tiếp theo
                $nextLesson = LesstionChapter::where('chapter_subject_id', $lesstionChapter->chapter_subject_id)
                    ->where('number_lesstion_chapter', '>', $lesstionChapter->number_lesstion_chapter)
                    ->orderBy('number_lesstion_chapter', 'asc')
                    ->first();

                if ($nextLesson) {
                    $nextLesson = $nextLesson->toArray();
                    $nextLesson['chapter'] = $lesstionChapter->chapterSubject; 

                    $data[] = [
                        'next_lesson' => $nextLesson,
                    ];
                }
            }
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }




}
