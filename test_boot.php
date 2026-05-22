<?php
echo "Autoloading...\n";
require __DIR__ . '/vendor/autoload.php';
echo "Autoload OK\n";

echo "Bootstrapping...\n";
$app = require_once __DIR__ . '/bootstrap/app.php';
echo "Bootstrap OK\n";

echo "Executing command...\n";
use Symfony\Component\Console\Input\ArgvInput;
$status = $app->handleCommand(new ArgvInput(['artisan', '--version']));
echo "Command executed with status: $status\n";
