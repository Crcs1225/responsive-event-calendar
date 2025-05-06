<?php
require_once('connection.php');
session_start();

header('Content-Type: application/json');

$user_id = 4;// for testing

if (!$user_id) {
    echo json_encode([]);
    exit;
}

// Step 1: Get event_tracking_ids from calendar_tracking
$sql1 = "SELECT event_tracking_id FROM calendar_tracking WHERE unique_id = ?";
$stmt1 = $conn->prepare($sql1);
$stmt1->execute([$user_id]);
$eventTrackingIds = $stmt1->fetchAll(PDO::FETCH_COLUMN);

if (empty($eventTrackingIds)) {
    echo json_encode([]);
    exit;
}

// Step 2: Get event_ids from event_tracking
$inClause2 = implode(',', array_fill(0, count($eventTrackingIds), '?'));
$sql2 = "SELECT event_id FROM event_tracking WHERE id IN ($inClause2)";
$stmt2 = $conn->prepare($sql2);
$stmt2->execute($eventTrackingIds);
$eventIds = $stmt2->fetchAll(PDO::FETCH_COLUMN);

if (empty($eventIds)) {
    echo json_encode([]);
    exit;
}

// Step 3: Get event details from events table
$inClause3 = implode(',', array_fill(0, count($eventIds), '?'));
$sql3 = "SELECT * FROM events WHERE id IN ($inClause3)";
$stmt3 = $conn->prepare($sql3);
$stmt3->execute($eventIds);

$events = []; // Ensure it's declared
while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
    $start = $row['start_date'] . 'T' . $row['start_time'];
    $end = $row['end_date'] . 'T' . $row['end_time'];

    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'start' => $start,
        'end' => $end,
        'type' => $row['type'],
        'status' => $row['status'],
    ];
}

// Return clean JSON
$json = json_encode($events);

if ($json === false) {
    http_response_code(500);
    echo json_encode(['error' => 'JSON encoding failed: ' . json_last_error_msg()]);
    exit;
}

// Optional: Log to file for debugging
file_put_contents(__DIR__ . '/debug_output.log', $json . PHP_EOL);

echo $json;
?>
