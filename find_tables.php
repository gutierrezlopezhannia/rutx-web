<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::table('RDB$RELATIONS')
    ->whereNull('RDB$VIEW_BLR')
    ->where('RDB$SYSTEM_FLAG', 0)
    ->pluck('RDB$RELATION_NAME');
    
echo json_encode($tables);
