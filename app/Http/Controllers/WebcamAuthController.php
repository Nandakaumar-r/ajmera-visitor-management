<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Routing\Controller as BaseController;

class WebcamAuthController extends BaseController
{
    private $similarityThreshold = 0.85;  // Increased threshold for better security
    private $maxAttempts = 50;  // Maximum login attempts
    private $decayMinutes = 10;  // Lockout duration in minutes

    public function __construct()
    {
        $this->middleware('throttle:' . $this->maxAttempts . ',' . $this->decayMinutes)->only(['processLogin']);
    }

    public function showLoginForm()
    {
        return view('auth.webcam-login');
    }

    public function processLogin(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'webcam_image' => 'required|string|min:100'  // Basic validation for base64 image
            ]);

            $webcamImage = $request->input('webcam_image');
            
            // Validate base64 image format
            if (!preg_match('/^data:image\/(\w+);base64,/', $webcamImage, $type)) {
                throw new Exception('Invalid image format');
            }
            
            // Check image type
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                throw new Exception('Invalid image type');
            }
            
            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $webcamImage));
            if ($imageData === false) {
                throw new Exception('Failed to decode image');
            }
            
            // Generate unique filename
            $filename = uniqid('webcam_') . '.jpg';
            $tempImagePath = storage_path('app/temp/' . $filename);
            $tempDir = dirname($tempImagePath);
            
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            if (!file_put_contents($tempImagePath, $imageData)) {
                throw new Exception('Failed to save temporary image');
            }

            try {
                // Find matching user using face detection
                $matchedUser = $this->findMatchingUser($tempImagePath);
            } finally {
                // Always clean up temporary file
                @unlink($tempImagePath);
            }

            if ($matchedUser) {
                // Reset login attempts on successful login
                $this->resetLoginAttempts($request);
                
                Auth::login($matchedUser);
                return response()->json([
                    'success' => true,
                    'message' => 'Authentication successful',
                    'redirect' => route('dashboard')
                ]);
            }

            // Increment failed login attempts
            $this->incrementLoginAttempts($request);

            return response()->json([
                'success' => false,
                'message' => 'Face not recognized. Please try again or use password login.',
                'attempts_remaining' => $this->maxAttempts - $this->attempts($request)
            ]);

        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Webcam authentication error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function updateFaceProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $webcamImage = $request->input('webcam_image');
            
            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $webcamImage));
            
            // Save temporary image
            $tempImagePath = storage_path('app/temp/profile_' . time() . '.jpg');
            $tempDir = dirname($tempImagePath);
            
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            file_put_contents($tempImagePath, $imageData);

            // Extract face features
            $features = $this->extractFaceFeatures($tempImagePath);
            
            if ($features === null) {
                @unlink($tempImagePath);
                return response()->json([
                    'success' => false,
                    'message' => 'Could not detect face. Please ensure good lighting and face is clearly visible.'
                ]);
            }
            
            // Save features to user profile
            $user->face_embedding = json_encode($features);
            $user->save();

            // Clean up temporary file
            @unlink($tempImagePath);

            return response()->json([
                'success' => true,
                'message' => 'Face profile updated successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update face profile: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Find a user matching the face in the provided image
     */
    protected function findMatchingUser($imagePath)
    {
        // Path to the Python script
        $pythonScript = storage_path('app/face_detection_helper.py');
        
        // Run the Python script and capture its output
        $command = "python \"{$pythonScript}\" \"{$imagePath}\"";
        $output = shell_exec($command);
        
        if (empty($output)) {
            throw new Exception('Face detection failed: No output from script');
        }
        
        // Parse the JSON output from the Python script
        $result = json_decode($output, true);
        
        // Check for errors returned by the script
        if (isset($result['error'])) {
            throw new Exception('Face detection failed: ' . $result['error']);
        }
        
        // Validate the features format
        if (!isset($result['features']) || !is_array($result['features'])) {
            throw new Exception('Face detection failed: Invalid features format');
        }
        
        $features = $result['features'];
        
        // Log the feature array length for debugging
        Log::info('Feature length: ' . (isset($result['length']) ? $result['length'] : count($features)));
        
        // Get all users with face features stored
        $users = User::whereNotNull('face_embedding')->get();
        
        $bestMatch = null;
        $bestScore = 0;
        $threshold = 0.7; // Minimum similarity score to consider a match (0.0-1.0)
        
        // Compare against each user's stored face features
        foreach ($users as $user) {
            // Safely decode user's stored features
            $storedFeatures = json_decode($user->face_embedding, true);
            
            if (!is_array($storedFeatures)) {
                continue; // Skip invalid stored features
            }
            
            // Make sure arrays have the same length before comparison
            // This is crucial to avoid the "Undefined array key" error
            $featuresToCompare = $features;
            $storedToCompare = $storedFeatures;
            
            // Make sure both arrays have the same length
            $minLength = min(count($featuresToCompare), count($storedToCompare));
            $featuresToCompare = array_slice($featuresToCompare, 0, $minLength);
            $storedToCompare = array_slice($storedToCompare, 0, $minLength);
            
            // Calculate similarity score using cosine similarity
            $similarity = $this->calculateCosineSimilarity($featuresToCompare, $storedToCompare);
            
            // Log similarity scores for debugging
            Log::info("Similarity with user {$user->id}: $similarity");
            
            // Keep track of the best match
            if ($similarity > $bestScore && $similarity >= $threshold) {
                $bestScore = $similarity;
                $bestMatch = $user;
            }
        }
        
        // Log the best match for debugging
        if ($bestMatch) {
            Log::info("Best match: User {$bestMatch->id} with score {$bestScore}");
        } else {
            Log::info("No match found above threshold {$threshold}");
        }
        
        return $bestMatch;
    }

    private function extractFaceFeatures($imagePath)
    {
        $pythonScript = storage_path('app/face_detection_helper.py');
        
        // Create Python helper script if it doesn't exist
        if (!file_exists($pythonScript)) {
            $this->createPythonHelper();
        }

        // Find Python executable
        $pythonPath = $this->findPythonPath();
        if (!$pythonPath) {
            throw new Exception('Python not found. Please ensure Python is installed and in your PATH.');
        }

        // Create process with absolute paths and environment variables
        $process = new Process([$pythonPath, $pythonScript, $imagePath]);
        $process->setWorkingDirectory(storage_path('app'));
        
        // Set environment variables to avoid random initialization issues
        $env = array_merge($_ENV, [
            'PYTHONHASHSEED' => '0',
            'PYTHONPATH' => dirname($pythonScript)
        ]);
        $process->setEnv($env);
        
        $process->setTimeout(30);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $result = json_decode($process->getOutput(), true);
        return $result['features'] ?? null;
    }

    private function findPythonPath()
    {
        // Try different Python commands
        $commands = ['python', 'python3', 'py'];
        
        foreach ($commands as $cmd) {
            try {
                $process = new Process([$cmd, '--version']);
                $process->run();
                
                if ($process->isSuccessful()) {
                    return $cmd;
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        return null;
    }

    private function createPythonHelper()
    {
        $pythonCode = <<<'PYTHON'
            import sys
            import cv2
            import numpy as np
            import json
            import os
            import hashlib

            def extract_face_features(image_path):
                try:
                    # Read image
                    image = cv2.imread(image_path)
                    if image is None:
                        return None, "Failed to read image"
                        
                    # Convert to grayscale
                    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
                    
                    # Load face cascade classifier
                    cascade_path = os.path.join(cv2.data.haarcascades, 'haarcascade_frontalface_default.xml')
                    if not os.path.exists(cascade_path):
                        return None, f"Cascade file not found at {cascade_path}"
                        
                    face_cascade = cv2.CascadeClassifier(cascade_path)
                    
                    # Detect faces
                    faces = face_cascade.detectMultiScale(
                        gray,
                        scaleFactor=1.1,
                        minNeighbors=5,
                        minSize=(30, 30)
                    )
                    
                    if len(faces) == 0:
                        return None, "No face detected"
                    elif len(faces) > 1:
                        return None, "Multiple faces detected"
                    
                    # Get the face
                    x, y, w, h = faces[0]
                    
                    # Extract face region
                    face_roi = gray[y:y+h, x:x+w]
                    
                    # Resize to standard size
                    face_roi = cv2.resize(face_roi, (128, 128))
                    
                    # Apply histogram equalization
                    face_roi = cv2.equalizeHist(face_roi)
                    
                    # Calculate simple features (pixel values and gradients)
                    features = []
                    
                    # Add pixel values (downsampled)
                    downsampled = cv2.resize(face_roi, (32, 32)).flatten()
                    features.extend(downsampled.tolist())
                    
                    # Add horizontal and vertical gradients
                    gx = cv2.Sobel(face_roi, cv2.CV_64F, 1, 0, ksize=3)
                    gy = cv2.Sobel(face_roi, cv2.CV_64F, 0, 1, ksize=3)
                    
                    # Downsample gradients
                    gx = cv2.resize(gx, (32, 32)).flatten()
                    gy = cv2.resize(gy, (32, 32)).flatten()
                    
                    features.extend(gx.tolist())
                    features.extend(gy.tolist())
                    
                    # Normalize features
                    features = np.array(features)
                    if np.sum(features) != 0:
                        features = features / np.sum(features)
                    
                    return features.tolist(), None
                    
                except Exception as e:
                    return None, str(e)

            if __name__ == "__main__":
                if len(sys.argv) != 2:
                    print(json.dumps({"error": "Invalid arguments"}))
                    sys.exit(1)
                
                image_path = sys.argv[1]
                features, error = extract_face_features(image_path)
                
                if features is None:
                    print(json.dumps({"error": error}))
                    sys.exit(1)
                
                print(json.dumps({"features": features}))
            PYTHON;

                    $scriptPath = storage_path('app/face_detection_helper.py');
                    $scriptDir = dirname($scriptPath);
                    
                    if (!file_exists($scriptDir)) {
                        mkdir($scriptDir, 0777, true);
                    }
                    
                    file_put_contents($scriptPath, $pythonCode);
                    
                    // Set proper permissions
                    chmod($scriptPath, 0755);
    }

    private function calculateSimilarity($features1, $features2)
    {
        // Convert to numpy arrays
        $f1 = array_values($features1);
        $f2 = array_values($features2);
        
        // Calculate cosine similarity
        $dot = 0;
        $norm1 = 0;
        $norm2 = 0;
        
        foreach ($f1 as $i => $val1) {
            $val2 = $f2[$i];
            $dot += $val1 * $val2;
            $norm1 += $val1 * $val1;
            $norm2 += $val2 * $val2;
        }
        
        if ($norm1 == 0 || $norm2 == 0) {
            return 0;
        }
        
        return $dot / (sqrt($norm1) * sqrt($norm2));
    }

        /**
     * Calculate cosine similarity between two feature vectors
     * This measures how similar two faces are (1.0 = identical, 0.0 = completely different)
     */
    protected function calculateCosineSimilarity($vector1, $vector2)
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        // For each feature in the vectors
        foreach ($vector1 as $i => $value) {
            $dotProduct += $value * $vector2[$i];
            $magnitude1 += $value * $value;
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }
        
        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Get the number of login attempts for the request
     */
    protected function attempts(Request $request)
    {
        $key = $this->throttleKey($request);
        return cache()->get($key, 0);
    }

    /**
     * Increment the login attempts for the request.
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);
        cache()->put($key, $this->attempts($request) + 1, 60);
    }

    /**
     * Reset the login attempts for the request.
     */
    protected function resetLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);
        cache()->forget($key);
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request)
    {
        return 'face_login|' . $request->ip();
    }
}
