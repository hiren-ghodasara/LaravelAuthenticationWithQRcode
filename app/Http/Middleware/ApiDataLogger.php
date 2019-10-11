<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiDataLogger
{
    private $startTime;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->startTime = microtime(true);
        return $next($request);
    }

    public function terminate($request, Response $response)
    {

        if (env('API_DATALOGGER', true)) {
            $endTime = microtime(true);
            $dataToLog = 'Time: ' . gmdate("F j, Y, g:i a") . "\n";
            $dataToLog .= 'Duration: ' . number_format($endTime - LARAVEL_START, 3) . "\n";
            $dataToLog .= 'IP Address: ' . $request->ip() . "\n";
            $dataToLog .= 'URL: ' . $request->fullUrl() . "\n";
            $dataToLog .= 'Method: ' . $request->method() . "\n";
            $dataToLog .= 'Status Code: ' . $response->getStatusCode() . "\n";
            $dataToLog .= 'Input: ' . $request->getContent() . "\n";
            $dataToLog .= 'Output: ' . $response->getContent() . "\n";
            if ($response->isClientError() || $response->isServerError()) {
                logger()->error($dataToLog);
            } else {
                logger()->debug($dataToLog);
            }
            // \File::append(storage_path('logs' . DIRECTORY_SEPARATOR . $filename), $dataToLog . "\n" . str_repeat("=", 20) . "\n\n");
        }
    }
}
