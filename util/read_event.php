<?php
header('Content-Type: application/json');
require_once('connection.php');

// Fetch events for the calendar
$schedules = $conn->query("SELECT id, title, description, start_datetime, end_datetime, type, status FROM `events`");
$events = [];

if ($schedules) {
    $rows = $schedules->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'start' => $row['start_datetime'], // FullCalendar expects 'start'
            'end' => $row['end_datetime'],     // FullCalendar expects 'end'
            'type' => $row['type'],  
            'status' => $row['status'],   
            // You can add more properties here if needed
        ];
    }
} else {
    // Handle query error
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch events: " . $conn->errorInfo()[2]]);
    exit();
}

echo json_encode($events);

?>