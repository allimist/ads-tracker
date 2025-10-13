<?php
require_once __DIR__ . '/../../../env.php';

// Connect to MySQL
$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

// Check password
if (!isset($_GET['code'])) {
    die('No code provided.');
}
$inputCode = $_GET['code'];
$codeQuery = "SELECT `value` FROM `globals` WHERE `name` = 'report_pass' LIMIT 1";
$codeResult = $mysqli->query($codeQuery);
if (!$codeResult || $codeResult->num_rows === 0) {
    die('report_pass not found.');
}
$reportPass = $codeResult->fetch_assoc()['value'];
if ($inputCode !== $reportPass) {
    die('Access denied.');
}

// Set the table name
$tableName = 'pc';

// Query all data from the table
$query = "SELECT * FROM `$tableName`";
$result = $mysqli->query($query);
if (!$result) {
    die("Query failed: " . $mysqli->error);
}

// Set headers to trigger file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $tableName . '_export.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Output column headers
$fields = $result->fetch_fields();
$headers = array_map(fn($f) => $f->name, $fields);
fputcsv($output, $headers);

// Output data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$mysqli->close();
exit;