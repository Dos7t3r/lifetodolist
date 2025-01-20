<?php
header('Content-Type: application/json; charset=utf-8');

function readJsonFile() {
    $jsonFile = __DIR__ . '/data/life_events.json';
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        return json_decode($jsonContent, true) ?? [];
    }
    return [];
}

if (isset($_GET['id'])) {
    $eventId = (int)$_GET['id'];
    $events = readJsonFile();
    
    $event = array_filter($events, function($e) use ($eventId) {
        return $e['id'] == $eventId;
    });

    if (!empty($event)) {
        echo json_encode(reset($event), JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['error' => '未找到该事项']);
    }
} else {
    echo json_encode(['error' => '未提供ID参数']);
}