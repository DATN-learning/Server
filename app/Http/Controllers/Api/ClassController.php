<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $class = ClassRoom::all();
        if (empty($class)) {
            return response()->json([
                'status' => false,
                'message' => 'class not found',
                'data' => [
                    'class' => $class,
                ]
            ], 404);
        } else {
            foreach ($class as $item) {
                $item->subjects;
                if (!empty($item->subjects)) {
                    foreach ($item->subjects as $subject) {
                        $subject->logo_image = asset('images/' . $subject->logo_image);
                    }
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'class found',
                'data' => [
                    'class' => $class,
                ]
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
        $validator = Validator::make($request->all(), [
            'name_class' => 'required',
            'class' => 'required|unique:class_rooms,class|integer|min:1|max:12',
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $class = new ClassRoom();
        $class->id_class_room = uniqid() . '' . uniqid() . $request->class;
        $class->name_class = $request->name_class;
        $class->class = $request->class;
        $class->slug = $request->slug . uniqid();
        $check = $class->save();
        if ($check) {
            return response()->json([
                'status' => true,
                'message' => 'add class success',
                'data' => [
                    'class' => $class,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'add class fail',
                'data' => [
                    'class' => $class,
                ]
            ], 400);
        }
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
