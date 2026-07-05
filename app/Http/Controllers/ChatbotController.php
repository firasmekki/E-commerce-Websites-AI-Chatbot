<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    public function __construct(
        private ChatbotService $chatbotService
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $response = $this->chatbotService->processMessage(
                $request->user(),
                $request->message
            );

            return response()->json([
                'reply' => $response,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'reply' => 'Desole, une erreur est survenue. Veuillez reessayer plus tard.',
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $history = $request->user()->chatHistory()
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($history);
    }
}
