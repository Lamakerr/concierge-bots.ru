<?php

namespace App\Jobs;

use App\Models\RequestLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fallbackUsername;
    protected $requestType;
    protected $query;

    public function __construct($fallbackUsername, $requestType, $query)
    {
        $this->fallbackUsername = $fallbackUsername;
        $this->requestType = $requestType;
        $this->query = $query;
    }

    public function handle()
    {
        // Log::info("Logging Request with user_name: ". $this->fallbackUsername);
        RequestLog::create([
            'user_name' => $this->fallbackUsername,
            'request_type' => $this->requestType,
            'query' => $this->query,
        ]);
    }
}