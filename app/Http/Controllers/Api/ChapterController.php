<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChapterSubject;
use App\Models\Subject;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Validator;
use Mockery\Matcher\Subset;


class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getChapterSubject(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $subject = Subject::where('id_subject', $request->subject_id)->first();
        if (empty($subject)) {
            return response()->json([
                'status' => false,
                'message' => 'subject not found',
                'data' => [
                    'chapter' => null,
                ]
            ], 200);
        }
        $chapters = ChapterSubject::where('subject_id', $subject->id)->get();
        foreach ($chapters as $chapter) {
            $chapter->chapter_image = asset('images/' . $chapter->chapter_image);
            $chapter->lessions;
        }
        return response()->json([
            'status' => true,
            'message' => 'chapter found',
            'data' => [
                'chapter' => $chapters,
            ]
        ], 200);
    }

    public function getChapterExercises(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $subject = Subject::where('id_subject', $request->subject_id)->first();
        // hide chapter_image
        if (empty($subject)) {
            return response()->json([
                'status' => false,
                'message' => 'subject not found',
                'data' => [
                    'chapter' => null,
                ]
            ], 200);
        }
        $chapters = ChapterSubject::where('subject_id', $subject->id)->get();
        foreach ($chapters as $chapter) {
            $chapter->makeHidden(['chapter_image']);
            $listQuestion = Question::where('id_question_query', $chapter->id_chapter_subject)->get();
            $chapter->questions = $listQuestion;
            foreach ($chapter->questions as $question) {
                $question->getAllAnswer;
                foreach ($question->Answers as $answer) {
                    $answer->imageAnswers = $this->getAllImageQuestion($answer->id_answer);
                }
                $question->imageQuestions = $this->getAllImageQuestion($question->id_question);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'chapter found',
            'data' => [
                'chapters' => $chapters,
            ]
        ], 200);
    }
    public function getAllImageQuestion($idQuestion)
    {
        $listUrlImage = [];

        $images = Image::where('id_query_image', $idQuestion)->get();
        foreach ($images as $image) {
            $urlImage = asset('images/' . $image->url_image);
            array_push($listUrlImage, $urlImage);
        }
        return $listUrlImage;
    }
    public function getAllImageAnswer($idAnswer)
    {
        $listUrlImage = [];

        $images = Image::where('id_query_image', $idAnswer)->get();
        foreach ($images as $image) {
            $urlImage = asset('images/' . $image->url_image);
            array_push($listUrlImage, $urlImage);
        }
        return $listUrlImage;
    }

    public function createChapter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_chapter_subject' => 'required',
            'subject_id' => 'required',
            'name_chapter_subject' => 'required',
            'chapter_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20408',
            'slug' => 'required',
            'number_chapter' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $chapter = new ChapterSubject();
        $chapter->id_chapter_subject = $request->id_chapter_subject . uniqid();
        $chapter->subject_id = $request->subject_id;
        $chapter->name_chapter_subject = $request->name_chapter_subject;
        $chapter->slug = $request->slug;
        $chapter->number_chapter = $request->number_chapter;
        $newNameImage = time() . uniqid() . $request->id_chapter_subject . '.' . $request->chapter_image->getClientOriginalExtension();
        $chapter->chapter_image = $newNameImage;
        $check = $chapter->save();
        if ($check) {
            $request->chapter_image->move(public_path('images'), $newNameImage);
            return response()->json([
                'status' => true,
                'message' => 'create chapter success',
                'data' => [
                    'chapter' => $chapter,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'create chapter fail',
                'data' => [
                    'chapter' => null,
                ]
            ], 200);
        }
    }

    public function editChapterByID(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_chapter_subject' => 'required',
            'name_chapter_subject' => 'required',
            'chapter_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20408',
            'number_chapter' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $chapter = ChapterSubject::where('id_chapter_subject', $request->id_chapter_subject)->first();
        if (empty($chapter)) {
            return response()->json([
                'status' => false,
                'message' => 'chapter not found',
                'data' => [
                    'chapter' => null,
                ]
            ], 200);
        }
        $chapter->name_chapter_subject = $request->name_chapter_subject;
        $chapter->number_chapter = $request->number_chapter;
        if ($request->hasFile('chapter_image')) {
            // remove old image
            $oldImage = public_path('images/' . $chapter->chapter_image);
            if (file_exists($oldImage)) {
                unlink($oldImage);
            }
            $newNameImage = time() . uniqid() . $request->id_chapter_subject . '.' . $request->chapter_image->getClientOriginalExtension();
            $chapter->chapter_image = $newNameImage;
            $request->chapter_image->move(public_path('images'), $newNameImage);
        }
        $check = $chapter->save();
        if ($check) {
            return response()->json([
                'status' => true,
                'message' => 'edit chapter success',
                'data' => [
                    'chapter' => $chapter,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'edit chapter fail',
                'data' => [
                    'chapter' => null,
                ]
            ], 200);
        }
    }

    public function  deleteChapterByID(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_chapter_subject' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $chapter = ChapterSubject::where('id_chapter_subject', $request->id_chapter_subject)->first();
        if (empty($chapter)) {
            return response()->json([
                'status' => false,
                'message' => 'chapter not found',
                'data' => [
                    'chapter' => null,
                ]
            ], 200);
        }
        $chapter->delete();
        return response()->json([
            'status' => true,
            'message' => 'delete chapter success',
            'data' => [
                'chapter' => $chapter,
            ]
        ], 200);
    }
}
