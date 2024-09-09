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
        $subject->id_subject = $request->id_subject.uniqid();
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
