<?php 
require_once('connection.php');

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
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

// Extract variables from POST data
extract($_POST);

// Validate required fields
if (empty($event_id) || empty($title) || empty($start_date) || empty($start_time) || empty($end_date) || empty($end_time)) {
    echo json_encode(["status" => "error", "message" => "Error: Missing required fields."]);
    $conn = null;
    exit;
}

// Build the update query
$sql = "UPDATE `events` SET 
            `title` = '{$title}', 
            `description` = '{$description}', 
            `start_date` = '{$start_date}', 
            `start_time` = '{$start_time}',
            `end_date` = '{$end_date}',
            `end_time` = '{$end_time}',
            `type` = '{$type}'
        WHERE `id` = '{$event_id}'";

// Execute the query
$save = $conn->query($sql);

if ($save) {
    // Return success response as JSON
    echo json_encode(["status" => "success", "message" => "Event Successfully Updated."]);
} else {
    // Return error response as JSON
    echo json_encode(["status" => "error", "message" => "An error occurred: " . $conn->errorInfo()[2]]);
}

$conn = null;
?>
