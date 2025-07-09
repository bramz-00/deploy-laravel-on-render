<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OpenAIController extends Controller
{
    public function __construct(
        private OpenAIService $openAIService
    ) {}

    public function index()
    {
        return Inertia::render('OpenAI/Chat', [
            'title' => 'AI Chat Assistant'
        ]);
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversation' => 'array'
        ]);

        try {
            // Build conversation history
            $messages = $request->conversation ?? [];
            
            // Add user message
            $messages[] = [
                'role' => 'user',
                'content' => $request->message
            ];

            // Get AI response
            $response = $this->openAIService->chat($messages);
            $aiMessage = $response['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';

            // Add AI response to conversation
            $messages[] = [
                'role' => 'assistant',
                'content' => $aiMessage
            ];

            return response()->json([
                'success' => true,
                'response' => $aiMessage,
                'conversation' => $messages,
                'usage' => $response['usage'] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get AI response: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'size' => 'in:256x256,512x512,1024x1024',
            'n' => 'integer|min:1|max:4'
        ]);

        try {
            $response = $this->openAIService->generateImage($request->prompt, [
                'size' => $request->size ?? '1024x1024',
                'n' => $request->n ?? 1
            ]);

            return response()->json([
                'success' => true,
                'images' => $response['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function textCompletion(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000',
            'model' => 'string|in:gpt-3.5-turbo,gpt-4,gpt-4-turbo-preview'
        ]);

        try {
            $response = $this->openAIService->generateText(
                $request->prompt,
                $request->model ?? 'gpt-3.5-turbo'
            );

            return response()->json([
                'success' => true,
                'text' => $response
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate text: ' . $e->getMessage()
            ], 500);
        }
    }
}