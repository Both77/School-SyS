<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run migrations
try {
    $kernel->call('migrate', ['--force' => true]);
    echo "Migrations completed.\n";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}

// Run seeders
try {
    $kernel->call('db:seed', ['--force' => true]);
    echo "Seeders completed.\n";
} catch (Exception $e) {
    echo "Seeder error: " . $e->getMessage() . "\n";
}

echo "Done!";
?>