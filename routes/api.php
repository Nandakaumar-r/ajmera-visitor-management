<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Services\GroqService;
use App\Services\VisitingCardOcrService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\EmployeeInsuranceController;

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

Route::post('/auth/login', [EmployeeApiController::class, 'login']);

Route::prefix('v1')->group(function () {
    // Insurance routes
    Route::apiResource('insurances', EmployeeInsuranceController::class);

    // Additional routes
    Route::get('insurances-statistics', [EmployeeInsuranceController::class, 'statistics']);
    Route::get('insurances-export', [EmployeeInsuranceController::class, 'export']);
});

Route::get('/employees/{employeeId}/details', [EmployeeApiController::class, 'details']);
Route::prefix('notes')->group(function () {
    Route::get('{employeeId}', [NoteController::class, 'index']);
    Route::post('/', [NoteController::class, 'store']);
    Route::put('{note}', [NoteController::class, 'update']);
    Route::delete('{note}', [NoteController::class, 'destroy']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth'])->group(function () {
    Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage']);
    Route::post('/chatbot/feedback', [ChatbotController::class, 'submitFeedback']);
});

Route::post('/visiting-card/analyze', function (Request $request) {
    try {
        $ocrService = app(VisitingCardOcrService::class);
        $result = $ocrService->processVisitingCard($request->input('text'));
        
        if ($result['success']) {
            return response()->json($result['data']);
        }
        
        return response()->json([
            'error' => $result['error'] ?? 'Failed to analyze image',
            'message' => 'The image could not be processed. Please try again.'
        ], 500);
    } catch (\Exception $e) {
        Log::error('API Error in visiting card analysis', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Internal server error',
            'message' => 'An unexpected error occurred. Please try again.'
        ], 500);
    }
});

Route::post('/analyze-visiting-card', function (Request $request) {
    try {
        $imageData = $request->input('image');
        
        if (empty($imageData)) {
            return response()->json([
                'error' => 'No image data provided',
                'message' => 'Please provide an image to analyze'
            ], 400);
        }

        $groqService = app(GroqService::class);
        $tesseractService = app(VisitingCardOcrService::class);
        
        // First get OCR text from Tesseract
        $tesseractResult = $tesseractService->processVisitingCard($imageData);
        
        if (!$tesseractResult['success']) {
            return response()->json([
                'error' => 'OCR processing failed',
                'message' => 'Could not extract text from the image'
            ], 500);
        }

        // Now analyze the extracted text with Groq
        $groqResult = $groqService->analyzeText($tesseractResult['data']['raw_text']);
        
        if ($groqResult) {
            return response()->json([
                'success' => true,
                'data' => array_merge($groqResult, [
                    'image_path' => $tesseractResult['data']['image_path'] ?? null,
                    'raw_text' => $tesseractResult['data']['raw_text'] ?? null
                ])
            ]);
        }

        return response()->json([
            'error' => 'Analysis failed',
            'message' => 'Could not analyze the visiting card text'
        ], 500);
    } catch (\Exception $e) {
        Log::error('Error analyzing visiting card', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Internal server error',
            'message' => 'An unexpected error occurred while analyzing the visiting card'
        ], 500);
    }
});
