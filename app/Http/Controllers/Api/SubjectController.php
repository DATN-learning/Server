<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showAllSubject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_room_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $subjects = Subject::where('class_room_id', $request->class_room_id)->get();

        foreach ($subjects as $subject) {
            $subject->classRoom;
        }

        return response()->json($subjects);
    }

    public function createSubject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_subject' => 'required',
            'class_room_id' => 'required',
            'name_subject' => 'required',
            'logo_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20048',
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $subject = new Subject();
        $subject->id_subject = $request->id_subject;
        $subject->class_room_id = $request->class_room_id;
        $subject->name_subject = $request->name_subject;
        $newNameImage = time() .uniqid().$request->id_subject. '.' . $request->logo_image->getClientOriginalExtension();
        $request->logo_image->move(public_path('images'), $newNameImage);
        $subject->logo_image = $newNameImage;
        $subject->slug = $request->slug;
        $check = $subject->save();

        if ($check) {
            return response()->json([
                'status' => true,
                'message' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'error',
            ], 200);
        }
    }
    public function deleteSubject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        $subject = Subject::where('id', $request->id)->first();
    
        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found',
            ], 404);
        }
    
        $check = $subject->delete();
    
        if ($check) {
            return response()->json([
                'message' => 'Subject deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Error deleting subject',
            ], 500);
        }
    }

    public function updateSubject(Request $request)
{
    // Xác thực dữ liệu đầu vào
    $validator = Validator::make($request->all(), [
        'id' => 'required|exists:subjects,id', 
        'name_subject' => 'string|max:255',
        'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    // Lấy đối tượng subject theo id
    $subject = Subject::where('id', $request->id)->first();

    if (!$subject) {
        return response()->json([
            'status' => false,
            'message' => 'Subject not found',
        ], 404);
    }

    // Cập nhật các trường cần thiết
    $subject->name_subject = $request->name_subject;
    
    // Xử lý logo_image nếu có
    if ($request->hasFile('logo_image')) {
        $newNameImage = time() . uniqid() . '.' . $request->logo_image->getClientOriginalExtension();
        $request->logo_image->move(public_path('images'), $newNameImage);
        $subject->logo_image = $newNameImage;
    }

    // Lưu thay đổi vào database
    $check = $subject->save();

    if ($check) {
        return response()->json([
            'status' => true,
            'message' => 'Subject updated successfully',
        ], 200);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Failed to update subject',
        ], 500);
    }
}

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
