<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Throwable;

class Handler
{
    /**
     * Handle exception and send email if needed
     */
    public static function handleException(Throwable $exception): void
    {
        try {
            // Add debug logging to verify handler is being called
            Log::info('Exception email service called for: ' . get_class($exception));
            
            // Send email notification for critical errors
            if (self::shouldSendErrorEmail($exception)) {
                Log::info('Attempting to send error email for: ' . get_class($exception));
                self::sendErrorEmail($exception);
            } else {
                Log::info('Skipping error email for: ' . get_class($exception) . ' - Reason: ' . self::getSkipReason($exception));
            }
        } catch (Throwable $e) {
            Log::error('Exception in ExceptionEmailService: ' . $e->getMessage());
        }
    }

    /**
     * Determine if we should send an error email
     */
    private static function shouldSendErrorEmail(Throwable $exception): bool
    {
        // For debugging - temporarily allow in all environments
        // Remove this condition once testing is complete
        if (config('app.debug_error_emails', false)) {
            Log::info('Debug mode enabled - sending error emails in all environments');
            return true;
        }
        
        // Only send emails in production environment
        if (!App::isProduction()) {
            return false;
        }

        // Skip certain exception types that are not critical
        $skipTypes = [
            \Illuminate\Validation\ValidationException::class,
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        ];

        foreach ($skipTypes as $type) {
            if ($exception instanceof $type) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get reason why error email was skipped (for debugging)
     */
    private static function getSkipReason(Throwable $exception): string
    {
        if (!App::isProduction() && !config('app.debug_error_emails', false)) {
            return 'Not in production environment';
        }

        $skipTypes = [
            \Illuminate\Validation\ValidationException::class => 'Validation exception',
            \Illuminate\Auth\AuthenticationException::class => 'Authentication exception',
            \Illuminate\Auth\Access\AuthorizationException::class => 'Authorization exception',
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => '404 Not Found',
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class => 'Method not allowed',
        ];

        foreach ($skipTypes as $type => $reason) {
            if ($exception instanceof $type) {
                return $reason;
            }
        }

        return 'Unknown reason';
    }

    /**
     * Send error notification email to developers
     */
    private static function sendErrorEmail(Throwable $exception): void
    {
        try {
            Log::info('Starting to send error email...');
            
            $request = request();
            $errorData = self::prepareErrorData($exception, $request);
            
            // Get developer emails from config
            $devEmails = config('app.dev_emails', ['nanda.kumar@fidelisgroup.in']);
            
            if (empty($devEmails)) {
                Log::warning('No developer emails configured in app.dev_emails');
                return;
            }

            Log::info('Sending error email to: ' . implode(', ', $devEmails));

            Mail::send([], [], function ($message) use ($errorData, $devEmails) {
                $message->to($devEmails)
                        ->subject('[' . config('app.name') . '] Application Error - ' . $errorData['error_type'])
                        ->html(self::generateEmailMarkdown($errorData));
            });

            Log::info('Error email sent successfully');

        } catch (Throwable $e) {
            // Log the email sending error but don't let it break the application
            Log::error('Failed to send error email: ' . $e->getMessage());
            Log::error('Email error stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Prepare error data for email
     */
    private static function prepareErrorData(Throwable $exception, Request $request): array
    {
        return [
            'error_type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'environment' => App::environment(),
            'request_data' => self::sanitizeRequestData($request),
            'headers' => self::sanitizeHeaders($request->headers->all()),
        ];
    }

    /**
     * Generate HTML email content using markdown-style formatting
     */
    private static function generateEmailMarkdown(array $errorData): string
    {
        $html = "
        <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; max-width: 800px; margin: 0 auto; background: #f8f9fa;'>
            <div style='background: #dc3545; color: white; padding: 20px; border-radius: 8px 8px 0 0;'>
                <h1 style='margin: 0; font-size: 24px;'>🚨 Application Error Alert</h1>
                <p style='margin: 10px 0 0 0; opacity: 0.9;'>" . config('app.name') . " - " . $errorData['environment'] . "</p>
            </div>
            
            <div style='background: white; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                
                <!-- Error Overview -->
                <div style='background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px; padding: 20px; margin-bottom: 25px;'>
                    <h2 style='color: #c53030; margin: 0 0 15px 0; font-size: 18px;'>📍 Error Details</h2>
                    <div style='background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 4px; font-family: \"SF Mono\", Monaco, monospace; font-size: 14px; overflow-x: auto;'>
                        <strong style='color: #f56565;'>" . htmlspecialchars($errorData['error_type']) . "</strong><br>
                        " . htmlspecialchars($errorData['message']) . "
                    </div>
                    <div style='margin-top: 15px; font-size: 14px; color: #4a5568;'>
                        <strong>📁 File:</strong> " . htmlspecialchars($errorData['file']) . "<br>
                        <strong>📍 Line:</strong> " . $errorData['line'] . "<br>
                        <strong>⏰ Time:</strong> " . $errorData['timestamp'] . "
                    </div>
                </div>

                <!-- Request Information -->
                <div style='background: #f0f8ff; border: 1px solid #bee3f8; border-radius: 6px; padding: 20px; margin-bottom: 25px;'>
                    <h2 style='color: #2b6cb0; margin: 0 0 15px 0; font-size: 18px;'>🌐 Request Information</h2>
                    <div style='font-size: 14px; line-height: 1.6;'>
                        <p><strong>URL:</strong> <code style='background: #e2e8f0; padding: 2px 6px; border-radius: 3px; font-family: monospace;'>" . htmlspecialchars($errorData['url']) . "</code></p>
                        <p><strong>Method:</strong> <span style='background: #48bb78; color: white; padding: 2px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;'>" . $errorData['method'] . "</span></p>
                        <p><strong>IP Address:</strong> " . htmlspecialchars($errorData['ip']) . "</p>
                        <p><strong>User Agent:</strong> " . htmlspecialchars($errorData['user_agent']) . "</p>
                        " . ($errorData['user_id'] ? "<p><strong>User ID:</strong> " . $errorData['user_id'] . "</p>" : "") . "
                    </div>
                </div>";

        // Add request data if present
        if (!empty($errorData['request_data'])) {
            $html .= "
                <div style='background: #fffaf0; border: 1px solid #fbd38d; border-radius: 6px; padding: 20px; margin-bottom: 25px;'>
                    <h2 style='color: #c05621; margin: 0 0 15px 0; font-size: 18px;'>📊 Request Data</h2>
                    <div style='background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 13px; overflow-x: auto; max-height: 300px; overflow-y: auto;'>
                        " . htmlspecialchars(json_encode($errorData['request_data'], JSON_PRETTY_PRINT)) . "
                    </div>
                </div>";
        }

        // Stack trace
        $html .= "
                <div style='background: #f7fafc; border: 1px solid #cbd5e0; border-radius: 6px; padding: 20px;'>
                    <h2 style='color: #4a5568; margin: 0 0 15px 0; font-size: 18px;'>🔍 Stack Trace</h2>
                    <details style='cursor: pointer;'>
                        <summary style='color: #2d3748; font-weight: 600; margin-bottom: 10px;'>Click to view stack trace</summary>
                        <div style='background: #1a202c; color: #e2e8f0; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px; line-height: 1.4; overflow-x: auto; max-height: 400px; overflow-y: auto; white-space: pre-wrap;'>
" . htmlspecialchars($errorData['trace']) . "
                        </div>
                    </details>
                </div>
                
                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #718096;'>
                    <p style='margin: 0;'>This email was automatically generated by the error monitoring system.</p>
                    <p style='margin: 5px 0 0 0;'>Environment: <strong>" . $errorData['environment'] . "</strong></p>
                </div>
            </div>
        </div>";

        return $html;
    }

    /**
     * Sanitize request data to remove sensitive information
     */
    private static function sanitizeRequestData(Request $request): array
    {
        $data = $request->all();
        
        // Remove sensitive fields
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'card_number',
            'cvv',
            'ssn',
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        // Recursively sanitize nested arrays
        array_walk_recursive($data, function (&$value, $key) use ($sensitiveFields) {
            if (in_array(strtolower($key), $sensitiveFields)) {
                $value = '[REDACTED]';
            }
        });

        return $data;
    }

    /**
     * Sanitize headers to remove sensitive information
     */
    private static function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization',
            'x-api-key',
            'x-auth-token',
            'cookie',
            'set-cookie',
        ];

        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = '[REDACTED]';
            }
        }

        return $headers;
    }
}