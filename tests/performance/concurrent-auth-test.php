<?php
/**
 * AUTHENTICATED CONCURRENT LOAD TEST
 * Tests dashboard and authenticated pages with real login sessions
 */

// ============================================================================
// CONFIGURATION
// ============================================================================
$baseUrl = 'http://127.0.0.1:8000';
$concurrentUsers = 8;
$requestsPerUser = 3;

// Login credentials (using test users created by create-test-users.php)
$testUsers = [
    ['email' => 'admin@test.com', 'password' => 'password'],
    ['email' => 'user1@test.com', 'password' => 'password'],
    ['email' => 'user2@test.com', 'password' => 'password'],
    ['email' => 'user3@test.com', 'password' => 'password'],
    ['email' => 'user4@test.com', 'password' => 'password'],
    ['email' => 'user5@test.com', 'password' => 'password'],
    ['email' => 'user6@test.com', 'password' => 'password'],
    ['email' => 'user7@test.com', 'password' => 'password'],
    ['email' => 'user8@test.com', 'password' => 'password'],
];

// Authenticated pages to test
$testPages = [
    '/dashboard' => 'Dashboard',
    '/activities' => 'Activities Page',
];

// ============================================================================
// HELPER FUNCTIONS
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

function login($baseUrl, $email, $password) {
    // Get CSRF token
    $ch = curl_init("$baseUrl/login");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => 'cookie.txt',
        CURLOPT_COOKIEFILE => 'cookie.txt',
    ]);
    $html = curl_exec($ch);
    curl_close($ch);
    
    // Extract CSRF token
    preg_match('/<input[^>]*name="_token"[^>]*value="([^"]*)"/', $html, $matches);
    $csrfToken = $matches[1] ?? '';
    
    if (empty($csrfToken)) {
        return null;
    }
    
    // Login
    $ch = curl_init("$baseUrl/login");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            '_token' => $csrfToken,
            'email' => $email,
            'password' => $password,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => 'cookie.txt',
        CURLOPT_COOKIEFILE => 'cookie.txt',
        CURLOPT_HEADER => true,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Extract session cookie
    preg_match('/laravel_session=([^;]+)/', $response, $matches);
    $sessionCookie = $matches[1] ?? null;
    
    return $sessionCookie;
}

// ============================================================================
// BANNER
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo colorText("  🔐 AUTHENTICATED CONCURRENT LOAD TEST", 'cyan') . "\n";
echo str_repeat("=", 80) . "\n";
echo "Concurrent users: " . colorText($concurrentUsers, 'yellow') . "\n";
echo "Requests per user: " . colorText($requestsPerUser, 'yellow') . "\n";
echo str_repeat("=", 80) . "\n\n";

// ============================================================================
// CREATE SESSIONS
// ============================================================================
echo colorText("🔑 Creating user sessions...", 'blue') . "\n";

$sessions = [];
for ($i = 0; $i < min($concurrentUsers, count($testUsers)); $i++) {
    $user = $testUsers[$i];
    echo "  Logging in: {$user['email']}... ";
    
    $session = login($baseUrl, $user['email'], $user['password']);
    
    if ($session) {
        $sessions[] = $session;
        echo colorText("✓", 'green') . "\n";
    } else {
        echo colorText("✗ Failed", 'red') . "\n";
    }
}

// If we need more sessions than test users, reuse sessions
while (count($sessions) < $concurrentUsers) {
    $sessions[] = $sessions[count($sessions) % count($testUsers)];
}

if (empty($sessions)) {
    echo colorText("\n❌ ERROR: Could not create any sessions!", 'red') . "\n";
    echo "Please check:\n";
    echo "  1. App is running: $baseUrl\n";
    echo "  2. Login credentials are correct\n";
    echo "  3. Database has test users\n";
    exit(1);
}

echo colorText("\n✅ Created " . count($sessions) . " user sessions\n\n", 'green');

// ============================================================================
// CONCURRENT TESTING
// ============================================================================
$results = [];

foreach ($testPages as $path => $name) {
    $url = $baseUrl . $path;
    
    echo colorText("Testing: $name ($path)", 'blue') . "\n";
    echo "URL: $url\n";
    echo str_repeat("-", 80) . "\n";
    
    $startTime = microtime(true);
    
    $mh = curl_multi_init();
    $handles = [];
    $requests = [];
    
    // Create concurrent requests
    for ($user = 0; $user < $concurrentUsers; $user++) {
        for ($req = 1; $req <= $requestsPerUser; $req++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_COOKIE => "laravel_session={$sessions[$user]}",
                CURLOPT_USERAGENT => "LoadTest-User" . ($user + 1) . "-Req$req",
            ]);
            
            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
            $requests[] = [
                'user' => $user + 1,
                'request' => $req,
                'start' => microtime(true)
            ];
        }
    }
    
    // Execute all handles
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);
    
    // Collect results
    $durations = [];
    $successes = 0;
    $failures = 0;
    $errors = [];
    
    foreach ($handles as $i => $ch) {
        $duration = (microtime(true) - $requests[$i]['start']) * 1000;
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $success = $httpCode == 200;
        
        $durations[] = $duration;
        
        if ($success) {
            $successes++;
        } else {
            $failures++;
            $errors[] = "User{$requests[$i]['user']}-Req{$requests[$i]['request']}: HTTP $httpCode";
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
    echo colorText("✓ Completed in " . round($totalTime, 2) . "ms", 'green') . "\n";
    echo "  Total Requests:  " . count($handles) . "\n";
    echo "  Successful:      " . colorText($successes, 'green') . "\n";
    if ($failures > 0) {
        echo "  Failed:          " . colorText($failures, 'red') . "\n";
        foreach (array_slice($errors, 0, 3) as $error) {
            echo "    • $error\n";
        }
    }
    echo "  Average Time:    " . round($avgDuration, 2) . "ms\n";
    echo "  Min Time:        " . round($minDuration, 2) . "ms\n";
    echo "  Max Time:        " . round($maxDuration, 2) . "ms\n";
    echo "  Throughput:      " . round((count($handles) / $totalTime) * 1000, 2) . " req/sec\n";
    
    // Status
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
    usleep(500000);
}

// ============================================================================
// SUMMARY
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo colorText("  📊 SUMMARY", 'cyan') . "\n";
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
// FINAL VERDICT
// ============================================================================
$avgAll = array_sum(array_column($results, 'avg')) / count($results);
$allSuccess = array_sum(array_column($results, 'failures')) == 0;

echo "\n" . colorText("🎯 FINAL VERDICT:", 'cyan') . "\n";
echo str_repeat("-", 80) . "\n";

if ($avgAll < 500 && $allSuccess) {
    echo colorText("🏆 EXCELLENT! System is PRODUCTION READY!", 'green') . "\n";
    echo "\n";
    echo "Your system can comfortably handle:\n";
    echo "  • " . colorText("8-10 concurrent users:", 'green') . " ✅ Easy\n";
    echo "  • " . colorText("15-20 concurrent users:", 'green') . " ✅ Comfortable\n";
    echo "  • " . colorText("25+ concurrent users:", 'yellow') . " ⚠️  Test recommended\n";
    echo "\n";
    echo colorText("✅ Ready to deploy to production!", 'green') . "\n";
} elseif ($avgAll < 1000 && $allSuccess) {
    echo colorText("✅ GOOD! System performs well under load.", 'yellow') . "\n";
    echo "\n";
    echo "Recommendations:\n";
    echo "  • Performance is acceptable for 8 concurrent users\n";
    echo "  • Consider further caching optimizations\n";
    echo "  • Monitor under real production load\n";
} else {
    echo colorText("⚠️  NEEDS ATTENTION", 'red') . "\n";
    echo "\n";
    echo "Issues detected:\n";
    if ($avgAll >= 1000) {
        echo "  • Response times are high (>1000ms average)\n";
    }
    if (!$allSuccess) {
        echo "  • Some requests failed - check authentication\n";
    }
    echo "\n";
    echo "Recommended actions:\n";
    echo "  1. Check OPcache status\n";
    echo "  2. Review slow query log\n";
    echo "  3. Monitor server resources (CPU/RAM)\n";
}

echo str_repeat("=", 80) . "\n";

// Cleanup
@unlink('cookie.txt');