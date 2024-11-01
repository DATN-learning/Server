<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
}
