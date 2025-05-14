<?php
header('Content-Type: application/json');

// Connect to the database
$conn = new mysqli("localhost", "root", "", "clinic_db");

// Check for connection error
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit();
}

// Sanitize and retrieve POST data
function sanitize($data) {
    return htmlspecialchars(trim($data));
}

$firstName        = sanitize($_POST['firstName'] ?? '');
$lastName         = sanitize($_POST['lastName'] ?? '');
$age              = intval($_POST['age'] ?? 0);
$gender           = sanitize($_POST['gender'] ?? '');
$phone            = sanitize($_POST['phone'] ?? '');
$email            = sanitize($_POST['email'] ?? '');
$appointmentDate  = sanitize($_POST['appointmentDate'] ?? '');
$appointmentTime  = sanitize($_POST['appointmentTime'] ?? '');
$service          = sanitize($_POST['service'] ?? '');
$otherService     = sanitize($_POST['otherService'] ?? '');
$reason           = sanitize($_POST['reason'] ?? '');

// Simple validation
if (!$firstName || !$lastName || !$age || !$gender || !$phone || !$email || !$appointmentDate || !$appointmentTime || !$service || !$reason) {
    echo json_encode([
        "status" => "error",
        "message" => "Please fill out all required fields."
    ]);
    exit();
}

// Insert query
$stmt = $conn->prepare("INSERT INTO appointment_table 
    (first_name, last_name, age, gender, phone, email, appointment_date, appointment_time, service, other_service, reason) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "SQL prepare failed: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("ssissssssss",
    $firstName, $lastName, $age, $gender, $phone, $email,
    $appointmentDate, $appointmentTime, $service, $otherService, $reason
);

// Execute and respond
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Appointment saved successfully!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to save appointment: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>