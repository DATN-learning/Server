<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ChapterController;
use App\Http\Controllers\Api\LesstionController;
use App\Http\Controllers\Api\QuestitonController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TokenNotificationController;
use App\Http\Controllers\Api\ChatBotController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\ViewController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/auth/resgister', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/adminLogin', [AuthController::class, 'adminLogin'])->name('auth.adminLogin');
Route::post('/auth/checktoken', [AuthController::class, 'loginByToken'])->name('auth.checktoken');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');
Route::post('/auth/logoutall', [AuthController::class, 'logoutAll'])->name('auth.logoutall');
Route::get('/auth/getprofile', [AuthController::class, 'getProfile'])->name('auth.getProfile')->middleware('auth:sanctum');
Route::get('/auth/getAllUsers', [AuthController::class, 'getAllUsers'])->name('auth.getAllUsers')->middleware('auth:sanctum');
Route::post('/auth/deleteUserByProfileId', [AuthController::class, 'deleteUserByProfileId'])->name('auth.deleteUserByProfileId')->middleware('auth:sanctum');
Route::post('/auth/updateprofile', [AuthController::class, 'updateProfile'])->name('auth.updateProfile')->middleware('auth:sanctum');
//class
Route::get('/classroom', [ClassController::class, 'index'])->name('classroom.index');
Route::post('/classroom', [ClassController::class, 'store'])->name('classroom.store')->middleware('auth:sanctum');

//subject
Route::post('/classroom/createSubject', [SubjectController::class, 'createSubject'])->name('classroom.createSubject')->middleware('auth:sanctum');
Route::post('/classroom/deleteSubject', [SubjectController::class, 'deleteSubject'])->name('classroom.deleteSubject')->middleware('auth:sanctum');
Route::post('/classroom/updateSubject', [SubjectController::class, 'updateSubject'])->name('classroom.updateSubject')->middleware('auth:sanctum');

Route::post('/classroom/getChapterSubject', [ChapterController::class, 'getChapterSubject'])->name('classroom.getChapterSubject')->middleware('auth:sanctum');
//?chapter
Route::post('/classroom/createChapter', [ChapterController::class, 'createChapter'])->name('classroom.createChapter')->middleware('auth:sanctum');
Route::post('/classroom/editChapterByID', [ChapterController::class, 'editChapterByID'])->name('classroom.editChapterByID')->middleware('auth:sanctum');
Route::post('/classroom/deleteChapterByID', [ChapterController::class, 'deleteChapterByID'])->name('classroom.deleteChapterByID')->middleware('auth:sanctum');
// ?lession
Route::post('/classroom/getLessionById', [LesstionController::class, 'getLessionByID'])->name('classroom.getSubjectChapterLession')->middleware('auth:sanctum');
Route::post('/classroom/createLession', [LesstionController::class, 'createLession'])->name('classroom.createLession')->middleware('auth:sanctum');
Route::post('/classroom/addSlideLession', [LesstionController::class, 'addSlideLession'])->name('classroom.addSlideLession')->middleware('auth:sanctum');
Route::post('/classroom/deleteSlideLession', [LesstionController::class, 'deleteSlideLession'])->name('classroom.deleteSlideLession')->middleware('auth:sanctum');
Route::post('/classroom/getSlideLession', [LesstionController::class, 'getSlideLession'])->name('classroom.getSlideLession')->middleware('auth:sanctum');
Route::post('/classroom/deleteLession', [LesstionController::class, 'deleteLession'])->name('classroom.deleteLession')->middleware('auth:sanctum');
Route::post('/classroom/updateLession', [LesstionController::class, 'updateLession'])->name('classroom.updateLession')->middleware('auth:sanctum');
// ?chapter exercise
Route::post('/classroom/getChapterExercises', [ChapterController::class, 'getChapterExercises'])->name('classroom.getChapterExercises')->middleware('auth:sanctum');
// question
Route::post('/question/getQuestionByIDQR', [QuestitonController::class, 'getQuestionByIDQR'])->name('question.getQuestionByIDQR')->middleware('auth:sanctum');
Route::post('/question/createQuestion', [QuestitonController::class, 'createQuestion'])->name('question.createQuestion')->middleware('auth:sanctum');
Route::post('/question/updateQuestion', [QuestitonController::class, 'updateQuestion'])->name('question.updateQuestion')->middleware('auth:sanctum');
Route::post('/question/deleteQuestion', [QuestitonController::class, 'deleteQuestion'])->name('question.deleteQuestion')->middleware('auth:sanctum');
Route::post('/question/submitedChapterAnswer', [QuestitonController::class, 'submitedChapterAnswer'])->name('question.submitedChapterAnswer')->middleware('auth:sanctum');
//posts
Route::post('/manapost/createPostQuestion', [PostController::class, 'createPostQuestion'])->name('manapost.createPostQuestion')->middleware('auth:sanctum');
Route::post('/manapost/updatePostQuestion', [PostController::class, 'updatePostQuestion'])->name('manapost.updatePostQuestion')->middleware('auth:sanctum');
Route::post('/manapost/deletePostQuestion', [PostController::class, 'deletePostQuestion'])->name('manapost.deletePostQuestion')->middleware('auth:sanctum');
Route::get('/manapost/getPostQuestion', [PostController::class, 'getPostQuestion'])->name('manapost.getPostQuestion')->middleware('auth:sanctum'); 
Route::post('/manapost/getPostQuestionByLable', [PostController::class, 'getPostQuestionByLable'])->name('manapost.getPostQuestionByLable')->middleware('auth:sanctum');
Route::post('/manapost/getPostQuestionById', [PostController::class, 'getPostQuestionById'])->name('manapost.getPostQuestionById')->middleware('auth:sanctum');
Route::get('/manapost/getCommentPost', [PostController::class, 'getCommentPost'])->name('manapost.getCommentPost')->middleware('auth:sanctum');
Route::post('/manapost/createCommentPost', [PostController::class, 'createCommentPost'])->name('manapost.createCommentPost')->middleware('auth:sanctum');
Route::post('/manapost/updateCommentPost', [PostController::class, 'updateCommentPost'])->name('manapost.updateCommentPost')->middleware('auth:sanctum');
Route::post('/manapost/deleteCommentPost', [PostController::class, 'deleteCommentPost'])->name('manapost.deleteCommentPost')->middleware('auth:sanctum');

Route::post('/manapost/getPostQuestionByClassRoom', [PostController::class, 'getPostQuestionByClassRoom'])->name('manapost.getPostQuestionByClassRoom')->middleware('auth:sanctum');
// token notification
Route::post('/tokennotification', [TokenNotificationController::class, 'createTokenDevice'])->name('tokennotification.store');
// chatgpt
// Route::get('/chatgpt/ask', [ChatGptController::class, 'ask'])->name('chatgpt.ask');
Route::post('/chat', [ChatBotController::class, 'chat'])->name('chat.chat');

//Rating
Route::post('/rating/createRating', [RatingController::class, 'createRating'])->name('rating.createRating')->middleware('auth:sanctum');
Route::post('/rating/getRatingByLessionChapterId', [RatingController::class, 'getRatingByLessionChapterId'])->name('rating.getRatingByLessionChapterId')->middleware('auth:sanctum');
Route::post('/rating/getAllRating', [RatingController::class, 'getAllRating'])->name('rating.getAllRating')->middleware('auth:sanctum');
Route::post('/rating/updateRating', [RatingController::class, 'updateRating'])->name('rating.updateRating')->middleware('auth:sanctum');
Route::post('/rating/deleteRating', [RatingController::class, 'deleteRating'])->name('rating.deleteRating')->middleware('auth:sanctum');

//View
Route::post('view/startView', [ViewController::class, 'startView'])->name('view.startView')->middleware('auth:sanctum');
Route::post('view/getUserLastLesson', [ViewController::class, 'getUserLastLesson'])->name('view.getUserLastLesson')->middleware('auth:sanctum');
Route::post('view/getLastSession', [ViewController::class, 'getLastSession'])->name('view.getLastSession')->middleware('auth:sanctum');
