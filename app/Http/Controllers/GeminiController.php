<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\CommonMark\CommonMarkConverter;
use Prism\Prism\Prism;

class GeminiController extends Controller
{
    public function generateText(Request $request)
    {
        $prompt = $request->input('prompt');

        $response = Prism::text()
            ->using('gemini', 'gemini-2.5-flash')
            ->withPrompt($prompt)
            ->asText();
        $converter = new CommonMarkConverter();
        $generated_text = $converter->convert($response->text)->getContent();
        return response()->json([
            'generated_text' => $generated_text,
            'usage' => $response->usage ?? null
        ]);
    }

    public function chatWithGemini(Request $request)
    {
        $messages = $request->input('messages', []);

        $response = Prism::text()
            ->using('gemini', 'gemini-1.5-pro')
            ->withMessages($messages)
            ->withTemperature(0.7)
            ->generate();

        return response()->json([
            'response' => $response->text,
            'finish_reason' => $response->finishReason ?? null
        ]);
    }

    // Gemini Pro for complex tasks
    public function complexAnalysis(Request $request)
    {
        $data = $request->input('data');

        $response = Prism::text()
            ->using('gemini', 'gemini-1.5-pro')
            ->withPrompt("Analyze this data and provide insights: " . json_encode($data))
            ->withMaxTokens(1000)
            ->generate();

        return response()->json([
            'analysis' => $response->text
        ]);
    }

    // Multimodal capabilities with Gemini
    public function analyzeImage(Request $request)
    {
        $imageUrl = $request->input('image_url');
        $question = $request->input('question', 'What do you see in this image?');

        $response = Prism::text()
            ->using('gemini', 'gemini-1.5-pro')
            ->withPrompt($question)
            ->withImages([$imageUrl])
            ->generate();

        return response()->json([
            'image_analysis' => $response->text
        ]);
    }
}