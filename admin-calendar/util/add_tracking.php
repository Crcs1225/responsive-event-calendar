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
if (empty($user_id) || empty($event_id)) {
    echo json_encode(["status" => "error", "message" => "Error: Missing required fields."]);
    $conn = null;
    exit;
}

// Build the insert query for the `event_tracking` table
$sql = "INSERT INTO `event_tracking` ( `event_id`) 
        VALUES ('{$event_id}')";

// Execute the query
$save = $conn->query($sql);

if ($save) {
    // Get the last inserted event ID
    $event_tracking_id = $conn->lastInsertId();

    // Return success response with event ID
    echo json_encode(["status" => "success", "message" => "Event Successfully Created.", "event_tracking_id" => $event_tracking_id]);
} else {
    // Return error response if the query failed
    echo json_encode(["status" => "error", "message" => "An Error occurred: " . $conn->errorInfo()[2]]);
}
 

$conn = null;
?>
