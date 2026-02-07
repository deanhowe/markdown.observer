<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogController extends Controller
{
    /**
     * Log a message from the client
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|string|in:emergency,alert,critical,error,warning,notice,info,debug',
            'message' => 'required|string|max:1000',
            'context' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid log data',
                'errors' => $validator->errors(),
                'meta' => [
                    'api_version' => 'v1',
                ],
            ], 422);
        }

        $level = $request->input('level');
        $message = $request->input('message');
        $context = $request->input('context', []);

        // Add client information to the context
        $context['client'] = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        // Add authenticated user information if available
        if ($request->user()) {
            $context['user'] = [
                'id' => $request->user()->id,
                'email' => $request->user()->email,
            ];
        }

        // Log to the appropriate channel based on the context
        if (isset($context['error']) && str_contains(strtolower($message), 'markdown')) {
            Log::channel('markdown')->log($level, $message, $context);
        } else {
            Log::log($level, $message, $context);
        }

        return response()->json([
            'success' => true,
            'meta' => [
                'api_version' => 'v1',
            ],
        ]);
    }
}
