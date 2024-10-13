<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class ChatBotController extends Controller
{
    public function chat(Request $request)
    {
        try {
            // Validate request input
            $request->validate([
                'message' => 'required|string',
            ]);

            // Gửi yêu cầu đến OpenAI
            $result = OpenAI::chat()->create([
                'max_tokens' => 1000,
                'model' => 'gpt-3.5-turbo',
                'prompt' => $request->message
            ]);

            // Lấy phản hồi từ OpenAI
            $response = array_reduce(
                $result->toArray()['choices'],
                fn(string $result, array $choice) => $result . $choice['text'],
                ""
            );

            return response()->json(['response' => $response]);

        } catch (\Exception $e) {
            // Log lỗi và trả về thông báo lỗi
            Log::error('ChatGPT controller error: ' . $e->getMessage());
            return response()->json(['error' => 'Đã xảy ra lỗi khi xử lý yêu cầu'], 500);
        }
    }
}
