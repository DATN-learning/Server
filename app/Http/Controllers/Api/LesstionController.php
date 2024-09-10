<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LesstionChapter;
use App\Models\PdfFile;
use App\Models\Question;

class LesstionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getLessionByID(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chapter_subject_id' => 'required',
            'id_lession' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $lession = LesstionChapter::where('chapter_subject_id', $request->chapter_subject_id)->where('id', $request->id_lession)->first();

        if (empty($lession)) {
            return response()->json([
                'status' => false,
                'message' => 'lession not found',
                'data' => [
                    'lession' => null,
                ]
            ], 200);
        }
        $lession->pdfFiles = $this->getAllPdfLesstion($lession->id_lesstion_chapter);
        if (!empty($lession->pdfFiles)) {
            foreach ($lession->pdfFiles as $pdf) {
                $pdf->pdf_file = asset('pdfs/' . $pdf->url_pdf);
            }
        }
        $listQuestion = Question::where('id_question_query', $lession->id_lesstion_chapter)->get();
        $lession->questions = $listQuestion;
        foreach ($lession->questions as $question) {
            $question->getAllAnswer;
            foreach ($question->Answers as $answer) {
                $answer->imageAnswers = $this->getAllImageQuestion($answer->id_answer);
            }
            $question->imageQuestions = $this->getAllImageQuestion($question->id_question);
        }
        return response()->json([
            'status' => true,
            'message' => 'lession found',
            'data' => [
                'lession' => $lession,
            ]
        ], 200);
    }
    public function getAllPdfLesstion($idLession)
    {
        $pdfs = PdfFile::where('id_query_pdf', $idLession)->get();
        return $pdfs;
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

    public function createLession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_lesstion_chapter' => 'required',
            'chapter_subject_id' => 'required',
            'name_lesstion_chapter' => 'required',
            'number_lesstion_chapter' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $lession = new LesstionChapter();
        $lession->id_lesstion_chapter = $request->id_lesstion_chapter . uniqid();
        $lession->chapter_subject_id = $request->chapter_subject_id;
        $lession->name_lesstion_chapter = $request->name_lesstion_chapter;
        $lession->number_lesstion_chapter = $request->number_lesstion_chapter;
        $lession->description_lesstion_chapter = $request->description_lesstion_chapter ? $request->description_lesstion_chapter : '';
        $check = $lession->save();
        if ($check) {
            return response()->json([
                'status' => true,
                'message' => 'create lession success',
                'data' => [
                    'lession' => $lession,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'create lession fail',
                'data' => [
                    'lession' => null,
                ]
            ], 200);
        }
    }

    public function updateLession(Request $request){
        $validator = Validator::make($request->all(), [
            
            'id_lession_chapter' => 'required|exists:lesstion_chapters,id_lession_chapter', 
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $lession = LesstionChapter::where('id_lession_chapter', $request->id_lession_chapter)->first();
        if (empty($lession)) {
            return response()->json([
                'message' => 'lession not found'
            ], 200);
        }
        $lession->name_lesstion_chapter = $request->name_lesstion_chapter;
        $lession->number_lesstion_chapter = $request->number_lesstion_chapter;
        $lession->description_lesstion_chapter = $request->description_lesstion_chapter ? $request->description_lesstion_chapter : '';
        $check = $lession->save();
        if ($check) {
            return response()->json([
                'message' => 'update lession success'
            ], 200);
        } else {
            return response()->json([
                'message' => 'update lession fail'
            ], 200);
        }
    }

    public function deleteLession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_lesstion_chapter' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $lession = LesstionChapter::where('id_lesstion_chapter', $request->id_lesstion_chapter)->first();
        if (empty($lession)) {
            return response()->json([
                'message' => 'lession not found'
            ], 200);
        }
        $check = $lession->delete();
        if ($check) {
            return response()->json([
                'message' => 'delete lession success'
                
            ], 200);
        } else {
            return response()->json([
                'message' => 'delete lession fail'
            ], 200);
        }
    }

    public function addSlideLession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_lesstion_chapter' => 'required',
            'id_pdf' => 'required',
            'slug' => 'required',
            'pdf_file' => 'required|mimes:pdf|max:20048',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $pdf = new PdfFile();
        $pdf->id_pdf = $request->id_pdf . uniqid();
        $pdf->id_query_pdf = $request->id_lesstion_chapter;
        $pdf->slug = $request->slug;
        $newFileName = uniqid() . $request->id_lesstion_chapter . '.' . $request->pdf_file->getClientOriginalExtension();
        $request->pdf_file->move(public_path('pdfs'), $newFileName);
        $pdf->url_pdf = $newFileName;
        $check = $pdf->save();
        if ($check) {
            return response()->json([
                'status' => true,
                'message' => 'add slide lession success',
                'data' => [
                    'pdf' => $pdf,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'add slide lession fail',
                'data' => [
                    'pdf' => null,
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
