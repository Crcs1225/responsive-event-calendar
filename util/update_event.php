<?php 
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // Instead of alert and redirect, return a JSON response.
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

// Now you can validate
if (empty($event_id) || empty($title) || empty($start_datetime) || empty($end_datetime)) {
    echo json_encode(["status" => "error", "message" => "Error: Missing required fields."]);
    $conn = null;
    exit;
}

// Build the update query
$sql = "UPDATE `events` SET 
            `title` = '{$title}', 
            `description` = '{$description}', 
            `start_datetime` = '{$start_datetime}', 
            `end_datetime` = '{$end_datetime}',
            `type` = '{$type}',
            `status` = '{$status}'
        WHERE `id` = '{$event_id}'";

$save = $conn->query($sql);

if ($save) {
    // Return success response as JSON
    echo json_encode(["status" => "success", "message" => "Event Successfully Updated."]);
} else {
    // Return error response as JSON
    echo json_encode(["status" => "error", "message" => "An Error occurred: " . $conn->errorInfo()[2]]);
}

$conn = null;
?>
