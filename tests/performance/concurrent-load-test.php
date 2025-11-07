<?php
/**
 * CONCURRENT LOAD TEST
 * Tests multiple users accessing the system SIMULTANEOUSLY
 * Simulates real-world scenario with 8-20 concurrent users
 */

// ============================================================================
// CONFIGURATION
// ============================================================================
$baseUrl = 'http://127.0.0.1:8000'; // Your app URL
$concurrentUsers = 8; // Start with 8 users
$requestsPerUser = 3; // Each user makes 3 requests

// Test pages (public pages - no auth needed)
$testPages = [
    '/' => 'Homepage',
    '/login' => 'Login Page',
];

// ============================================================================
// COLORS
// ============================================================================
function colorText($text, $color) {
    $colors = [
        'green' => "\033[0;32m",
        'yellow' => "\033[1;33m",
        'red' => "\033[0;31m",
        'blue' => "\033[0;34m",
        'cyan' => "\033[0;36m",
        'reset' => "\033[0m"
    ];
    return $colors[$color] . $text . $colors['reset'];
}

// ============================================================================
// BANNER
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo colorText("  🚀 CONCURRENT LOAD TEST - SIMULTANEOUS USERS", 'cyan') . "\n";
echo str_repeat("=", 80) . "\n";
echo "Testing: " . colorText($concurrentUsers . " concurrent users", 'yellow') . "\n";
echo "Requests per user: " . colorText($requestsPerUser, 'yellow') . "\n";
echo "Total requests: " . colorText($concurrentUsers * $requestsPerUser * count($testPages), 'yellow') . "\n";
echo str_repeat("=", 80) . "\n\n";

// ============================================================================
// TEST FUNCTION
// ============================================================================
function makeRequest($url) {
    $start = microtime(true);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'LoadTest/1.0',
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $duration = (microtime(true) - $start) * 1000;
    
    return [
        'duration' => round($duration, 2),
        'status' => $httpCode,
        'success' => $httpCode == 200 && empty($error),
        'error' => $error
    ];
}

// ============================================================================
// CONCURRENT TESTING
// ============================================================================
$results = [];

foreach ($testPages as $path => $name) {
    $url = $baseUrl . $path;
    
    echo colorText("Testing: $name ($path)", 'blue') . "\n";
    echo "URL: $url\n";
    echo str_repeat("-", 80) . "\n";
    
    // Simulate concurrent users using multi-curl
    $startTime = microtime(true);
    
    $mh = curl_multi_init();
    $handles = [];
    $requests = [];
    
    // Create handles for concurrent users
    for ($user = 1; $user <= $concurrentUsers; $user++) {
        for ($req = 1; $req <= $requestsPerUser; $req++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => "LoadTest-User$user-Req$req",
            ]);
            
            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
            $requests[] = [
                'user' => $user,
                'request' => $req,
                'start' => microtime(true)
            ];
        }
    }
    
    // Execute all handles concurrently
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);
    
    // Collect results
    $durations = [];
    $successes = 0;
    $failures = 0;
    
    foreach ($handles as $i => $ch) {
        $duration = (microtime(true) - $requests[$i]['start']) * 1000;
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $success = $httpCode == 200;
        
        $durations[] = $duration;
        
        if ($success) {
            $successes++;
        } else {
            $failures++;
        }
        
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    
    curl_multi_close($mh);
    
    $totalTime = (microtime(true) - $startTime) * 1000;
    
    // Calculate statistics
    $avgDuration = array_sum($durations) / count($durations);
    $minDuration = min($durations);
    $maxDuration = max($durations);
    
    // Store results
    $results[$name] = [
        'avg' => round($avgDuration, 2),
        'min' => round($minDuration, 2),
        'max' => round($maxDuration, 2),
        'total_time' => round($totalTime, 2),
        'successes' => $successes,
        'failures' => $failures,
        'requests' => count($handles)
    ];
    
    // Display results
    echo colorText("✓ Completed in " . round($totalTime, 2) . "ms (wall clock time)", 'green') . "\n";
    echo "  Total Requests:  " . count($handles) . "\n";
    echo "  Successful:      " . colorText($successes, 'green') . "\n";
    if ($failures > 0) {
        echo "  Failed:          " . colorText($failures, 'red') . "\n";
    }
    echo "  Average Time:    " . round($avgDuration, 2) . "ms\n";
    echo "  Min Time:        " . round($minDuration, 2) . "ms\n";
    echo "  Max Time:        " . round($maxDuration, 2) . "ms\n";
    echo "  Requests/sec:    " . round((count($handles) / $totalTime) * 1000, 2) . "\n";
    
    // Performance assessment
    if ($avgDuration < 500) {
        echo "  Status:          " . colorText("✅ EXCELLENT", 'green') . "\n";
    } elseif ($avgDuration < 1000) {
        echo "  Status:          " . colorText("✅ GOOD", 'yellow') . "\n";
    } elseif ($avgDuration < 2000) {
        echo "  Status:          " . colorText("⚠️  MODERATE", 'yellow') . "\n";
    } else {
        echo "  Status:          " . colorText("❌ SLOW", 'red') . "\n";
    }
    
    echo str_repeat("-", 80) . "\n\n";
    
    // Cool down between tests
    usleep(500000); // 0.5 second
}

// ============================================================================
// SUMMARY
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo colorText("  📊 CONCURRENT LOAD TEST SUMMARY", 'cyan') . "\n";
echo str_repeat("=", 80) . "\n";

foreach ($results as $page => $data) {
    $status = $data['avg'] < 500 ? '✅' : ($data['avg'] < 1000 ? '⚠️' : '❌');
    printf(
        "%-20s %s Avg: %6.0fms | Min: %6.0fms | Max: %6.0fms | Success: %d/%d\n",
        $page,
        $status,
        $data['avg'],
        $data['min'],
        $data['max'],
        $data['successes'],
        $data['requests']
    );
}

echo str_repeat("=", 80) . "\n";

// ============================================================================
// RECOMMENDATIONS
// ============================================================================
$avgAll = array_sum(array_column($results, 'avg')) / count($results);

echo "\n" . colorText("💡 RECOMMENDATIONS:", 'cyan') . "\n";
echo str_repeat("-", 80) . "\n";

if ($avgAll < 500) {
    echo colorText("✅ EXCELLENT!", 'green') . " Your system handles $concurrentUsers concurrent users very well.\n";
    echo "   You can safely support:\n";
    echo "   • " . colorText("8-10 users:", 'green') . " Easy\n";
    echo "   • " . colorText("15-20 users:", 'green') . " Comfortable\n";
    echo "   • " . colorText("25+ users:", 'yellow') . " Test with higher concurrent users\n";
    echo "\n";
    echo "   Next step: Try testing with " . colorText("15 or 20 concurrent users", 'yellow') . "\n";
    echo "   Edit \$concurrentUsers = 15; and run again.\n";
} elseif ($avgAll < 1000) {
    echo colorText("✅ GOOD!", 'yellow') . " System is working well under load.\n";
    echo "   • Current performance is acceptable for 8 concurrent users\n";
    echo "   • Consider caching improvements for better response times\n";
} else {
    echo colorText("⚠️  NEEDS OPTIMIZATION", 'red') . "\n";
    echo "   • Response times are higher than expected\n";
    echo "   • Check: OPcache, database queries, server resources\n";
}

echo str_repeat("=", 80) . "\n";

// ============================================================================
// NEXT STEPS
// ============================================================================
echo "\n" . colorText("🚀 NEXT STEPS:", 'cyan') . "\n";
echo str_repeat("-", 80) . "\n";
echo "1. " . colorText("Test with more users:", 'yellow') . " Edit \$concurrentUsers (try 10, 15, 20)\n";
echo "2. " . colorText("Monitor server:", 'yellow') . " Watch CPU/RAM usage during test\n";
echo "3. " . colorText("Test authenticated pages:", 'yellow') . " Create authenticated version\n";
echo "4. " . colorText("Production ready?:", 'yellow') . " If avg < 500ms consistently, you're good!\n";
echo str_repeat("=", 80) . "\n";