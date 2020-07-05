<?php

namespace App\Http\Middleware\accesslog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\HttpLogger\DefaultLogWriter as DefaultLogWriter;

class LogWriter extends DefaultLogWriter
{
    public function logRequest(Request $request): void
    {
        $method = strtoupper($request->getMethod());

        $uri = $request->getPathInfo();

        $bodyAsJson = json_encode($request->except(config('http-logger.except')));

        $message = "{$method} {$uri} - {$bodyAsJson}";

        Log::info($message);
    }
}
