<?php
/**
 * SERVER RESOURCE MONITOR
 * Monitor CPU, RAM, and PHP performance during load tests
 * Cross-platform: Windows & Linux
 */

// ============================================================================
// CONFIGURATION
// ============================================================================
$refreshInterval = 2; // seconds
$maxSamples = 60;

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

function isWindows() {
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

function getMemoryUsageWindows() {
    $output = shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value');
    
    preg_match('/FreePhysicalMemory=(\d+)/', $output, $free);
    preg_match('/TotalVisibleMemorySize=(\d+)/', $output, $total);
    
    if (empty($total) || empty($free)) {
        return null;
    }
    
    $totalMB = round($total[1] / 1024);
    $freeMB = round($free[1] / 1024);
    $usedMB = $totalMB - $freeMB;
    $usedPercent = round(($usedMB / $totalMB) * 100, 1);
    
    return [
        'total' => $totalMB,
        'used' => $usedMB,
        'free' => $freeMB,
        'percent' => $usedPercent
    ];
}

function getMemoryUsageLinux() {
    if (!file_exists('/proc/meminfo')) {
        return null;
    }
    
    $memInfo = file_get_contents('/proc/meminfo');
    preg_match('/MemTotal:\s+(\d+)/', $memInfo, $total);
    preg_match('/MemAvailable:\s+(\d+)/', $memInfo, $available);
    
    if (empty($total) || empty($available)) {
        return null;
    }
    
    $totalMB = round($total[1] / 1024);
    $availableMB = round($available[1] / 1024);
    $usedMB = $totalMB - $availableMB;
    $usedPercent = round(($usedMB / $totalMB) * 100, 1);
    
    return [
        'total' => $totalMB,
        'used' => $usedMB,
        'free' => $availableMB,
        'percent' => $usedPercent
    ];
}

function getMemoryUsage() {
    return isWindows() ? getMemoryUsageWindows() : getMemoryUsageLinux();
}

function getCpuUsageWindows() {
    static $lastCheck = 0;
    static $lastCpu = 0;
    
    $now = microtime(true);
    if ($now - $lastCheck < 1) {
        return $lastCpu;
    }
    
    $output = shell_exec('wmic cpu get loadpercentage /value');
    preg_match('/LoadPercentage=(\d+)/', $output, $matches);
    
    $lastCheck = $now;
    $lastCpu = isset($matches[1]) ? (int)$matches[1] : 0;
    
    return $lastCpu;
}

function getCpuUsageLinux() {
    static $lastStats = null;
    
    if (!file_exists('/proc/stat')) {
        return null;
    }
    
    $stats = file_get_contents('/proc/stat');
    preg_match('/cpu\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $stats, $matches);
    
    if (empty($matches)) {
        return null;
    }
    
    $currentStats = [
        'user' => $matches[1],
        'nice' => $matches[2],
        'system' => $matches[3],
        'idle' => $matches[4]
    ];
    
    if ($lastStats === null) {
        $lastStats = $currentStats;
        return 0;
    }
    
    $totalDiff = array_sum($currentStats) - array_sum($lastStats);
    $idleDiff = $currentStats['idle'] - $lastStats['idle'];
    
    $cpuUsage = $totalDiff > 0 ? round((($totalDiff - $idleDiff) / $totalDiff) * 100, 1) : 0;
    
    $lastStats = $currentStats;
    
    return $cpuUsage;
}

function getCpuUsage() {
    return isWindows() ? getCpuUsageWindows() : getCpuUsageLinux();
}

function getPhpInfo() {
    return [
        'version' => PHP_VERSION,
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false,
        'current_memory' => round(memory_get_usage(true) / 1024 / 1024, 2),
    ];
}

function getOpcacheInfo() {
    if (!function_exists('opcache_get_status')) {
        return null;
    }
    
    $status = @opcache_get_status();
    if (!$status) {
        return null;
    }
    
    return [
        'enabled' => $status['opcache_enabled'],
        'memory_usage' => $status['memory_usage'],
        'stats' => $status['opcache_statistics'],
    ];
}

function formatBytes($bytes) {
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}

function getProgressBar($percent, $width = 30) {
    $filled = round(($percent / 100) * $width);
    $empty = $width - $filled;
    
    $color = $percent < 60 ? 'green' : ($percent < 80 ? 'yellow' : 'red');
    
    return colorText(str_repeat('█', $filled), $color) . 
           str_repeat('░', $empty) . 
           sprintf(' %5.1f%%', $percent);
}

function clearScreen() {
    if (isWindows()) {
        system('cls');
    } else {
        echo "\033[2J\033[H";
    }
}

// ============================================================================
// MAIN MONITORING LOOP
// ============================================================================
$samples = [];
$startTime = time();

echo colorText("🖥️  SERVER RESOURCE MONITOR", 'cyan') . "\n";
echo colorText("Initializing... Please wait...\n\n", 'yellow');
sleep(2);

// Initial CPU reading
getCpuUsage();
sleep(1);

while (true) {
    clearScreen();
    
    $now = time();
    $runtime = $now - $startTime;
    
    // ========================================================================
    // HEADER
    // ========================================================================
    echo str_repeat("=", 80) . "\n";
    echo colorText("  🖥️  SERVER RESOURCE MONITOR", 'cyan') . " - " . (isWindows() ? 'Windows' : 'Linux') . "\n";
    echo str_repeat("=", 80) . "\n";
    echo "Runtime: " . gmdate('H:i:s', $runtime) . " | ";
    echo "Refresh: {$refreshInterval}s | ";
    echo date('H:i:s') . "\n";
    echo str_repeat("=", 80) . "\n\n";
    
    // ========================================================================
    // SYSTEM RESOURCES
    // ========================================================================
    echo colorText("📊 SYSTEM RESOURCES", 'blue') . "\n";
    echo str_repeat("-", 80) . "\n";
    
    // CPU
    $cpu = getCpuUsage();
    if ($cpu !== null) {
        echo "CPU Usage:    " . getProgressBar($cpu) . "\n";
        $samples['cpu'][] = $cpu;
    } else {
        echo "CPU Usage:    " . colorText("N/A", 'yellow') . "\n";
    }
    
    // Memory
    $memory = getMemoryUsage();
    if ($memory !== null) {
        echo "Memory:       " . getProgressBar($memory['percent']) . 
             " ({$memory['used']} MB / {$memory['total']} MB)\n";
        $samples['memory'][] = $memory['percent'];
    } else {
        echo "Memory:       " . colorText("N/A", 'yellow') . "\n";
    }
    
    echo "\n";
    
    // ========================================================================
    // PHP INFORMATION
    // ========================================================================
    echo colorText("🐘 PHP CONFIGURATION", 'blue') . "\n";
    echo str_repeat("-", 80) . "\n";
    
    $phpInfo = getPhpInfo();
    
    echo "PHP Version:      " . colorText($phpInfo['version'], 'yellow') . "\n";
    echo "Memory Limit:     " . colorText($phpInfo['memory_limit'], 'yellow') . "\n";
    echo "Current Memory:   " . colorText($phpInfo['current_memory'] . ' MB', 'yellow') . "\n";
    echo "Max Exec Time:    " . colorText($phpInfo['max_execution_time'] . 's', 'yellow') . "\n";
    echo "OPcache:          " . ($phpInfo['opcache_enabled'] ? 
        colorText('✅ ENABLED', 'green') : 
        colorText('❌ DISABLED', 'red')) . "\n";
    
    echo "\n";
    
    // ========================================================================
    // OPCACHE STATISTICS
    // ========================================================================
    $opcache = getOpcacheInfo();
    if ($opcache && $opcache['enabled']) {
        echo colorText("⚡ OPCACHE STATUS", 'blue') . "\n";
        echo str_repeat("-", 80) . "\n";
        
        $memUsed = $opcache['memory_usage']['used_memory'];
        $memFree = $opcache['memory_usage']['free_memory'];
        $memTotal = $memUsed + $memFree;
        $memPercent = round(($memUsed / $memTotal) * 100, 1);
        
        echo "Memory Usage:     " . getProgressBar($memPercent) . 
             " (" . formatBytes($memUsed) . " / " . formatBytes($memTotal) . ")\n";
        
        echo "Cached Scripts:   " . colorText(number_format($opcache['stats']['num_cached_scripts']), 'yellow') . "\n";
        echo "Cache Hits:       " . colorText(number_format($opcache['stats']['hits']), 'green') . "\n";
        echo "Cache Misses:     " . colorText(number_format($opcache['stats']['misses']), 'yellow') . "\n";
        
        if ($opcache['stats']['hits'] + $opcache['stats']['misses'] > 0) {
            $hitRate = round(($opcache['stats']['hits'] / ($opcache['stats']['hits'] + $opcache['stats']['misses'])) * 100, 2);
            echo "Hit Rate:         " . getProgressBar($hitRate) . "\n";
        }
        
        echo "\n";
    }
    
    // ========================================================================
    // STATISTICS
    // ========================================================================
    if (!empty($samples['cpu']) || !empty($samples['memory'])) {
        echo colorText("📈 STATISTICS (Last " . min(count($samples['cpu'] ?? []), $maxSamples) . " samples)", 'blue') . "\n";
        echo str_repeat("-", 80) . "\n";
        
        if (!empty($samples['cpu'])) {
            $recentCpu = array_slice($samples['cpu'], -$maxSamples);
            $avgCpu = round(array_sum($recentCpu) / count($recentCpu), 1);
            $maxCpu = max($recentCpu);
            $minCpu = min($recentCpu);
            
            echo sprintf("CPU:    Avg: %5.1f%% | Min: %5.1f%% | Max: %5.1f%%", 
                $avgCpu, $minCpu, $maxCpu);
            
            if ($avgCpu < 60) {
                echo " " . colorText("✅ Good", 'green');
            } elseif ($avgCpu < 80) {
                echo " " . colorText("⚠️  High", 'yellow');
            } else {
                echo " " . colorText("❌ Critical", 'red');
            }
            echo "\n";
        }
        
        if (!empty($samples['memory'])) {
            $recentMem = array_slice($samples['memory'], -$maxSamples);
            $avgMem = round(array_sum($recentMem) / count($recentMem), 1);
            $maxMem = max($recentMem);
            $minMem = min($recentMem);
            
            echo sprintf("Memory: Avg: %5.1f%% | Min: %5.1f%% | Max: %5.1f%%", 
                $avgMem, $minMem, $maxMem);
            
            if ($avgMem < 70) {
                echo " " . colorText("✅ Good", 'green');
            } elseif ($avgMem < 85) {
                echo " " . colorText("⚠️  High", 'yellow');
            } else {
                echo " " . colorText("❌ Critical", 'red');
            }
            echo "\n";
        }
        
        echo "\n";
    }
    
    // ========================================================================
    // ALERTS
    // ========================================================================
    $alerts = [];
    
    if ($cpu !== null && $cpu > 80) {
        $alerts[] = "⚠️  CPU usage is HIGH ({$cpu}%)";
    }
    if ($memory !== null && $memory['percent'] > 85) {
        $alerts[] = "⚠️  Memory usage is HIGH ({$memory['percent']}%)";
    }
    if (!$phpInfo['opcache_enabled']) {
        $alerts[] = "❌ OPcache is DISABLED - enable for better performance";
    }
    
    if (!empty($alerts)) {
        echo colorText("🚨 ALERTS", 'red') . "\n";
        echo str_repeat("-", 80) . "\n";
        foreach ($alerts as $alert) {
            echo $alert . "\n";
        }
        echo "\n";
    }
    
    // ========================================================================
    // TIPS
    // ========================================================================
    echo colorText("💡 MONITORING TIPS", 'cyan') . "\n";
    echo str_repeat("-", 80) . "\n";
    echo "• Run load tests in another terminal\n";
    echo "• Watch for CPU/Memory spikes during concurrent requests\n";
    echo "• Normal: CPU < 70%, Memory < 80%\n";
    echo "• High load: CPU 70-90%, Memory 80-90%\n";
    echo "• Critical: CPU > 90%, Memory > 90%\n";
    
    echo str_repeat("=", 80) . "\n";
    echo colorText("Press Ctrl+C to stop", 'yellow') . "\n";
    
    // Trim samples
    if (isset($samples['cpu']) && count($samples['cpu']) > $maxSamples) {
        $samples['cpu'] = array_slice($samples['cpu'], -$maxSamples);
    }
    if (isset($samples['memory']) && count($samples['memory']) > $maxSamples) {
        $samples['memory'] = array_slice($samples['memory'], -$maxSamples);
    }
    
    sleep($refreshInterval);
}