<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// manage_devices.php
$action = $_POST['action'];
$device_id = $_POST['device_id'] ?? null;
$device_name = $_POST['device_name'] ?? null;
$device_ip = $_POST['device_ip'] ?? null;

$devices = file_exists('devices.txt') ? json_decode(file_get_contents('devices.txt'), true) : [];

switch ($action) {
    case 'add':
        $new_id = uniqid();
        $devices[$new_id] = ['name' => $device_name, 'ip' => $device_ip];
        break;
    case 'edit':
        if ($device_id && isset($devices[$device_id])) {
            $devices[$device_id] = ['name' => $device_name, 'ip' => $device_ip];
        }
        break;
    case 'delete':
        if ($device_id && isset($devices[$device_id])) {
            unset($devices[$device_id]);
        }
        break;
}

file_put_contents('devices.txt', json_encode($devices));
header('Location: index.php');
?>
