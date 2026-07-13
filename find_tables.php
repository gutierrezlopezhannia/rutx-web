<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$equiv = DB::table('EQUIVALENCIAS_MONEDAS')->take(5)->get();
echo json_encode($equiv, JSON_PRETTY_PRINT);
