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

        // Lấy 5 lần xem gần nhất của user theo user_id
        $lastViews = View::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];
        $processedChapters = []; // Tập hợp các chương đã xử lý

        foreach ($lastViews as $view) {
            // Lấy thông tin bài học hiện tại
            $lesstionChapter = LesstionChapter::with(['chapterSubject'])
                ->where('id_lesstion_chapter', $view->id_view_query)
                ->first();

            if ($lesstionChapter) {
                $chapterId = $lesstionChapter->chapter_subject_id;

                // Bỏ qua nếu chương này đã được xử lý
                if (in_array($chapterId, $processedChapters)) {
                    continue;
                }

                // Đánh dấu chương này đã được xử lý
                $processedChapters[] = $chapterId;

                // Lấy bài học tiếp theo
                $nextLesson = LesstionChapter::where('chapter_subject_id', $chapterId)
                    ->where('number_lesstion_chapter', '>', $lesstionChapter->number_lesstion_chapter)
                    ->orderBy('number_lesstion_chapter', 'asc')
                    ->first();

                // Kiểm tra nếu tồn tại bài học tiếp theo
                if ($nextLesson) {
                    $chapter = $lesstionChapter->chapterSubject;
                    $chapter->chapter_image = asset('images/' . $chapter->chapter_image);

                    // Chỉ thêm vào kết quả nếu có bài học tiếp theo
                    $data[] = [
                        'chapter' => $chapter,
                        'next_lesson' => $nextLesson,
                    ];
                }
            }

            // Dừng lại khi đã lấy đủ 5 chương
            if (count($data) >= 5) {
                break;
            }
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    // public function getLastSession(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation error',
    //             'errors' => $validator->errors(),
    //         ], 400);
    //     }

    //     $userId = $request->user_id;

    //     // Lấy 5 lần xem gần nhất của user theo `user_id`
    //     $lastViews = View::where('user_id', $userId)
    //         ->orderBy('id', 'desc')
    //         ->take(5)
    //         ->get();

    //     $data = [];
    //     foreach ($lastViews as $view) {
    //         // Lấy thông tin bài học hiện tại
    //         $lesstionChapter = LesstionChapter::with(['chapterSubject'])
    //             ->where('id_lesstion_chapter', $view->id_view_query)
    //             ->first();

    //         if ($lesstionChapter) {
    //             // Lấy bài học tiếp theo
    //             $nextLesson = LesstionChapter::where('chapter_subject_id', $lesstionChapter->chapter_subject_id)
    //                 ->where('number_lesstion_chapter', '>', $lesstionChapter->number_lesstion_chapter)
    //                 ->orderBy('number_lesstion_chapter', 'asc')
    //                 ->first();

    //             // Kiểm tra nếu tồn tại bài học tiếp theo
    //             if ($nextLesson) {
    //                 $chapter = $lesstionChapter->chapterSubject;

    //                 // Gắn bài học tiếp theo vào chương

    //                 // Chỉ thêm mục này vào data nếu có next_lesson
    //                 $data[] = [
    //                     'chapter' => $chapter,
    //                     'next_lesson' => $nextLesson,
    //                 ];
    //             }
    //         }
    //     }
    //     return response()->json([
    //         'status' => true,
    //         'data' => $data,
    //     ]);
    // }

}
