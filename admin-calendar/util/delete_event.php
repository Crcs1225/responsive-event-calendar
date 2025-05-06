<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // Return error response if not a POST request
    echo json_encode(["status" => "error", "message" => "Error: No data to delete."]);
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

// Validate the event ID
if (empty($event_id)) {
    echo json_encode(["status" => "error", "message" => "Error: Missing event ID."]);
    $conn = null;
    exit;
}

// Build the delete query
$sql = "DELETE FROM `events` WHERE `id` = '{$event_id}'";

$delete = $conn->query($sql);

if ($delete) {
    // Return success response as JSON
    echo json_encode(["status" => "success", "message" => "Event Successfully Deleted."]);
} else {
    // Return error response as JSON
    echo json_encode(["status" => "error", "message" => "An Error occurred: " . $conn->errorInfo()[2]]);
}

$conn = null;
?>
