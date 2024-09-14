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
        'answers' => 'required|array|min:2',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 200);
    }

    $question = new Question();
    $question->id_question = $request->id_question;
    $question->id_question_query = $request->id_question_query;
    $question->title = $request->title;
    $question->description = $request->description;
    $question->answer_correct = uniqid() .$request->answer_correct;
    $question->level_question = $request->level_question;
    $question->number_question = $request->number_question;
    $question->slug = $request->slug;

    if ($request->hasFile('image_question')) {
        $newNameImage = time() . uniqid() . $request->id_question . '.' . $request->image_question->getClientOriginalExtension();
        $image = new Image();
        $image->id_image = time() . uniqid() . $request->id_question;
        $image->id_query_image = $request->id_question;
        $image->url_image = $newNameImage;
        $isSaveImage = $image->save();
        if (!$isSaveImage) {
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

    // Lưu các câu trả lời liên quan
    foreach ($request->answers as $answer) {
        $answerDb = new Answer();
        $answerDb->id_answer = uniqid() .$answer["id_answer"];
        $answerDb->question_id = $question->id;
        $answerDb->answer_text = $answer["answer_text"];
        $answerDb->slug = uniqid() . $answer["id_answer"];
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
    }

    $questionWithAnswers = Question::with('answers')
        ->where('id_question', $question->id_question)
        ->first();

    return response()->json([
        'status' => true,
        'message' => 'create question success',
        'data' => [
            'question' => $questionWithAnswers,
        ]
    ], 200);
}


public function updateQuestion(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id' => 'required',
        'answers' => 'array|min:1'  // Yêu cầu mảng các câu trả lời để cập nhật
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    // Tìm câu hỏi theo id_question
    $question = Question::where('id', $request->id)->first();

    if (!$question) {
        return response()->json([
            'message' => 'Question not found',
        ], 404);
    }

    // Cập nhật các trường của câu hỏi
    $question->title = $request->title ?? $question->title;
    $question->description = $request->description ?? $question->description;
    $question->level_question = $request->level_question ?? $question->level_question;
    $question->number_question = $request->number_question ?? $question->number_question;

    // Duyệt qua mảng các câu trả lời được gửi từ request
    foreach ($request->answers as $answerData) {
        if (isset($answerData['id_answer'])) {
            // Tìm câu trả lời theo id_answer và question_id
            $answer = Answer::where('id_answer', $answerData['id_answer'])
                            ->where('question_id', $question->id) // Sử dụng id của bảng Question
                            ->first();

            if ($answer) {
                // Cập nhật câu trả lời
                $answer->answer_text = $answerData['answer_text'] ?? $answer->answer_text;
                $answer->save();

                // Nếu id_answer trùng với answer_correct mới, cập nhật trường answer_correct
                if ($request->answer_correct == $answer->id_answer) {
                    $question->answer_correct = $answer->id_answer;
                }
            }
        }
    }

    // Lưu các thay đổi của câu hỏi
    $question->save();

    return response()->json([
        'message' => 'Question and answers updated successfully',
    ], 200);
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
