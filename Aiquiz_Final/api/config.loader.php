<?php
// Config Loader - Automatically loads local config if it exists, otherwise production config

$localConfigPath = __DIR__ . '/config.local.php';
$productionConfigPath = __DIR__ . '/config.php';

if (file_exists($localConfigPath)) {
    // Use local development config
    require_once $localConfigPath;
} elseif (file_exists($productionConfigPath)) {
    // Use production config
    require_once $productionConfigPath;
} else {
    die('Configuration file not found');
}
?>
