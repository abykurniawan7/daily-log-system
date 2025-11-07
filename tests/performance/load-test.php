<?php

/**
 * LOAD TEST SCRIPT FOR WORKLOG BPD BALI
 * 
 * Usage:
 * 1. Copy file ini ke root project Laravel
 * 2. Run: php load-test.php
 * 3. Lihat hasil test di terminal
 * 
 * Requirements:
 * - PHP CLI
 * - Laravel app running (php artisan serve)
 */

echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║        LOAD TEST - WORKLOG BPD BALI                      ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

// Configuration
$baseUrl = 'http://127.0.0.1:8000';
$testScenarios = [
    [
        'name' => 'Light Load (5 concurrent users)',
        'concurrent' => 5,
        'requests' => 50,
    ],
    [
        'name' => 'Normal Load (10 concurrent users)',
        'concurrent' => 10,
        'requests' => 100,
    ],
    [
        'name' => 'Peak Load (20 concurrent users)',
        'concurrent' => 20,
        'requests' => 200,
    ],
];

// Test endpoints
$endpoints = [
    '/login',
    '/',
    '/dashboard',
];

// Check if Apache Bench is available
$abPath = findApacheBench();

if (!$abPath) {
    echo "❌ ERROR: Apache Bench (ab) not found!\n";
    echo "   Please install Apache or use Laragon (includes ab.exe)\n\n";
    exit(1);
}

echo "✅ Apache Bench found: $abPath\n\n";

// Run tests
$results = [];

foreach ($testScenarios as $scenario) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 TEST: {$scenario['name']}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $endpoint = $baseUrl . $endpoints[0]; // Test login page (public)
    
    echo "🎯 Target: $endpoint\n";
    echo "👥 Concurrent Users: {$scenario['concurrent']}\n";
    echo "📤 Total Requests: {$scenario['requests']}\n\n";
    
    // Run Apache Bench
    $command = sprintf(
        '"%s" -n %d -c %d -q %s',
        $abPath,
        $scenario['requests'],
        $scenario['concurrent'],
        $endpoint
    );
    
    echo "⏳ Running test...\n\n";
    
    $output = [];
    exec($command, $output, $returnCode);
    
    if ($returnCode !== 0) {
        echo "❌ Test failed!\n\n";
        continue;
    }
    
    // Parse results
    $result = parseApacheBenchOutput($output);
    $results[$scenario['name']] = $result;
    
    // Display results
    displayResults($result, $scenario);
    
    echo "\n";
    sleep(2); // Pause between tests
}

// Summary
echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║                    SUMMARY REPORT                         ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

foreach ($results as $scenarioName => $result) {
    $status = getStatus($result);
    echo sprintf(
        "%-40s %s\n",
        $scenarioName,
        $status
    );
    echo sprintf(
        "  • Requests/sec: %.2f | Avg Time: %.0f ms | Failed: %d\n\n",
        $result['requests_per_second'] ?? 0,
        $result['time_per_request'] ?? 0,
        $result['failed_requests'] ?? 0
    );
}

// Recommendation
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💡 RECOMMENDATION FOR 8 USERS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$bestScenario = getBestScenario($results);

if ($bestScenario['concurrent'] >= 10) {
    echo "✅ Your website can handle 10+ concurrent users!\n";
    echo "   This is MORE than enough for 8 users.\n\n";
    echo "   Recommended Max Concurrent Users: 20 users\n";
} elseif ($bestScenario['concurrent'] >= 5) {
    echo "⚠️  Your website can handle 5-10 concurrent users.\n";
    echo "   This is sufficient for 8 users, but with limited buffer.\n\n";
    echo "   Recommended Max Concurrent Users: 10 users\n";
} else {
    echo "❌ Your website struggles with 5+ concurrent users.\n";
    echo "   Consider optimization (caching, database indexes).\n\n";
    echo "   Current Max Concurrent Users: < 5 users\n";
}

echo "\n";

// ========== HELPER FUNCTIONS ==========

function findApacheBench() {
    $paths = [
        'C:\laragon\bin\apache\apache-2.4.54-win64-VS16\bin\ab.exe', // Laragon
        'C:\xampp\apache\bin\ab.exe', // XAMPP
        '/usr/bin/ab', // Linux
        '/usr/local/bin/ab', // macOS
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    // Try system path
    exec('where ab', $output, $returnCode);
    if ($returnCode === 0 && !empty($output)) {
        return trim($output[0]);
    }
    
    return null;
}

function parseApacheBenchOutput($output) {
    $result = [];
    
    foreach ($output as $line) {
        if (preg_match('/Requests per second:\s+([\d.]+)/', $line, $matches)) {
            $result['requests_per_second'] = (float)$matches[1];
        }
        if (preg_match('/Time per request:\s+([\d.]+).*\(mean\)/', $line, $matches)) {
            $result['time_per_request'] = (float)$matches[1];
        }
        if (preg_match('/Failed requests:\s+(\d+)/', $line, $matches)) {
            $result['failed_requests'] = (int)$matches[1];
        }
        if (preg_match('/Complete requests:\s+(\d+)/', $line, $matches)) {
            $result['complete_requests'] = (int)$matches[1];
        }
    }
    
    return $result;
}

function displayResults($result, $scenario) {
    $rps = $result['requests_per_second'] ?? 0;
    $avgTime = $result['time_per_request'] ?? 0;
    $failed = $result['failed_requests'] ?? 0;
    
    echo "┌─────────────────────────────────────────────────────────┐\n";
    echo sprintf("│ Requests per Second: %-30s │\n", number_format($rps, 2) . ' req/s');
    echo sprintf("│ Average Response Time: %-26s │\n", number_format($avgTime, 0) . ' ms');
    echo sprintf("│ Failed Requests: %-34s │\n", $failed);
    echo "└─────────────────────────────────────────────────────────┘\n";
    
    // Status
    if ($avgTime < 500 && $failed === 0) {
        echo "   Status: ✅ EXCELLENT (Fast & Stable)\n";
    } elseif ($avgTime < 1000 && $failed < 5) {
        echo "   Status: ✅ GOOD (Acceptable Performance)\n";
    } elseif ($avgTime < 2000) {
        echo "   Status: ⚠️  MODERATE (Slow Response)\n";
    } else {
        echo "   Status: ❌ POOR (Too Slow or Many Failures)\n";
    }
}

function getStatus($result) {
    $avgTime = $result['time_per_request'] ?? 9999;
    $failed = $result['failed_requests'] ?? 999;
    
    if ($avgTime < 500 && $failed === 0) {
        return "✅ EXCELLENT";
    } elseif ($avgTime < 1000 && $failed < 5) {
        return "✅ GOOD";
    } elseif ($avgTime < 2000) {
        return "⚠️  MODERATE";
    } else {
        return "❌ POOR";
    }
}

function getBestScenario($results) {
    $best = [
        'concurrent' => 0,
        'requests_per_second' => 0,
    ];
    
    foreach ($results as $name => $result) {
        $avgTime = $result['time_per_request'] ?? 9999;
        $failed = $result['failed_requests'] ?? 999;
        
        // Consider "successful" if avg time < 2s and no failures
        if ($avgTime < 2000 && $failed < 10) {
            // Extract concurrent users from scenario name
            if (preg_match('/(\d+)\s+concurrent/', $name, $matches)) {
                $concurrent = (int)$matches[1];
                if ($concurrent > $best['concurrent']) {
                    $best['concurrent'] = $concurrent;
                    $best['requests_per_second'] = $result['requests_per_second'] ?? 0;
                }
            }
        }
    }
    
    return $best;
}