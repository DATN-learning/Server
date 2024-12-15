<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotController extends Controller
{
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string',
            ]);

            $ollamaPayload = [
                'model' => 'gemma2:2b',
                'messages' => [
                    ['role' => 'user', 'content' => $request->message],
                ],
                'stream' => false,
            ];

            $response = Http::post(env('OLLAMA_API_BASE_URL') . '/chat', $ollamaPayload);
                if ($response->successful()) {
                return response()->json([
                    'message' => $response->json()['message']['content'] ?? '',
                ]);
            }

            Log::error('Ollama API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return response()->json([
                'error' => 'Ollama API returned an error. Please check the logs.',
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('ChatBotController Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            return response()->json([
                'error' => 'Đã xảy ra lỗi khi xử lý yêu cầu.',
            ], 500);
        }
    }
}
