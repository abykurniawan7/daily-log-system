<?php

/**
 * QUICK PERFORMANCE TEST
 * Test public pages yang tidak butuh authentication
 * 
 * Usage: php quick-perf-test.php
 */

$baseUrl = 'http://127.0.0.1:8000';

// Test public endpoints
$endpoints = [
    '/' => 'Homepage',
    '/login' => 'Login Page',
];

echo "═══════════════════════════════════════════════════════════\n";
echo "  QUICK PERFORMANCE TEST - PUBLIC PAGES\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$results = [];

foreach ($endpoints as $path => $name) {
    echo "Testing: {$name} ({$path})\n";
    
    $url = $baseUrl . $path;
    $times = [];
    
    // Run 10 requests
    for ($i = 0; $i < 10; $i++) {
        $start = microtime(true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        $end = microtime(true);
        $time = ($end - $start) * 1000; // Convert to ms
        
        $times[] = $time;
        
        echo "  Request " . ($i + 1) . ": " . number_format($time, 0) . "ms (HTTP {$httpCode})\n";
        
        usleep(100000); // 100ms delay between requests
    }
    
    $avg = array_sum($times) / count($times);
    $min = min($times);
    $max = max($times);
    
    echo "\n";
    echo "  Average: " . number_format($avg, 0) . "ms\n";
    echo "  Min: " . number_format($min, 0) . "ms\n";
    echo "  Max: " . number_format($max, 0) . "ms\n";
    
    // Status
    if ($avg < 500) {
        echo "  Status: ✅ EXCELLENT\n";
    } elseif ($avg < 1000) {
        echo "  Status: ✅ GOOD\n";
    } elseif ($avg < 2000) {
        echo "  Status: ⚠️  MODERATE\n";
    } else {
        echo "  Status: ❌ SLOW\n";
    }
    
    echo "\n" . str_repeat('─', 60) . "\n\n";
    
    $results[$name] = [
        'avg' => $avg,
        'min' => $min,
        'max' => $max,
    ];
}

// Summary
echo "═══════════════════════════════════════════════════════════\n";
echo "  SUMMARY\n";
echo "═══════════════════════════════════════════════════════════\n\n";

foreach ($results as $name => $data) {
    printf(
        "%-20s Avg: %4dms | Min: %4dms | Max: %4dms\n",
        $name,
        $data['avg'],
        $data['min'],
        $data['max']
    );
}

echo "\n";
echo "ℹ️  Note: Login page is typically slower due to CSRF and session handling.\n";
echo "   For authenticated pages, use load-test-authenticated.php\n";
echo "\n";