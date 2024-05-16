<?php
// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// Allow the Content-Type header
header("Access-Control-Allow-Headers: Content-Type");

// Set the response content type to JSON
header("Content-Type: application/json");

require_once "../config.php";

// Function to sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Log the $_POST array
error_log("Received POST data: " . print_r($_POST, true));

// Get form data
$courseId = sanitize_input($_POST["courseId"]);
$courseName = sanitize_input($_POST["courseName"]);
$courseDesc = sanitize_input($_POST["courseDesc"]);
$departmentName = sanitize_input($_POST["departmentName"]);

// Check if courseId is provided
if (empty($courseId)) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "courseId is required"]);
    exit();
}

// Prepare and bind statement
$stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_description = ?, department_name = ? WHERE course_id = ?");
$stmt->bind_param("sssi", $courseName, $courseDesc, $departmentName, $courseId);

// Execute the statement
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "Course Updated Successfully"]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Course not found or no changes made"]);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

// Close statement
$stmt->close();

// Close connection
$conn->close();
?>
