<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Import the Controller class from the correct namespace
use GuzzleHttp\Client;

class ChatGptController extends Controller
{
    public function ask(Request $request)
    {
        // Đảm bảo bạn đã thêm OpenAI API key vào file .env
        $apiKey = env('OPENAI_API_KEY');

        // Tạo client Guzzle
        $client = new Client();

        // Đặt câu hỏi từ request
        $question = $request->input('question', ''); // Lấy câu hỏi từ request

        if (empty($question)) {
            return response()->json(['error' => 'Câu hỏi không được để trống'], 400);
        }

        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [['role' => 'user', 'content' => $request->input('question')]],
                ],
            ]);
            $responseBody = json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 429) {
                // Xử lý lỗi quá nhiều yêu cầu
                return response()->json([
                    'status' => false,
                    'message' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.',
                ], 429);
            } else {
                // Xử lý các lỗi khác
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        }
        
    }
}
