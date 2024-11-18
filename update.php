<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ip = $_GET['ip'];

// Function to ping IP address and get latency
function pingAddress($ip) {
    $pingresult = exec("/bin/ping -c 1 $ip", $outcome, $status);
    if ($status == 0) {
        preg_match('/time=([0-9]+)\.?[0-9]*/', implode("\n", $outcome), $matches);
        $latency = isset($matches[1]) ? $matches[1] : 'N/A';
        return ["<span style='color: green;'>Online</span>", $latency];
    } else {
        return ["<span style='color: red;'>Offline</span>", 'N/A'];
    }
}

$response = pingAddress($ip);
echo json_encode(['status' => $response[0], 'latency' => $response[1]]);
?>
