<?php
/**
 * CREATE TEST USERS
 * Creates test users for concurrent authentication testing
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "================================================================================\n";
echo "  🔑 CREATING TEST USERS\n";
echo "================================================================================\n\n";

$testUsers = [
    ['name' => 'Admin Test', 'email' => 'admin@test.com'],
    ['name' => 'User 1', 'email' => 'user1@test.com'],
    ['name' => 'User 2', 'email' => 'user2@test.com'],
    ['name' => 'User 3', 'email' => 'user3@test.com'],
    ['name' => 'User 4', 'email' => 'user4@test.com'],
    ['name' => 'User 5', 'email' => 'user5@test.com'],
    ['name' => 'User 6', 'email' => 'user6@test.com'],
    ['name' => 'User 7', 'email' => 'user7@test.com'],
    ['name' => 'User 8', 'email' => 'user8@test.com'],
];

$password = 'password'; // Default test password
$hashedPassword = Hash::make($password);

echo "Creating users with password: '$password'\n\n";

$created = 0;
$existing = 0;

foreach ($testUsers as $userData) {
    // Check if user exists
    $user = User::where('email', $userData['email'])->first();
    
    if ($user) {
        echo "  ⚠️  {$userData['email']} - Already exists (updating password)\n";
        $user->password = $hashedPassword;
        $user->save();
        $existing++;
    } else {
        User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'email_verified_at' => now(),
            'password' => $hashedPassword,
        ]);
        echo "  ✅ {$userData['email']} - Created\n";
        $created++;
    }
}

echo "\n";
echo "================================================================================\n";
echo "  📊 SUMMARY\n";
echo "================================================================================\n";
echo "Created:  $created users\n";
echo "Updated:  $existing users\n";
echo "Total:    " . ($created + $existing) . " test users ready\n";
echo "\n";
echo "Login credentials for all users:\n";
echo "  Email:    admin@test.com (or user1@test.com, user2@test.com, etc.)\n";
echo "  Password: password\n";
echo "\n";
echo "================================================================================\n";
echo "  ✅ READY FOR TESTING!\n";
echo "================================================================================\n";
echo "Next steps:\n";
echo "  1. Run: php concurrent-auth-test.php\n";
echo "  2. Expected: All logins should succeed\n";
echo "  3. Expected: Response times < 1000ms\n";
echo "================================================================================\n";