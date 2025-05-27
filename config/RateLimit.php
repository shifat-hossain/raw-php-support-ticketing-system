<?php

class RateLimit
{
    public function rateLimit($maxRequests = 100, $timeWindow = 60)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $logFile = 'public/rate_limit.json';

        $log = json_decode(file_get_contents($logFile), true) ?? [];
        
        $now = time();
        
        // Remove expired entries
        foreach ($log as $loggedIp => $timestamps) {
            $log[$loggedIp] = array_filter($timestamps, fn($ts) => $ts > $now - $timeWindow);
        }

        // Add this request
        $log[$ip][] = $now;
        
        if (count($log[$ip]) > $maxRequests) {
            http_response_code(429);
            echo json_encode(['message' => 'Too Many Requests']);
            exit;
        }

        // Save back to file
        file_put_contents($logFile, json_encode($log));
    }
}
