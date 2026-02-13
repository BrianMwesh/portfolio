<?php
require_once 'config.php';
header('Content-Type: application/json');

$conn = getDBConnection();

if (isset($_GET['id'])) {
    $project_id = intval($_GET['id']);
    
    $query = "SELECT * FROM projects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
        echo json_encode($project);
    } else {
        echo json_encode(['error' => 'Project not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No project ID provided']);
}

$conn->close();
?>