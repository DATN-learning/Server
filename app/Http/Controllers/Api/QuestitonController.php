<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Question;
use App\Models\Image;
use App\Models\Answer;

class QuestitonController extends Controller
{
    //
    public function getQuestionByIDQR(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_question_query' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $questions = Question::where('id_question_query', $request->id_question_query)->get();
        if (empty($questions)) {
            return response()->json([
                'status' => false,
                'message' => 'question not found',
                'data' => [
                    'question' => null,
                ]
            ], 200);
        }
        foreach ($questions as $question) {
            $question->getAllAnswer;
            foreach ($question->Answers as $answer) {
                $answer->imageAnswers = $this->getAllImageQuestion($answer->id_answer);
            }
            $question->imageQuestions = $this->getAllImageQuestion($question->id_question);
        }
        return response()->json([
            'status' => true,
            'message' => 'question found',
            'data' => [
                'question' => $questions,
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

    public function createQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_question' => 'required',
            'id_question_query' => 'required',
            'title' => 'required',
            'description' => 'required',
            'answer_correct' => 'required',
            'level_question' => 'required',
            'number_question' => 'required',
            'slug' => 'required',
            'answers'=>'required | array | min:2',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }
        $question = new Question();
        $question->id_question = $request->id_question;
        $question->id_question_query = $request->id_question_query;
        $question->title = $request->title;
        $question->description = $request->description;
        $question->answer_correct = $request->answer_correct;
        $question->level_question = $request->level_question;
        $question->number_question = $request->number_question;
        $question->slug = $request->slug;
        
        if ($request->hasFile('image_question')) {
            $newNameImage = time() . uniqid() . $request->id_question . '.' . $request->image_question->getClientOriginalExtension();
            $image = new Image();
            $image->id_image = time() . uniqid().$request->id_question;
            $image->id_query_image = $request->id_question;
            $image->url_image = $newNameImage;
            $isSaveImage = $image->save();
            if(!$isSaveImage){
                return response()->json([
                    'status' => false,
                    'message' => 'create image fail',
                    'data' => [
                        'question' => null,
                    ]
                ], 200);
            }
            $request->image_question->move(public_path('images'), $newNameImage);
        }
        $isSuccess = $question->save();
        if (!$isSuccess) {
            return response()->json([
                'status' => false,
                'message' => 'create question fail',
                'data' => [
                    'question' => null,
                ]
            ], 200);
        }

        foreach ($request->answers as $answer) {
            $answerDb = new Answer();
            $answerDb->id_answer = time() . uniqid().$answer["id_answer"];
            $answerDb->question_id = $question->id;
            $answerDb->answer_text =$answer["answer_text"];
            $answerDb->slug =$answer["answer_text"];
            $isSuccess = $answerDb->save();
            
            if (!$isSuccess) {
                return response()->json([
                    'status' => false,
                    'message' => 'create answer fail',
                    'data' => [
                        'question' => null,
                    ]
                ], 200);
            }
        return response()->json([
            'status' => true,
            'message' => 'create question success',
            'data' => [
                'question' => $question,
            ]
        ], 200);
    }
}

    public function updateQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_question' => 'required'
            
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $question = Question::where('id', $request->id)->first();

        if (!$question) {
            return response()->json([
                'message' => 'Question not found',
            ], 404);
        }

        $question->title = $request->title;
        $question->description = $request->description;
        $question->answer_correct = $request->answer_correct;
        $question->level_question = $request->level_question;
        $question->number_question = $request->number_question;

        $answer = Answer::where('question_id', $question->id)->get();

        if($answer){
            foreach ($answer as $ans) {
                $ans->answer_text = $request->answer_text;
            }
        }
    }

    public function deleteQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_question' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $question = Question::where('id_question', $request->id_question)->first();

        if (!$question) {
            return response()->json([
                'message' => 'Question not found',
            ], 404);
        }

        $check = $question->delete();

        if ($check) {
            return response()->json([
                'message' => 'Question deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Error deleting question',
            ], 500);
        }
    }

}
