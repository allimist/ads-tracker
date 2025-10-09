<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");

// Database config
require_once __DIR__ . '/../../env.php';


// Connect to MySQL
$mysqli = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set charset
$mysqli->set_charset("utf8mb4");

// Get all GET parameters
$inputParams = $_GET;

// Add default date if not provided
if (!isset($inputParams['date'])) {
    $inputParams['date'] = date('Y-m-d H:i:s');
}

// ✅ Add default values if missing
if (!isset($inputParams['record_source'])) {
    $inputParams['record_source'] = 'postback';
}

if (!isset($inputParams['event_type'])) {
    $inputParams['event_type'] = 'converssion'; // confirm if spelling is correct
}

// Get actual column names from the `pc` table
$columnResult = $mysqli->query("SHOW COLUMNS FROM `pc`");
if (!$columnResult) {
    die("Failed to get table columns: " . $mysqli->error);
}

$validColumns = [];
while ($col = $columnResult->fetch_assoc()) {
    $validColumns[] = $col['Field'];
}

// Filter GET params to only include columns that exist in the table
$dataToInsert = [];
foreach ($inputParams as $key => $value) {
    if (in_array($key, $validColumns)) {
        $dataToInsert[$key] = $value;
    }
}

// Remove `id` if it's in there (assuming it's auto-increment)
unset($dataToInsert['id']);

if (empty($dataToInsert)) {
    die("No valid columns to insert.");
}

// Build query dynamically
$columns = implode('`, `', array_keys($dataToInsert));
$placeholders = implode(', ', array_fill(0, count($dataToInsert), '?'));
$types = str_repeat('s', count($dataToInsert)); // assume all values are strings

$sql = "INSERT INTO `pc` (`$columns`) VALUES ($placeholders)";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

// Bind parameters dynamically
$stmt->bind_param($types, ...array_values($dataToInsert));

// Execute and check result
if ($stmt->execute()) {
    echo "✅ Inserted successfully.";
} else {
    echo "❌ Insert failed: " . $stmt->error;
}

$stmt->close();
$mysqli->close();

?>
