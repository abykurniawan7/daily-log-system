<?php

/**
 * AUTHENTICATED LOAD TEST
 * Test performa halaman dashboard & activities (yang sudah di-optimize)
 * 
 * Usage:
 * 1. Login ke aplikasi via browser dulu
 * 2. Ambil session cookie dari DevTools
 * 3. Update $sessionCookie di bawah
 * 4. Run: php load-test-authenticated.php
 */

// ========== CONFIGURATION ==========

$baseUrl = 'http://127.0.0.1:8000';

// ❗ IMPORTANT: Update session cookie setelah login
// Cara dapat cookie:
// 1. Login via browser
// 2. F12 > Application > Cookies > Copy value "laravel_session"
// 3. Paste di bawah ini
$sessionCookie = 'YOUR_SESSION_COOKIE_HERE';

// Test scenarios
$tests = [
    [
        'name' => 'Dashboard (Optimized)',
        'url' => '/dashboard',
        'concurrency' => 5,
        'requests' => 50,
        'target_time' => 500, // Target: <500ms
    ],
    [
        'name' => 'Dashboard (Normal Load)',
        'url' => '/dashboard',
        'concurrency' => 10,
        'requests' => 100,
        'target_time' => 1000, // Target: <1000ms
    ],
    [
        'name' => 'Activities Index',
        'url' => '/activities',
        'concurrency' => 5,
        'requests' => 50,
        'target_time' => 400, // Target: <400ms
    ],
    [
        'name' => 'My Activities',
        'url' => '/activities/my-activities',
        'concurrency' => 5,
        'requests' => 50,
        'target_time' => 400, // Target: <400ms
    ],
    [
        'name' => 'Peak Load Test (20 users)',
        'url' => '/dashboard',
        'concurrency' => 20,
        'requests' => 200,
        'target_time' => 2000, // Target: <2000ms
    ],
];

// ========== FUNCTIONS ==========

function printHeader($text, $width = 80) {
    echo str_repeat('═', $width) . "\n";
    echo "  " . $text . "\n";
    echo str_repeat('═', $width) . "\n";
}

function printBox($lines, $width = 60) {
    echo "┌" . str_repeat('─', $width - 2) . "┐\n";
    foreach ($lines as $line) {
        printf("│ %-" . ($width - 4) . "s │\n", $line);
    }
    echo "└" . str_repeat('─', $width - 2) . "┘\n";
}

function findApacheBench() {
    $possiblePaths = [
        'C:\\xampp\\apache\\bin\\ab.exe',
        'C:\\laragon\\bin\\apache\\httpd-2.4.54-win64-VS16\\bin\\ab.exe',
        'C:\\laragon\\bin\\apache\\apache-2.4.54-win64-VS16\\bin\\ab.exe',
        '/usr/bin/ab',
        '/usr/local/bin/ab',
        'ab',
    ];
    
    foreach ($possiblePaths as $path) {
        if (@file_exists($path) || @exec("where $path 2>nul", $output, $code) === 0 || $code === 0) {
            return $path;
        }
    }
    
    return null;
}

function runLoadTest($abPath, $url, $concurrency, $requests, $sessionCookie) {
    // Create cookie file
    $cookieFile = tempnam(sys_get_temp_dir(), 'cookie_');
    file_put_contents($cookieFile, "laravel_session={$sessionCookie}");
    
    $command = sprintf(
        '"%s" -n %d -c %d -C "laravel_session=%s" "%s" 2>&1',
        $abPath,
        $requests,
        $concurrency,
        $sessionCookie,
        $url
    );
    
    $output = shell_exec($command);
    
    // Parse results
    $results = [
        'requests_per_sec' => 0,
        'avg_time' => 0,
        'failed_requests' => 0,
    ];
    
    if (preg_match('/Requests per second:\s+([\d.]+)/', $output, $matches)) {
        $results['requests_per_sec'] = floatval($matches[1]);
    }
    
    if (preg_match('/Time per request:\s+([\d.]+).*\(mean\)/', $output, $matches)) {
        $results['avg_time'] = floatval($matches[1]);
    }
    
    if (preg_match('/Failed requests:\s+(\d+)/', $output, $matches)) {
        $results['failed_requests'] = intval($matches[1]);
    }
    
    // Cleanup
    @unlink($cookieFile);
    
    return $results;
}

function getStatusEmoji($avgTime, $targetTime, $failedRequests) {
    if ($failedRequests > 0) {
        return '❌';
    }
    
    if ($avgTime <= $targetTime) {
        return '✅';
    } elseif ($avgTime <= $targetTime * 1.5) {
        return '⚠️';
    } else {
        return '❌';
    }
}

function getStatusText($avgTime, $targetTime, $failedRequests) {
    if ($failedRequests > 0) {
        return 'FAILED';
    }
    
    if ($avgTime <= $targetTime) {
        return 'EXCELLENT';
    } elseif ($avgTime <= $targetTime * 1.5) {
        return 'GOOD';
    } else {
        return 'NEEDS IMPROVEMENT';
    }
}

// ========== MAIN EXECUTION ==========

system('cls'); // Clear screen on Windows

printHeader('🚀 AUTHENTICATED LOAD TEST - OPTIMIZED PAGES');

echo "\n";

// Check Apache Bench
$abPath = findApacheBench();
if (!$abPath) {
    echo "❌ Apache Bench (ab) not found!\n";
    echo "   Please install Apache or XAMPP\n";
    exit(1);
}

echo "✅ Apache Bench found: {$abPath}\n";

// Check session cookie
if ($sessionCookie === 'YOUR_SESSION_COOKIE_HERE') {
    echo "\n";
    echo "❌ ERROR: Session cookie not configured!\n";
    echo "\n";
    echo "📋 HOW TO GET SESSION COOKIE:\n";
    echo "   1. Open your app in browser: {$baseUrl}\n";
    echo "   2. Login with your credentials\n";
    echo "   3. Press F12 > Application tab > Cookies\n";
    echo "   4. Copy value of 'laravel_session'\n";
    echo "   5. Update \$sessionCookie in this script\n";
    echo "   6. Run again: php load-test-authenticated.php\n";
    echo "\n";
    exit(1);
}

echo "✅ Session cookie configured\n";
echo "\n";

$allResults = [];

// Run tests
foreach ($tests as $test) {
    echo str_repeat('━', 80) . "\n";
    echo "📊 TEST: {$test['name']}\n";
    echo str_repeat('━', 80) . "\n";
    
    $fullUrl = $baseUrl . $test['url'];
    
    echo "🎯 Target: {$fullUrl}\n";
    echo "👥 Concurrent Users: {$test['concurrency']}\n";
    echo "📤 Total Requests: {$test['requests']}\n";
    echo "🎯 Target Time: {$test['target_time']}ms\n";
    echo "⏳ Running test...\n\n";
    
    $results = runLoadTest($abPath, $fullUrl, $test['concurrency'], $test['requests'], $sessionCookie);
    
    $emoji = getStatusEmoji($results['avg_time'], $test['target_time'], $results['failed_requests']);
    $status = getStatusText($results['avg_time'], $test['target_time'], $results['failed_requests']);
    
    printBox([
        sprintf("Requests per Second: %.2f req/s", $results['requests_per_sec']),
        sprintf("Average Response Time: %s ms", number_format($results['avg_time'], 0)),
        sprintf("Target Time: %s ms", number_format($test['target_time'], 0)),
        sprintf("Failed Requests: %d", $results['failed_requests']),
        "",
        sprintf("Status: %s %s", $emoji, $status),
    ]);
    
    // Performance comparison
    $improvement = (($test['target_time'] - $results['avg_time']) / $test['target_time']) * 100;
    
    if ($improvement > 0) {
        echo sprintf("   🎉 %.0f%% FASTER than target!\n", $improvement);
    } elseif ($improvement > -20) {
        echo sprintf("   ⚠️  %.0f%% slower than target (acceptable)\n", abs($improvement));
    } else {
        echo sprintf("   ❌ %.0f%% slower than target (needs optimization)\n", abs($improvement));
    }
    
    echo "\n";
    
    $allResults[] = [
        'name' => $test['name'],
        'concurrency' => $test['concurrency'],
        'results' => $results,
        'target_time' => $test['target_time'],
        'status' => $status,
        'emoji' => $emoji,
    ];
    
    sleep(2); // Cooldown between tests
}

// Summary Report
echo "\n";
printHeader('📈 SUMMARY REPORT');
echo "\n";

foreach ($allResults as $result) {
    printf(
        "%-40s %s %s\n",
        $result['name'] . " ({$result['concurrency']} users)",
        $result['emoji'],
        $result['status']
    );
    printf(
        "  • Requests/sec: %.2f | Avg Time: %d ms | Failed: %d | Target: %d ms\n\n",
        $result['results']['requests_per_sec'],
        $result['results']['avg_time'],
        $result['results']['failed_requests'],
        $result['target_time']
    );
}

// Overall Assessment
echo str_repeat('━', 80) . "\n";
echo "🎯 OVERALL ASSESSMENT FOR 8 CONCURRENT USERS:\n";
echo str_repeat('━', 80) . "\n";

$testFor8Users = array_filter($allResults, function($r) {
    return $r['concurrency'] == 10;
});

if (!empty($testFor8Users)) {
    $test8 = array_values($testFor8Users)[0];
    $avgTime = $test8['results']['avg_time'];
    
    if ($avgTime < 1000 && $test8['results']['failed_requests'] == 0) {
        echo "✅ EXCELLENT! Your system can easily handle 8 concurrent users.\n";
        echo "   Average response time: " . number_format($avgTime, 0) . "ms (Target: <1000ms)\n";
        echo "   Recommended Max: 15-20 concurrent users\n";
    } elseif ($avgTime < 2000 && $test8['results']['failed_requests'] == 0) {
        echo "✅ GOOD! Your system can handle 8 concurrent users comfortably.\n";
        echo "   Average response time: " . number_format($avgTime, 0) . "ms\n";
        echo "   Recommended Max: 10-12 concurrent users\n";
    } else {
        echo "⚠️  Your system can handle 8 users but with some slowness.\n";
        echo "   Average response time: " . number_format($avgTime, 0) . "ms\n";
        echo "   Consider further optimization for better UX.\n";
    }
} else {
    echo "⚠️  10-user test not found in results.\n";
}

echo "\n";
echo "📊 Load test completed!\n";
echo "   Report generated: " . date('Y-m-d H:i:s') . "\n";
echo "\n";