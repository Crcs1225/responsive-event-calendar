<?php
include 'connection.php';
header('Content-Type: application/json');

try {
    $sql = "SELECT id, name, department, position FROM applicants"; // Replace table/fields as needed
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($applicants);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>