<?php
/**
 * QUICK BEFORE/AFTER TEST
 * Shows immediate impact of server configuration changes
 */

$baseUrl = 'http://127.0.0.1:8000';

function colorText($text, $color) {
    $colors = [
        'green' => "\033[0;32m",
        'yellow' => "\033[1;33m",
        'red' => "\033[0;31m",
        'cyan' => "\033[0;36m",
        'reset' => "\033[0m"
    ];
    return $colors[$color] . $text . $colors['reset'];
}

echo str_repeat("=", 80) . "\n";
echo colorText("  ⚡ QUICK PERFORMANCE TEST - Before/After Comparison", 'cyan') . "\n";
echo str_repeat("=", 80) . "\n\n";

// ============================================================================
// TEST 1: Single Request (Baseline)
// ============================================================================
echo colorText("TEST 1: Single Request (Baseline)", 'yellow') . "\n";
echo str_repeat("-", 80) . "\n";

$start = microtime(true);
$ch = curl_init($baseUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 10,
]);
curl_exec($ch);
curl_close($ch);
$singleTime = (microtime(true) - $start) * 1000;

echo "Single request: " . round($singleTime, 2) . "ms ";
if ($singleTime < 500) {
    echo colorText("✅ GOOD\n", 'green');
} else {
    echo colorText("⚠️  SLOW\n", 'yellow');
}
echo "\n";

// ============================================================================
// TEST 2: 5 Concurrent Requests
// ============================================================================
echo colorText("TEST 2: 5 Concurrent Requests", 'yellow') . "\n";
echo str_repeat("-", 80) . "\n";

$start = microtime(true);
$mh = curl_multi_init();
$handles = [];

for ($i = 0; $i < 5; $i++) {
    $ch = curl_init($baseUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    curl_multi_add_handle($mh, $ch);
    $handles[] = $ch;
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

foreach ($handles as $ch) {
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}
curl_multi_close($mh);

$concurrentTime = (microtime(true) - $start) * 1000;
$slowdownFactor = round($concurrentTime / $singleTime, 1);

echo "5 concurrent requests (wall clock): " . round($concurrentTime, 2) . "ms ";
if ($concurrentTime < 1000) {
    echo colorText("✅ EXCELLENT\n", 'green');
} elseif ($concurrentTime < 2000) {
    echo colorText("✅ GOOD\n", 'yellow');
} else {
    echo colorText("❌ SLOW (queuing detected)\n", 'red');
}

echo "Slowdown factor: " . $slowdownFactor . "x ";
if ($slowdownFactor < 2) {
    echo colorText("✅ Parallel processing\n", 'green');
} elseif ($slowdownFactor < 5) {
    echo colorText("⚠️  Some queuing\n", 'yellow');
} else {
    echo colorText("❌ Sequential processing\n", 'red');
}

echo "\n";

// ============================================================================
// VERDICT
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo colorText("  🎯 VERDICT", 'cyan') . "\n";
echo str_repeat("=", 80) . "\n\n";

if ($concurrentTime < 1000 && $slowdownFactor < 2) {
    echo colorText("✅ EXCELLENT! Server is handling concurrent requests properly.\n\n", 'green');
    echo "Your system:\n";
    echo "  • Processes requests in parallel ✅\n";
    echo "  • No significant queuing ✅\n";
    echo "  • Ready for production testing ✅\n\n";
    echo "Next steps:\n";
    echo "  1. Run: " . colorText("php concurrent-load-test.php", 'yellow') . "\n";
    echo "  2. Test with 8-20 concurrent users\n";
    echo "  3. Deploy if all tests pass! 🚀\n";
    
} elseif ($concurrentTime >= 1000 && $concurrentTime < 3000) {
    echo colorText("⚠️  MODERATE. Some performance issues detected.\n\n", 'yellow');
    echo "Observations:\n";
    echo "  • Concurrent processing works but is slower than expected\n";
    echo "  • Some queuing or resource contention\n\n";
    echo "Recommendations:\n";
    echo "  1. Check server resources (CPU/RAM)\n";
    echo "  2. Verify OPcache is enabled\n";
    echo "  3. Review database connection pool\n";
    
} else {
    echo colorText("❌ PROBLEM DETECTED: Requests are queuing!\n\n", 'red');
    echo "What's happening:\n";
    echo "  • Concurrent requests are NOT running in parallel\n";
    echo "  • They're waiting in queue (sequential processing)\n";
    echo "  • This will cause poor performance with multiple users\n\n";
    
    echo colorText("🔧 SOLUTION:\n\n", 'yellow');
    echo "Option 1: " . colorText("Use PHP built-in server (EASIEST)", 'green') . "\n";
    echo "  1. Stop Laragon\n";
    echo "  2. Run: php artisan serve\n";
    echo "  3. Test again\n";
    echo "  4. Expected: Concurrent ~= Single request time\n\n";
    
    echo "Option 2: " . colorText("Fix Apache configuration", 'yellow') . "\n";
    echo "  1. Read: fix-laragon-performance.md\n";
    echo "  2. Increase MaxRequestWorkers to 150\n";
    echo "  3. Restart Laragon\n";
    echo "  4. Test again\n\n";
    
    echo "Option 3: " . colorText("Run full diagnostic", 'yellow') . "\n";
    echo "  Run: php diagnose-slow-performance.php\n";
    echo "  This will show detailed analysis and specific fixes\n";
}

echo "\n" . str_repeat("=", 80) . "\n";

// ============================================================================
// COMPARISON TABLE
// ============================================================================
echo "\n" . colorText("📊 PERFORMANCE COMPARISON", 'cyan') . "\n";
echo str_repeat("=", 80) . "\n";

printf("%-25s | %-15s | %-15s | %s\n", 
    "Metric", "Your Result", "Target", "Status");
echo str_repeat("-", 80) . "\n";

// Single request
printf("%-25s | %12.0fms | %12s | %s\n", 
    "Single Request",
    $singleTime,
    "< 500ms",
    $singleTime < 500 ? colorText('✅', 'green') : colorText('❌', 'red')
);

// Concurrent
printf("%-25s | %12.0fms | %12s | %s\n", 
    "5 Concurrent (wall clock)",
    $concurrentTime,
    "< 1000ms",
    $concurrentTime < 1000 ? colorText('✅', 'green') : colorText('❌', 'red')
);

// Slowdown factor
printf("%-25s | %13.1fx | %12s | %s\n", 
    "Slowdown Factor",
    $slowdownFactor,
    "< 2x",
    $slowdownFactor < 2 ? colorText('✅', 'green') : colorText('❌', 'red')
);

echo str_repeat("=", 80) . "\n\n";

// ============================================================================
// EXPECTED RESULTS
// ============================================================================
echo colorText("💡 WHAT YOU SHOULD SEE (After Fix):", 'cyan') . "\n";
echo str_repeat("-", 80) . "\n";
echo "Single Request:           250-400ms\n";
echo "5 Concurrent (wall):      300-600ms\n";
echo "Slowdown Factor:          1.2-1.8x\n";
echo "\n";
echo "This means requests run in PARALLEL, not in QUEUE.\n";
echo str_repeat("=", 80) . "\n";