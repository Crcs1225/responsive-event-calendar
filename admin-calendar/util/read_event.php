<?php
header('Content-Type: application/json');
require_once('connection.php');

// Fetch events from the updated table structure
$schedules = $conn->query("SELECT id, title, description, start_date, start_time, end_date, end_time, type, status FROM `events`");
$events = [];

if ($schedules) {
    $rows = $schedules->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        // Recombine date and time into ISO 8601 format: 'YYYY-MM-DDTHH:MM:SS'
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
            // Add additional fields if needed
        ];
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch events: " . $conn->errorInfo()[2]]);
    exit();
}

echo json_encode($events);
?>
