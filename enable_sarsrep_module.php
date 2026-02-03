<?php
/**
 * SARS Representative Module - Enable Script
 *
 * Upload this file to your application root (same folder as artisan)
 * Then run: php enable_sarsrep_module.php
 *
 * This script:
 * 1. Adds SARSRepresentative to modules_statuses.json
 * 2. Clears all Laravel caches
 * 3. Creates the storage directory for document uploads
 * 4. Verifies the module files exist
 */

echo "=== SARS Representative Module Enable Script ===\n\n";

// Determine the base path
$basePath = __DIR__;
echo "Working directory: {$basePath}\n\n";

// Step 1: Update modules_statuses.json
echo "--- Step 1: Enabling module in modules_statuses.json ---\n";
$statusFile = $basePath . '/modules_statuses.json';

if (file_exists($statusFile)) {
    $content = file_get_contents($statusFile);
    $statuses = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "ERROR: Could not parse modules_statuses.json: " . json_last_error_msg() . "\n";
        echo "Current content:\n{$content}\n";
        exit(1);
    }

    echo "Current modules:\n";
    foreach ($statuses as $name => $enabled) {
        echo "  {$name}: " . ($enabled ? 'enabled' : 'disabled') . "\n";
    }

    if (isset($statuses['SARSRepresentative']) && $statuses['SARSRepresentative'] === true) {
        echo "\nSARSRepresentative is already enabled.\n";
    } else {
        $statuses['SARSRepresentative'] = true;
        $json = json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($statusFile, $json);
        echo "\nSARSRepresentative has been ENABLED.\n";
    }
} else {
    echo "modules_statuses.json not found at: {$statusFile}\n";
    echo "Creating it with SARSRepresentative enabled...\n";

    // Check if there's a different location
    $altPath = $basePath . '/bootstrap/cache/modules_statuses.json';
    if (file_exists($altPath)) {
        echo "Found at alternate location: {$altPath}\n";
        $content = file_get_contents($altPath);
        $statuses = json_decode($content, true) ?: [];
        $statuses['SARSRepresentative'] = true;
        file_put_contents($altPath, json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "Updated at alternate location.\n";
    } else {
        $statuses = ['SARSRepresentative' => true];
        file_put_contents($statusFile, json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "Created new modules_statuses.json\n";
    }
}

echo "\n--- Step 2: Verifying module files ---\n";
$modulePath = $basePath . '/Modules/SARSRepresentative';
$requiredFiles = [
    'module.json',
    'Http/Controllers/SarsRepController.php',
    'Models/SarsRepRequest.php',
    'Models/SarsRepresentative.php',
    'Models/SarsRepDocument.php',
    'Models/SarsRepAuditLog.php',
    'Providers/SARSRepresentativeServiceProvider.php',
    'Providers/RouteServiceProvider.php',
    'Routes/web.php',
    'Resources/views/sarsrep/index.blade.php',
    'Resources/views/sarsrep/show.blade.php',
    'Resources/views/sarsrep/create.blade.php',
    'Resources/views/templates/cover_letter.blade.php',
    'Resources/views/templates/mandate.blade.php',
    'Resources/views/templates/resolution.blade.php',
    'Resources/views/templates/bundle_index.blade.php',
];

$allPresent = true;
foreach ($requiredFiles as $file) {
    $fullPath = $modulePath . '/' . $file;
    $exists = file_exists($fullPath);
    echo "  " . ($exists ? "[OK]" : "[MISSING]") . " {$file}\n";
    if (!$exists) $allPresent = false;
}

if (!$allPresent) {
    echo "\nWARNING: Some module files are missing! The module may not work correctly.\n";
} else {
    echo "\nAll 16 module files present.\n";
}

echo "\n--- Step 3: Creating storage directory ---\n";
$storagePath = $basePath . '/storage/app/public/sars_rep_docs';
if (!is_dir($storagePath)) {
    if (mkdir($storagePath, 0775, true)) {
        echo "Created: {$storagePath}\n";
    } else {
        echo "ERROR: Could not create {$storagePath}\n";
        echo "Please create it manually and ensure it's writable.\n";
    }
} else {
    echo "Storage directory already exists: {$storagePath}\n";
}

echo "\n--- Step 4: Clearing caches ---\n";
// Clear compiled views
$viewCachePath = $basePath . '/storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*.php');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    echo "Cleared {$count} compiled view files.\n";
}

// Clear route cache
$routeCache = $basePath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "Cleared route cache.\n";
} else {
    echo "No route cache found (OK).\n";
}

// Clear config cache
$configCache = $basePath . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "Cleared config cache.\n";
} else {
    echo "No config cache found (OK).\n";
}

// Clear services cache
$servicesCache = $basePath . '/bootstrap/cache/services.php';
if (file_exists($servicesCache)) {
    unlink($servicesCache);
    echo "Cleared services cache.\n";
} else {
    echo "No services cache found (OK).\n";
}

// Clear packages cache
$packagesCache = $basePath . '/bootstrap/cache/packages.php';
if (file_exists($packagesCache)) {
    unlink($packagesCache);
    echo "Cleared packages cache.\n";
} else {
    echo "No packages cache found (OK).\n";
}

echo "\n=== DONE ===\n";
echo "Module should now be accessible at: /cims/sarsrep\n";
echo "If still getting 404, try running: php artisan optimize:clear\n";
echo "\nYou can delete this script after use.\n";
