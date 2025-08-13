<?php

// Simple test script to verify health endpoints
echo "Testing Health Endpoints\n";
echo "=======================\n\n";

// Test basic health endpoint
echo "1. Testing basic health endpoint:\n";
$basic_url = 'http://127.0.0.1:8001/api/health';
$basic_response = file_get_contents($basic_url);
echo "URL: $basic_url\n";
echo 'Response: '.($basic_response ? '✅ Success' : '❌ Failed')."\n";
if ($basic_response) {
    $basic_data = json_decode($basic_response, true);
    echo 'Status: '.($basic_data['status'] ?? 'Unknown')."\n";
    echo 'Service: '.($basic_data['service'] ?? 'Unknown')."\n";
}
echo "\n";

// Test detailed health endpoint
echo "2. Testing detailed health endpoint:\n";
$detailed_url = 'http://127.0.0.1:8001/api/health/detailed';
$detailed_response = file_get_contents($detailed_url);
echo "URL: $detailed_url\n";
echo 'Response: '.($detailed_response ? '✅ Success' : '❌ Failed')."\n";
if ($detailed_response) {
    $detailed_data = json_decode($detailed_response, true);
    echo 'Status: '.($detailed_data['status'] ?? 'Unknown')."\n";
    echo 'Checks count: '.(count($detailed_data['checks'] ?? []))."\n";
    echo 'Response time: '.($detailed_data['response_time_ms'] ?? 'Unknown')."ms\n";

    // Check individual services
    if (isset($detailed_data['checks'])) {
        echo "Service checks:\n";
        foreach ($detailed_data['checks'] as $service => $check) {
            $status = $check['healthy'] ? '✅' : '❌';
            echo "  $service: $status ".($check['status'] ?? 'Unknown')."\n";
        }
    }
}
echo "\n";

echo "Health check test completed!\n";
