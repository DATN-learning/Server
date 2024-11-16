<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Question;
use App\Models\Image;
use App\Models\Answer;
use App\Models\Score;

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
            $question->answer_correct = time() .$request->answer_correct;
            $question->level_question = $request->level_question;
            $question->number_question = $request->number_question;
            $question->slug = $request->slug;

            if ($request->hasFile('image_question')) {
                $newNameImage = time() .$request->id_question . '.' . $request->image_question->getClientOriginalExtension();
                $image = new Image();
                $image->id_image = time() . $request->id_question;
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

            foreach ($request->answers as $answer) {
                $answerDb = new Answer();
                $answerDb->id_answer =  time() .$answer["id_answer"];
                $answerDb->question_id = $question->id;
                $answerDb->answer_text = $answer["answer_text"];
                $answerDb->slug =  time() .$answer["id_answer"];
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
    // Validate dữ liệu request
    $validator = Validator::make($request->all(), [
        'id_question' => 'required',
        'answers' => 'required|array',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 200);
    }

    $question = Question::where('id_question', $request->id_question)->first();
    if (!$question) {
        return response()->json([
            'status' => false,
            'message' => 'Question not found',
            'data' => null
        ], 404);
    }

    // Cập nhật thông tin câu hỏi
    $question->id_question_query = $request->id_question_query;
    $question->title = $request->title;
    $question->description = $request->description;
    $question->level_question = $request->level_question;
    $question->number_question = $request->number_question;
    $question->slug = $request->slug;

    // Kiểm tra xem answer_correct có nằm trong danh sách các câu trả lời không
    $answerCorrectId = null;
    foreach ($request->answers as $answer) {
        if ($answer['id_answer'] == $request->answer_correct) {
            $answerCorrectId = $answer['id_answer'];
            break;
        }
    }

    if (!$answerCorrectId) {
        return response()->json([
            'status' => false,
            'message' => 'The provided answer_correct does not match any answer IDs',
            'data' => null
        ], 400);
    }

    // Cập nhật hoặc thêm câu trả lời mới
    foreach ($request->answers as $answer) {
        $answerDb = Answer::where('id_answer', $answer['id_answer'])
            ->where('question_id', $question->id)
            ->first();

        if ($answerDb) {
            $answerDb->answer_text = $answer['answer_text'];
            $answerDb->save();
        } 
    }

    // Cập nhật answer_correct cho question
    $question->answer_correct = $answerCorrectId;

    // Lưu các thay đổi của câu hỏi
    $isSuccess = $question->save();

    if (!$isSuccess) {
        return response()->json([
            'status' => false,
            'message' => 'Update question fail',
            'data' => null
        ], 500);
    }

    // Lấy lại question cùng các câu trả lời liên quan
    $questionWithAnswers = Question::with('answers')
        ->where('id_question', $question->id_question)
        ->first();

    return response()->json([
        'status' => true,
        'message' => 'Update question success',
        'data' => [
            'question' => $questionWithAnswers,
        ]
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

    public function submitedChapterAnswer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_score' => 'required',
            'user_id' => 'required',
            'question_query_id' => 'required',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required',
            'answers.*.answer_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $userId = $request->user_id;
        $questionQueryId = $request->question_query_id;
        $answers = $request->answers;


        $totalQuestions = Question::where('id_question_query', $questionQueryId)->count();
        if ($totalQuestions == 0) {
            return response()->json(['error' => 'No questions found for this chapter.'], 404);
        }

        $pointsPerQuestion = 10 / $totalQuestions;
        $totalScore = 0;

        foreach ($answers as $answer) {
            $question = Question::where('id_question_query', $questionQueryId)
                                ->where('id', $answer['question_id'])
                                ->first();

            if ($question) {
                $isCorrect = $answer['answer_id'] == $question->answer_correct;
                $totalScore += $isCorrect ? $pointsPerQuestion : 0;
            }
        }

        $existingScore = Score::where('user_id', $userId)
                            ->where('question_query_id', $questionQueryId)
                            ->first();

        if ($existingScore) {
            $existingScore->score = round($totalScore, 2);
            $existingScore->save();
        } else {
            $score = new Score();
            $score->id_score = uniqid(). $request->id_score;
            $score->user_id = $userId;
            $score->question_query_id = $questionQueryId;
            $score->score = round($totalScore, 2);
            $score->save();
        }

        return response()->json([
            'status' => true,
            'data' => [
                'total_score' => round($totalScore, 2),
            ]
        ], 200);
    }

}
