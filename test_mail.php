<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw('Test email from CivicSeva', function($message) {
        $message->to('kumarpankaj3404@gmail.com')->subject('Test Email');
    });
    echo "Mail sent successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
