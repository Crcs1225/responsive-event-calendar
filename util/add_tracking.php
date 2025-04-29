<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // If the request method is not POST, return an error.
    echo json_encode(["status" => "error", "message" => "Error: No data to update."]);
    $conn = null;
    exit;
}

// Read JSON body if Content-Type is application/json
if (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input) {
        $_POST = $input;
    }
}

extract($_POST);

// Validate required fields
if (empty($user_id) || empty($event_id) || empty($office) || empty($status)) {
    echo json_encode(["status" => "error", "message" => "Error: Missing required fields."]);
    $conn = null;
    exit;
}

// Build the insert query for the `event_tracking` table
$sql = "INSERT INTO `event_tracking` (`from`, `event_id`, `to`, `status`) 
        VALUES ('{$user_id}', '{$event_id}', '{$office}', '{$status}')";

// Execute the query
$save = $conn->query($sql);

if (!$save) {
    // Output the error message directly
    echo json_encode(["status" => "error", "message" => "An error occurred: " . $conn->errorInfo()[2]]);
} else {
    echo json_encode(["status" => "success", "message" => "Tracking data added successfully."]);
}

$conn = null;
?>
