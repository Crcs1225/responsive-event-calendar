<?php 
require_once('connection.php');
if($_SERVER['REQUEST_METHOD'] !='POST'){
    echo "<script> alert('Error: No data to save.'); location.replace('./') </script>";
    $conn->close();
    exit;
}
extract($_POST);
$allday = isset($allday);


$sql = "INSERT INTO `events` (`title`,`description`,`start_datetime`,`end_datetime`, `type`,`status`) VALUES ('$title','$description','$start_datetime','$end_datetime', '$type','$status')";

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

$conn = null;
?>