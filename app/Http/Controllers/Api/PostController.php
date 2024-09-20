<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Image;
use App\Models\PostAnalysisData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use App\Models\CommentPost;
use Illuminate\Pagination\LengthAwarePaginator;

class PostController extends Controller
{
    //
    public function getPostQuestion(Request $request)
    {
        $posts = $request->class ?
            ($request->subject
                ? Post::where('class_room_id', $request->class)->where('subject_id', $request->subject)->get()
                : Post::where('class_room_id', $request->class)->get()
            ) : Post::all();

        $posts->map(function ($post) {
            $post->images = Image::where('id_query_image', $post->id_post)->get();
            foreach ($post->images as $image) {
                $image->url_image = url('/images/' . $image->url_image);
            }
            $post->timeAgo = $post->created_at->diffForHumans();
            $post->userCreate;
            $post->userCreate->avatar = url('/images/' . $post->userCreate->profile->id_image);
            unset($post->userCreate->profile);
        });

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $request->per_page ? $request->per_page : 10;
        $currentPageSearchResults = $posts->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $posts = new LengthAwarePaginator($currentPageSearchResults, count($posts), $perPage);
        $posts->setPath($request->url() . '?' . 'class=' . $request->class . '&subject=' . $request->subject . '&per_page=' . $request->per_page);
        $posts = $posts->toArray();
        $posts['data'] = $posts['data'] ? array_reverse($posts['data']) : [];

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $posts
        ], 200);
    }
    public  function getPostQuestionByLable(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'label' => 'required',
            'class_room_id' => 'required|numeric',
            'subject_id' => 'numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'error' => $validator->errors()
            ], 200);
        }
        $posts = $request->subject_id ?
            Post::where('category_post', $request->label)->where('class_room_id', $request->class_room_id)->where('subject_id', $request->subject_id)->get() :
            Post::where('category_post', $request->label)->where('class_room_id', $request->class_room_id)->get();
        $posts->map(function ($post) {
            $post->images = Image::where('id_query_image', $post->id_post)->get();
            foreach ($post->images as $image) {
                $image->url_image = url('/images/' . $image->url_image);
            }
            $post->getDataAnalytics;
        });
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $posts
        ], 200);
    }

    public function createPostQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_post' => 'required',
            'user_id' => 'required|numeric',
            'title' => 'required',
            'description' => 'required',
            'class_room_id' => 'required|numeric',
            'subject_id' => 'numeric',
            'list_text' => 'required|array|min:1',
            'label' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'error' => $validator->errors()
            ], 200);
        }
        $post = new Post();
        $post->id_post = $request->id_post . uniqid();
        $post->user_id = $request->user_id;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->class_room_id = $request->class_room_id;
        $post->subject_id = $request->subject_id ? $request->subject_id : null;
        $post->category_post = $request->label;
        $isCreateSuccess = $post->save();
        if ($isCreateSuccess) {
            if ($request->hasFile('photos')) {
                foreach ($request->photos as $file) {
                    $image = new Image();
                    $image->id_image = $post->id_post . uniqid() . 'img';
                    $image->id_query_image = $post->id_post;
                    $nameImage = $post->id_post . uniqid() . 'imagepost' . '.' . $file->extension();
                    $file->move(public_path() . '/images/', $nameImage);
                    $image->url_image = $nameImage;
                    $isCreateImagePost = $image->save();
                    if ($isCreateImagePost) {
                        foreach ($request->list_text as $text) {
                            $postAnalysisData = new PostAnalysisData();
                            $postAnalysisData->id_post_analysis_data = $post->id_post . uniqid() . 'postanalysisdata';
                            $postAnalysisData->post_id = $post->id;
                            $postAnalysisData->text_data = $text; //$postAnalysisData->removeAccents($text);
                            $isCreatePostAnalysisData = $postAnalysisData->save();
                            if (!$isCreatePostAnalysisData) {
                                $post->delete();
                                return response()->json([
                                    'status' => false,
                                    'message' => 'create post fail',
                                ], 200);
                            }
                        }
                        return response()->json([
                            'status' => true,
                            'message' => 'create post success',
                        ], 200);
                    } else {
                        $post->delete();
                        return response()->json([
                            'status' => false,
                            'message' => 'create post fail',
                        ], 200);
                    }
                }
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'create post success no image',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'create post fail',
            ], 200);
        }
    }

    public function updatePostQuestion(Request $request)
{
    // Validate dữ liệu request
    $validator = Validator::make($request->all(), [
        'id_post' => 'required',  // ID của bài viết cần cập nhật
        'user_id' => 'required|numeric',
        'title' => 'required',
        'description' => 'required',
        'class_room_id' => 'required|numeric',
        'subject_id' => 'numeric',
        'list_text' => 'required|array|min:1', // Ít nhất 1 phân tích text
        'label' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'validate error',
            'error' => $validator->errors()
        ], 200);
    }

    // Tìm bài viết cần cập nhật
    $post = Post::where('id_post', $request->id_post)->first();

    if (!$post) {
        return response()->json([
            'status' => false,
            'message' => 'post not found',
        ], 200);
    }

    // Cập nhật thông tin bài viết
    $post->title = $request->title;
    $post->description = $request->description;
    $post->category_post = $request->label;

    // Lưu bài viết sau khi cập nhật
    $isUpdateSuccess = $post->save();

    if ($isUpdateSuccess) {
        // Kiểm tra và cập nhật hoặc thêm ảnh mới nếu có
        if ($request->hasFile('photos')) {
            // Lấy tất cả ảnh hiện có của bài viết
            $existingImages = Image::where('id_query_image', $post->id_post)->get();

            // Xóa ảnh cũ nếu có
            foreach ($existingImages as $existingImage) {
                $imagePath = public_path('/images/') . $existingImage->url_image;
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Xóa file ảnh từ thư mục
                }
                $existingImage->delete(); // Xóa bản ghi ảnh trong cơ sở dữ liệu
            }

            // Thêm ảnh mới vào bài viết
            foreach ($request->photos as $file) {
                $image = new Image();
                $image->id_image = $post->id_post . uniqid() . 'img';
                $image->id_query_image = $post->id_post;
                $nameImage = $post->id_post . uniqid() . 'imagepost' . '.' . $file->extension();
                $file->move(public_path() . '/images/', $nameImage);
                $image->url_image = $nameImage;

                // Lưu ảnh vào bảng image
                $isUpdateImagePost = $image->save();

                if (!$isUpdateImagePost) {
                    return response()->json([
                        'status' => false,
                        'message' => 'update post image fail',
                    ], 200);
                }
            }
        }

        // Cập nhật các phân tích text cho bài viết
        PostAnalysisData::where('post_id', $post->id)->delete(); // Xóa dữ liệu phân tích cũ
        foreach ($request->list_text as $text) {
            $postAnalysisData = new PostAnalysisData();
            $postAnalysisData->id_post_analysis_data = $post->id_post . uniqid() . 'postanalysisdata';
            $postAnalysisData->post_id = $post->id;
            $postAnalysisData->text_data = $text;

            // Lưu dữ liệu phân tích vào bảng post_analysis_data
            $isCreatePostAnalysisData = $postAnalysisData->save();

            if (!$isCreatePostAnalysisData) {
                return response()->json([
                    'status' => false,
                    'message' => 'update post analysis data fail',
                ], 200);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'update post success',
        ], 200);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'update post fail',
        ], 200);
    }
}

    

    public function deletePostQuestion(Request $request)
    {
        // Validate dữ liệu request
        $validator = Validator::make($request->all(), [
            'id_post' => 'required',  // ID của bài viết cần xóa
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'error' => $validator->errors()
            ], 200);
        }

        // Tìm bài viết cần xóa
        $post = Post::where('id_post', $request->id_post)->first();

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'post not found',
            ], 200);
        }

        // Xóa các ảnh liên quan đến bài viết
        $images = Image::where('id_query_image', $post->id_post)->get();
        foreach ($images as $image) {
            $imagePath = public_path('/images/') . $image->url_image;
            if (file_exists($imagePath)) {
                unlink($imagePath); // Xóa file ảnh từ thư mục
            }
            $image->delete(); // Xóa bản ghi ảnh trong cơ sở dữ liệu
        }

        // Xóa các dữ liệu phân tích liên quan đến bài viết
        PostAnalysisData::where('post_id', $post->id)->delete();

        // Xóa bài viết
        $isDeleteSuccess = $post->delete();

        if ($isDeleteSuccess) {
            return response()->json([
                'status' => true,
                'message' => 'delete post success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'delete post fail',
            ], 200);
            }
        }



        public function getPostQuestionById(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'id_post' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'error' => $validator->errors()
                ], 200);
            }
            $post = Post::where('id_post', $request->id_post)->first();
            if ($post) {
                $post->images = Image::where('id_query_image', $post->id_post)->get();
                foreach ($post->images as $image) {
                    $image->url_image = url('/images/' . $image->url_image);
                }
                $post->timeAgo = $post->created_at->diffForHumans();
                $post->userCreate;
                $post->userCreate->avatar = url('/images/' . $post->userCreate->profile->id_image);
                $post->classNumber = $post->classRoom->name_class;
                $post->subjectName = $post->subject ? $post->subject->name_subject : null;
                unset($post->classRoom);
                unset($post->subject);
                unset($post->userCreate->profile);

                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'data' => $post
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'post not found',
                ], 200);
            }
        }

        public function getCommentPost(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'id_post' => 'required',
                'limit' => 'numeric',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'error' => $validator->errors()
                ], 200);
            }
            $post = Post::where('id_post', $request->id_post)->first();
            if (empty($post)) {
                return response()->json([
                    'status' => false,
                    'message' => 'post not found',
                ], 200);
            }
            foreach ($post->getComments as $comment) {
                $comment->images = Image::where('id_query_image', $comment->comment_id)->get();
                foreach ($comment->images as $image) {
                    $image->url_image = url('/images/' . $image->url_image);
                }
                $comment->timeAgo = $comment->created_at->diffForHumans();
                $comment->userCreate;
                $comment->userCreate->avatar = url('/images/' . $comment->userCreate->profile->id_image);
                unset($comment->userCreate->profile);
            }
            $post->comments = $post->getComments;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = $request->limit ? $request->limit : 10;
            $currentPageSearchResults = $post->comments->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $post->comments = new LengthAwarePaginator($currentPageSearchResults, count($post->comments), $perPage);
            $post->comments->setPath($request->url() . '/?id_post=' . $request->id_post . '&limit=' . $request->limit);
            $posts = $post->comments->toArray();
            // reverse array
            $posts['data'] = $posts['data'] ? array_reverse($posts['data']) : [];
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $posts
            ], 200);
        }
        public function createCommentPost(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required',
                'id_post' => 'required',
                'user_id' => 'required|numeric',
                'title_comment' => 'required',
                'description_comment' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'error' => $validator->errors()
                ], 200);
            }

            $comment = new CommentPost();
            $comment->comment_id = $request->comment_id . uniqid() . uniqid();
            $comment->user_id = $request->user_id;
            $comment->post_id = $request->id_post;
            $comment->title = $request->title_comment;
            $comment->body = $request->description_comment;
            $isCreateComment = $comment->save();
            // image file
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = uniqid() . $image->getClientOriginalName();
                $image->move(public_path() . '/images/', $name);
                $image = new Image();
                $image->id_image = 'comment' . $comment->comment_id . uniqid() . 'image';
                $image->url_image = $name;
                $image->id_query_image = $comment->comment_id;
                $isCreateImageComment = $image->save();
                if (!$isCreateImageComment) {
                    $comment->delete();
                    return response()->json([
                        'status' => false,
                        'message' => 'create comment fail',
                    ], 200);
                }
            }
            if ($isCreateComment) {
                $comment->images = Image::where('id_query_image', $comment->comment_id)->get();
                foreach ($comment->images as $image) {
                    $image->url_image = url('/images/' . $image->url_image);
                }
                $comment->timeAgo = $comment->created_at->diffForHumans();
                $comment->userCreate;
                $comment->userCreate->avatar = url('/images/' . $comment->userCreate->profile->id_image);
                unset($comment->userCreate->profile);
                return response()->json([
                    'status' => true,
                    'message' => 'create comment success',
                    'data' => $comment
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'create comment fail',
                ], 200);
            }
        }
    }
