<?php 
require_once('connection.php');

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script> alert('Error: No data to save.'); location.replace('./') </script>";
    $conn->close();
    exit;
}

// Extract variables from POST data
extract($_POST);

// Validate required fields
if (empty($title) || empty($description) || empty($start_date) || empty($start_time) || empty($end_date) || empty($end_time)) {
    echo json_encode(["status" => "error", "message" => "Error: Missing required fields."]);
    $conn->close();
    exit;
}

// Check if it's an all-day event (optional field)
$allday = isset($allday) ? 1 : 0;

// Build the SQL query to insert the event with separate date and time columns
$sql = "INSERT INTO `events` 
            (`title`, `description`, `start_date`, `start_time`, `end_date`, `end_time`, `type`) 
        VALUES 
            ('$title', '$description', '$start_date', '$start_time', '$end_date', '$end_time', '$type')";

// Execute the query
$save = $conn->query($sql);

if ($save) {
    // Get the last inserted event ID
    $event_id = $conn->lastInsertId();

    // Return success response with event ID
    echo json_encode(["status" => "success", "message" => "Event Successfully Created.", "event_id" => $event_id]);
} else {
    // Return error response if the query failed
    echo json_encode(["status" => "error", "message" => "An Error occurred: " . $conn->errorInfo()[2]]);
}
 
// Close the connection
$conn = null;
?>
